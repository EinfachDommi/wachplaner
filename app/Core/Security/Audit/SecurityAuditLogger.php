<?php

declare(strict_types=1);

namespace Wachplaner\Core\Security\Audit;

use JsonException;
use PDO;
use Throwable;
use Wachplaner\Core\Security\SecurityContext;
use Wachplaner\Services\Logging\Logger;

final class SecurityAuditLogger
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly Logger $logger
    ) {
    }

    public function log(SecurityEvent $event, SecurityContext $context): void
    {
        $metadata = $this->sanitizeMetadata($event->metadata);

        try {
            $statement = $this->pdo->prepare(
                <<<'SQL'
                INSERT INTO security_audit_logs (
                    event_type,
                    severity,
                    result,
                    request_id,
                    user_id,
                    subject_hash,
                    ip_prefix_hash,
                    user_agent_hash,
                    metadata_json,
                    created_at
                ) VALUES (
                    :event_type,
                    :severity,
                    :result,
                    :request_id,
                    :user_id,
                    :subject_hash,
                    :ip_prefix_hash,
                    :user_agent_hash,
                    :metadata_json,
                    NOW()
                )
                SQL
            );

            $statement->execute([
                'event_type' => $event->eventType,
                'severity' => $event->severity,
                'result' => $event->result,
                'request_id' => $context->requestId,
                'user_id' => $context->userId,
                'subject_hash' => $event->subjectHash,
                'ip_prefix_hash' => $context->ipPrefixHash,
                'user_agent_hash' => $context->userAgentHash,
                'metadata_json' => json_encode(
                    $metadata,
                    JSON_THROW_ON_ERROR
                    | JSON_UNESCAPED_UNICODE
                    | JSON_UNESCAPED_SLASHES
                ),
            ]);
        } catch (Throwable $exception) {
            $this->writeFallbackLog($event, $context, $exception);
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function latest(int $limit = 50): array
    {
        $limit = max(1, min($limit, 200));

        $statement = $this->pdo->prepare(
            <<<'SQL'
            SELECT
                id,
                event_type,
                severity,
                result,
                request_id,
                user_id,
                created_at
            FROM security_audit_logs
            ORDER BY created_at DESC, id DESC
            LIMIT :limit
            SQL
        );

        $statement->bindValue('limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return is_array($rows) ? $rows : [];
    }

    /**
     * @param array<string, scalar|null> $metadata
     * @return array<string, scalar|null>
     */
    private function sanitizeMetadata(array $metadata): array
    {
        $blockedKeys = [
            'password',
            'passwort',
            'token',
            'secret',
            'authorization',
            'cookie',
            'csrf',
            'session',
        ];

        $sanitized = [];

        foreach ($metadata as $key => $value) {
            $normalizedKey = mb_strtolower((string) $key, 'UTF-8');

            $containsBlockedKey = false;

            foreach ($blockedKeys as $blockedKey) {
                if (str_contains($normalizedKey, $blockedKey)) {
                    $containsBlockedKey = true;
                    break;
                }
            }

            if ($containsBlockedKey) {
                $sanitized[$key] = '[REDACTED]';
                continue;
            }

            if (is_string($value)) {
                $sanitized[$key] = mb_substr($value, 0, 500, 'UTF-8');
                continue;
            }

            $sanitized[$key] = $value;
        }

        return $sanitized;
    }

    private function writeFallbackLog(
        SecurityEvent $event,
        SecurityContext $context,
        Throwable $exception
    ): void {
        try {
            $this->logger->error('Security audit persistence failed', [
                'event_type' => $event->eventType,
                'severity' => $event->severity,
                'result' => $event->result,
                'request_id' => $context->requestId,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
        } catch (Throwable) {
            // Security audit logging must not interrupt the request.
        }
    }
}
