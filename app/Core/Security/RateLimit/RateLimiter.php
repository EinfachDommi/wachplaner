<?php

declare(strict_types=1);

namespace Wachplaner\Core\Security\RateLimit;

use DateTimeImmutable;
use DateTimeZone;

final class RateLimiter
{
    public function __construct(
        private readonly RateLimitRepository $repository,
        private readonly DateTimeZone $timezone = new DateTimeZone('UTC')
    ) {
    }

    public function attempt(
        string $scope,
        string $subjectHash,
        RateLimitProfile $profile
    ): RateLimitDecision {
        if (!RateLimitScope::isValid($scope)) {
            throw new \InvalidArgumentException('Unbekannter Rate-Limit-Scope.');
        }

        return $this->repository->transaction(
            function () use ($scope, $subjectHash, $profile): RateLimitDecision {
                $now = new DateTimeImmutable('now', $this->timezone);
                $row = $this->repository->findForUpdate($scope, $subjectHash);

                if ($row === null) {
                    $this->repository->create($scope, $subjectHash, $now);

                    return RateLimitDecision::allowed($profile->maxAttempts - 1);
                }

                $blockedUntil = $this->parseDate($row['blocked_until'] ?? null);

                if ($blockedUntil !== null && $blockedUntil > $now) {
                    return RateLimitDecision::blocked(
                        $blockedUntil->getTimestamp() - $now->getTimestamp(),
                        $blockedUntil
                    );
                }

                $windowStartedAt = $this->parseDate(
                    $row['window_started_at'] ?? null
                ) ?? $now;

                $windowExpired = (
                    $now->getTimestamp() - $windowStartedAt->getTimestamp()
                ) >= $profile->windowSeconds;

                $attemptCount = $windowExpired
                    ? 1
                    : ((int) ($row['attempt_count'] ?? 0)) + 1;

                $blockCount = (int) ($row['block_count'] ?? 0);
                $newWindowStartedAt = $windowExpired ? $now : $windowStartedAt;

                if ($attemptCount > $profile->maxAttempts) {
                    $blockSeconds = $profile->escalatedBlockSeconds($blockCount);
                    $newBlockedUntil = $now->modify('+' . $blockSeconds . ' seconds');

                    $this->repository->updateWindow(
                        (int) $row['id'],
                        $attemptCount,
                        $blockCount + 1,
                        $newWindowStartedAt,
                        $newBlockedUntil,
                        $now
                    );

                    return RateLimitDecision::blocked(
                        $blockSeconds,
                        $newBlockedUntil,
                        true
                    );
                }

                $this->repository->updateWindow(
                    (int) $row['id'],
                    $attemptCount,
                    $blockCount,
                    $newWindowStartedAt,
                    null,
                    $now
                );

                return RateLimitDecision::allowed(
                    $profile->maxAttempts - $attemptCount
                );
            }
        );
    }

    public function clear(string $scope, string $subjectHash): void
    {
        $this->repository->clear($scope, $subjectHash);
    }

    public function cleanup(int $retentionDays = 7): int
    {
        $retentionDays = max(1, min($retentionDays, 365));

        return $this->repository->cleanup(
            new DateTimeImmutable(
                '-' . $retentionDays . ' days',
                $this->timezone
            )
        );
    }

    private function parseDate(mixed $value): ?DateTimeImmutable
    {
        if (!is_string($value) || trim($value) === '') {
            return null;
        }

        return new DateTimeImmutable($value, $this->timezone);
    }
}
