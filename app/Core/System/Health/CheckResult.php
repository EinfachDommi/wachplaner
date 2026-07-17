<?php

declare(strict_types=1);

namespace Wachplaner\Core\System\Health;

final class CheckResult
{
    /**
     * @param array<string, scalar|null> $meta
     */
    public function __construct(
        public readonly string $key,
        public readonly string $label,
        public readonly string $value,
        public readonly string $level,
        public readonly bool $critical = false,
        public readonly array $meta = []
    ) {
    }

    public static function ok(
        string $key,
        string $label,
        string $value,
        bool $critical = false,
        array $meta = []
    ): self {
        return new self($key, $label, $value, 'ok', $critical, $meta);
    }

    public static function warning(
        string $key,
        string $label,
        string $value,
        bool $critical = false,
        array $meta = []
    ): self {
        return new self($key, $label, $value, 'warning', $critical, $meta);
    }

    public static function error(
        string $key,
        string $label,
        string $value,
        bool $critical = true,
        array $meta = []
    ): self {
        return new self($key, $label, $value, 'error', $critical, $meta);
    }

    public function isOk(): bool
    {
        return $this->level === 'ok';
    }

    public function isCriticalFailure(): bool
    {
        return $this->critical && !$this->isOk();
    }

    /**
     * Backward-compatible representation for existing AdminLTE views.
     *
     * @return array{key:string,label:string,value:string,ok:bool,level:string,critical:bool,meta:array<string, scalar|null>}
     */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'value' => $this->value,
            'ok' => $this->isOk(),
            'level' => $this->level,
            'critical' => $this->critical,
            'meta' => $this->meta,
        ];
    }
}
