<?php

declare(strict_types=1);

namespace Wachplaner\Core\System;

final class MaintenanceState
{
    public function __construct(
        public readonly bool $active,
        public readonly string $mode,
        public readonly string $reason,
        public readonly string $message,
        public readonly ?string $detectedAt = null,
        public readonly ?string $lastCheckAt = null,
        public readonly ?string $retryAt = null
    ) {
    }

    public static function online(): self
    {
        return new self(
            active: false,
            mode: 'online',
            reason: 'none',
            message: ''
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            active: (bool) ($data['active'] ?? false),
            mode: (string) ($data['mode'] ?? 'online'),
            reason: (string) ($data['reason'] ?? 'none'),
            message: (string) ($data['message'] ?? ''),
            detectedAt: isset($data['detected_at']) ? (string) $data['detected_at'] : null,
            lastCheckAt: isset($data['last_check_at']) ? (string) $data['last_check_at'] : null,
            retryAt: isset($data['retry_at']) ? (string) $data['retry_at'] : null
        );
    }

    public function toArray(): array
    {
        return [
            'active' => $this->active,
            'mode' => $this->mode,
            'reason' => $this->reason,
            'message' => $this->message,
            'detected_at' => $this->detectedAt,
            'last_check_at' => $this->lastCheckAt,
            'retry_at' => $this->retryAt,
        ];
    }

    public function isAutomatic(): bool
    {
        return $this->active && $this->mode === 'automatic';
    }

    public function isForced(): bool
    {
        return $this->active && $this->mode === 'forced';
    }

    public function isManual(): bool
    {
        return $this->active && $this->mode === 'manual';
    }
}
