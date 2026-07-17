<?php

declare(strict_types=1);

namespace Wachplaner\Core\Security\RateLimit;

use Wachplaner\Core\Settings\SettingsService;

final class RateLimitProfiles
{
    public function __construct(
        private readonly SettingsService $settings
    ) {
    }

    public function login(): RateLimitProfile
    {
        return new RateLimitProfile(
            $this->int('security.login.max_attempts', 10, 1, 100),
            $this->int('security.login.window_seconds', 900, 60, 86400),
            $this->int('security.login.block_seconds', 300, 30, 86400),
            $this->int('security.login.escalation_factor', 2, 1, 10),
            $this->int('security.login.max_block_seconds', 86400, 300, 604800)
        );
    }

    public function registration(): RateLimitProfile
    {
        return new RateLimitProfile(
            $this->int('security.register.max_attempts', 3, 1, 50),
            $this->int('security.register.window_seconds', 3600, 60, 86400),
            $this->int('security.register.block_seconds', 3600, 60, 86400),
            $this->int('security.register.escalation_factor', 2, 1, 10),
            $this->int('security.register.max_block_seconds', 86400, 300, 604800)
        );
    }

    private function int(
        string $key,
        int $default,
        int $minimum,
        int $maximum
    ): int {
        $value = (int) $this->settings->get($key, (string) $default);

        return max($minimum, min($value, $maximum));
    }
}
