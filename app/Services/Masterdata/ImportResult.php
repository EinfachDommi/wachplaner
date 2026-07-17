<?php

declare(strict_types=1);

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

    public function hasWarnings(): bool
    {
        return $this->warnings !== [] || $this->skipped > 0;
    }

    public function status(): string
    {
        if (!$this->success()) {
            return 'error';
        }

        if ($this->hasWarnings()) {
            return 'warning';
        }

        return 'success';
    }

    public function addProcessed(int $count = 1): void
    {
        $this->processed += max(0, $count);
    }

    public function addCreated(int $count = 1): void
    {
        $this->created += max(0, $count);
    }

    public function addUpdated(int $count = 1): void
    {
        $this->updated += max(0, $count);
    }

    public function addSkipped(int $count = 1): void
    {
        $this->skipped += max(0, $count);
    }

    public function addError(string $message): void
    {
        $message = trim($message);
        if ($message !== '') {
            $this->errors[] = $message;
        }
    }

    public function addWarning(string $message): void
    {
        $message = trim($message);
        if ($message !== '') {
            $this->warnings[] = $message;
        }
    }
}
