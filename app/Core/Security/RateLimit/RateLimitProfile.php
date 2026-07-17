<?php

declare(strict_types=1);

namespace Wachplaner\Core\Security\RateLimit;

use InvalidArgumentException;

final class RateLimitProfile
{
    public function __construct(
        public readonly int $maxAttempts,
        public readonly int $windowSeconds,
        public readonly int $blockSeconds,
        public readonly int $escalationFactor = 2,
        public readonly int $maxBlockSeconds = 86400
    ) {
        if ($maxAttempts < 1) {
            throw new InvalidArgumentException('maxAttempts muss mindestens 1 sein.');
        }

        if ($windowSeconds < 1 || $blockSeconds < 1) {
            throw new InvalidArgumentException('Zeitwerte müssen größer als 0 sein.');
        }

        if ($escalationFactor < 1 || $maxBlockSeconds < $blockSeconds) {
            throw new InvalidArgumentException('Ungültige Eskalationskonfiguration.');
        }
    }

    public function escalatedBlockSeconds(int $previousBlockCount): int
    {
        $previousBlockCount = max(0, $previousBlockCount);
        $seconds = $this->blockSeconds;

        for ($i = 0; $i < $previousBlockCount; $i++) {
            $seconds *= $this->escalationFactor;

            if ($seconds >= $this->maxBlockSeconds) {
                return $this->maxBlockSeconds;
            }
        }

        return min($seconds, $this->maxBlockSeconds);
    }
}
