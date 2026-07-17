<?php

declare(strict_types=1);

namespace Wachplaner\Core\Security\Forms;

final class FormToken
{
    public function __construct(
        public readonly string $token,
        public readonly string $nonce,
        public readonly int $issuedAt
    ) {
    }
}
