<?php

declare(strict_types=1);

namespace Wachplaner\Core\Security\Forms;

final class FormProtectionService
{
    public function __construct(
        private readonly HoneypotGuard $honeypot,
        private readonly FormTokenService $tokens
    ) {
    }

    public function issue(string $formId): FormToken
    {
        return $this->tokens->issue($formId);
    }

    public function honeypotField(): string
    {
        return $this->honeypot->renderField();
    }

    /**
     * @param array<string, mixed> $input
     */
    public function validate(
        string $formId,
        array $input,
        ?string $token
    ): FormProtectionResult {
        $honeypotResult = $this->honeypot->validate($input);

        if (!$honeypotResult->valid) {
            return $honeypotResult;
        }

        if ($token === null || trim($token) === '') {
            return FormProtectionResult::invalid('form_token_missing');
        }

        return $this->tokens->validateAndConsume($formId, $token);
    }
}
