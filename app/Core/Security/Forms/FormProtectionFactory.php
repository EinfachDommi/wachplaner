<?php

declare(strict_types=1);

namespace Wachplaner\Core\Security\Forms;

use Wachplaner\Core\Config;

final class FormProtectionFactory
{
    public static function create(): FormProtectionService
    {
        $secret = (string) Config::get(
            'security.form_secret',
            Config::get('app.key', '')
        );

        $minimumAge = (int) Config::get(
            'security.form_minimum_age',
            2
        );

        $maximumAge = (int) Config::get(
            'security.form_maximum_age',
            7200
        );

        return new FormProtectionService(
            new HoneypotGuard(
                (string) Config::get(
                    'security.honeypot_field',
                    'website'
                )
            ),
            new FormTokenService(
                $secret,
                max(1, $minimumAge),
                max(60, $maximumAge)
            )
        );
    }
}
