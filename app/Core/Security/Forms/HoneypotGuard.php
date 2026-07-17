<?php

declare(strict_types=1);

namespace Wachplaner\Core\Security\Forms;

final class HoneypotGuard
{
    public function __construct(
        private readonly string $fieldName = 'website'
    ) {
    }

    public function fieldName(): string
    {
        return $this->fieldName;
    }

    /**
     * @param array<string, mixed> $input
     */
    public function validate(array $input): FormProtectionResult
    {
        $value = $input[$this->fieldName] ?? '';

        if (is_array($value) || is_object($value)) {
            return FormProtectionResult::invalid('honeypot_invalid_type');
        }

        if (trim((string) $value) !== '') {
            return FormProtectionResult::invalid('honeypot_filled');
        }

        return FormProtectionResult::valid();
    }

    public function renderField(): string
    {
        $name = htmlspecialchars($this->fieldName, ENT_QUOTES, 'UTF-8');

        return sprintf(
            '<div aria-hidden="true" style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden;">'
            . '<label for="%1$s">Website</label>'
            . '<input type="text" id="%1$s" name="%1$s" value="" tabindex="-1" autocomplete="off">'
            . '</div>',
            $name
        );
    }
}
