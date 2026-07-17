<?php

declare(strict_types=1);

namespace Wachplaner\Core\Security\RateLimit;

use DateTimeImmutable;
use PDO;
use Throwable;

final class RateLimitRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findForUpdate(string $scope, string $subjectHash): ?array
    {
        $statement = $this->pdo->prepare(
            <<<'SQL'
            SELECT
                id,
                scope,
                subject_hash,
                attempt_count,
                block_count,
                window_started_at,
                blocked_until,
                last_attempt_at,
                created_at,
                updated_at
            FROM security_rate_limits
            WHERE scope = :scope
              AND subject_hash = :subject_hash
            LIMIT 1
            FOR UPDATE
            SQL
        );

        $statement->execute([
            'scope' => $scope,
            'subject_hash' => $subjectHash,
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return is_array($row) ? $row : null;
    }

    public function create(
        string $scope,
        string $subjectHash,
        DateTimeImmutable $now
    ): void {
        $statement = $this->pdo->prepare(
            <<<'SQL'
            INSERT INTO security_rate_limits (
                scope,
                subject_hash,
                attempt_count,
                block_count,
                window_started_at,
                blocked_until,
                last_attempt_at,
                created_at,
                updated_at
            ) VALUES (
                :scope,
                :subject_hash,
                1,
                0,
                :window_started_at,
                NULL,
                :last_attempt_at,
                :created_at,
                :updated_at
            )
            SQL
        );

        $timestamp = $now->format('Y-m-d H:i:s');

        $statement->execute([
            'scope' => $scope,
            'subject_hash' => $subjectHash,
            'window_started_at' => $timestamp,
            'last_attempt_at' => $timestamp,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
    }

    public function updateWindow(
        int $id,
        int $attemptCount,
        int $blockCount,
        DateTimeImmutable $windowStartedAt,
        ?DateTimeImmutable $blockedUntil,
        DateTimeImmutable $now
    ): void {
        $statement = $this->pdo->prepare(
            <<<'SQL'
            UPDATE security_rate_limits
            SET
                attempt_count = :attempt_count,
                block_count = :block_count,
                window_started_at = :window_started_at,
                blocked_until = :blocked_until,
                last_attempt_at = :last_attempt_at,
                updated_at = :updated_at
            WHERE id = :id
            SQL
        );

        $statement->execute([
            'attempt_count' => $attemptCount,
            'block_count' => $blockCount,
            'window_started_at' => $windowStartedAt->format('Y-m-d H:i:s'),
            'blocked_until' => $blockedUntil?->format('Y-m-d H:i:s'),
            'last_attempt_at' => $now->format('Y-m-d H:i:s'),
            'updated_at' => $now->format('Y-m-d H:i:s'),
            'id' => $id,
        ]);
    }

    public function clear(string $scope, string $subjectHash): void
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM security_rate_limits
             WHERE scope = :scope AND subject_hash = :subject_hash'
        );

        $statement->execute([
            'scope' => $scope,
            'subject_hash' => $subjectHash,
        ]);
    }

    public function cleanup(DateTimeImmutable $olderThan): int
    {
        $statement = $this->pdo->prepare(
            <<<'SQL'
            DELETE FROM security_rate_limits
            WHERE updated_at < :older_than
              AND (blocked_until IS NULL OR blocked_until < NOW())
            SQL
        );

        $statement->execute([
            'older_than' => $olderThan->format('Y-m-d H:i:s'),
        ]);

        return $statement->rowCount();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function activeBlocks(int $limit = 100): array
    {
        $limit = max(1, min($limit, 500));

        $statement = $this->pdo->prepare(
            <<<'SQL'
            SELECT
                id,
                scope,
                attempt_count,
                block_count,
                blocked_until,
                last_attempt_at
            FROM security_rate_limits
            WHERE blocked_until IS NOT NULL
              AND blocked_until > NOW()
            ORDER BY blocked_until DESC
            LIMIT :limit
            SQL
        );

        $statement->bindValue('limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return is_array($rows) ? $rows : [];
    }

    public function transaction(callable $callback): mixed
    {
        $ownsTransaction = !$this->pdo->inTransaction();

        if ($ownsTransaction) {
            $this->pdo->beginTransaction();
        }

        try {
            $result = $callback();

            if ($ownsTransaction && $this->pdo->inTransaction()) {
                $this->pdo->commit();
            }

            return $result;
        } catch (Throwable $exception) {
            if ($ownsTransaction && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $exception;
        }
    }
}
