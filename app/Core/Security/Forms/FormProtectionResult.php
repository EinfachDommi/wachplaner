<?php

declare(strict_types=1);

namespace Wachplaner\Core\Security\Forms;

final class FormProtectionResult
{
    private function __construct(
        public readonly bool $valid,
        public readonly string $reason
    ) {
    }

    public static function valid(): self
    {
        return new self(true, 'ok');
    }

    public static function invalid(string $reason): self
    {
        return new self(false, $reason);
    }
}
