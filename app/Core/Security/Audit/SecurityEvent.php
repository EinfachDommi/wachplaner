<?php

declare(strict_types=1);

namespace Wachplaner\Core\Security\Audit;

final class SecurityEvent
{
    /**
     * @param array<string, scalar|null> $metadata
     */
    public function __construct(
        public readonly string $eventType,
        public readonly string $severity,
        public readonly string $result,
        public readonly ?string $subjectHash = null,
        public readonly array $metadata = []
    ) {
    }

    public static function info(
        string $eventType,
        string $result,
        ?string $subjectHash = null,
        array $metadata = []
    ): self {
        return new self($eventType, 'info', $result, $subjectHash, $metadata);
    }

    public static function warning(
        string $eventType,
        string $result,
        ?string $subjectHash = null,
        array $metadata = []
    ): self {
        return new self($eventType, 'warning', $result, $subjectHash, $metadata);
    }

    public static function critical(
        string $eventType,
        string $result,
        ?string $subjectHash = null,
        array $metadata = []
    ): self {
        return new self($eventType, 'critical', $result, $subjectHash, $metadata);
    }
}
