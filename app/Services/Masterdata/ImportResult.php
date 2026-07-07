<?php

namespace Wachplaner\Services\Masterdata;

final class ImportResult
{
    public function __construct(
        public string $type,
        public int $processed = 0,
        public int $created = 0,
        public int $updated = 0,
        public int $skipped = 0,
        public array $errors = [],
        public array $warnings = []
    ) {
    }

    public function success(): bool
    {
        return $this->errors === [];
    }

    public function addError(string $message): void
    {
        $this->errors[] = $message;
    }

    public function addWarning(string $message): void
    {
        $this->warnings[] = $message;
    }
}
