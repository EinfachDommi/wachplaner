<?php

declare(strict_types=1);

namespace Wachplaner\Core\Security\Registration;

use Wachplaner\Core\Settings\SettingsService;

final class RegistrationPolicy
{
    public const MODE_DISABLED = 'disabled';
    public const MODE_OPEN = 'open';
    public const MODE_INVITE_ONLY = 'invite_only';

    private const ALLOWED_MODES = [
        self::MODE_DISABLED,
        self::MODE_OPEN,
        self::MODE_INVITE_ONLY,
    ];

    public function __construct(
        private readonly SettingsService $settings
    ) {
    }

    public function mode(bool $maintenanceActive = false): string
    {
        if ($maintenanceActive) {
            return self::MODE_DISABLED;
        }

        $mode = (string) $this->settings->get(
            'security.registration_mode',
            self::MODE_DISABLED
        );

        return in_array($mode, self::ALLOWED_MODES, true)
            ? $mode
            : self::MODE_DISABLED;
    }

    public function isEnabled(bool $maintenanceActive = false): bool
    {
        return $this->mode($maintenanceActive) !== self::MODE_DISABLED;
    }

    public function requiresInvite(bool $maintenanceActive = false): bool
    {
        return $this->mode($maintenanceActive) === self::MODE_INVITE_ONLY;
    }

    public function setMode(string $mode): void
    {
        if (!in_array($mode, self::ALLOWED_MODES, true)) {
            throw new \InvalidArgumentException('Ungültiger Registrierungsmodus.');
        }

        $this->settings->set('security.registration_mode', $mode);
    }
}
