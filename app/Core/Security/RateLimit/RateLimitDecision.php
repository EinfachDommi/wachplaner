<?php

declare(strict_types=1);

namespace Wachplaner\Core\Security\RateLimit;

use DateTimeImmutable;

final class RateLimitDecision
{
    private function __construct(
        public readonly bool $allowed,
        public readonly int $remaining,
        public readonly int $retryAfterSeconds,
        public readonly ?DateTimeImmutable $blockedUntil,
        public readonly bool $newlyBlocked = false
    ) {
    }

    public static function allowed(int $remaining): self
    {
        return new self(true, max(0, $remaining), 0, null);
    }

    public static function blocked(
        int $retryAfterSeconds,
        DateTimeImmutable $blockedUntil,
        bool $newlyBlocked = false
    ): self {
        return new self(
            false,
            0,
            max(1, $retryAfterSeconds),
            $blockedUntil,
            $newlyBlocked
        );
    }
}
