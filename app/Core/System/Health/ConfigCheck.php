<?php

declare(strict_types=1);

namespace Wachplaner\Core\System\Health;

use Wachplaner\Core\System\Contracts\SystemCheckInterface;

final class ConfigCheck implements SystemCheckInterface
{
    /** @param list<string> $requiredKeys */
    public function __construct(
        private readonly string $rootPath,
        private readonly array $requiredKeys = ['db.host', 'db.name', 'db.user']
    ) {
    }

    public function key(): string
    {
        return 'configuration';
    }

    public function run(): CheckResult
    {
        if (!\EnvLoader::exists($this->rootPath)) {
            return CheckResult::error(
                $this->key(),
                'Konfiguration',
                '.env fehlt'
            );
        }

        $missing = \Config::missing($this->requiredKeys);

        if ($missing !== []) {
            return CheckResult::error(
                $this->key(),
                'Konfiguration',
                'fehlende Werte: ' . implode(', ', $missing)
            );
        }

        return CheckResult::ok(
            $this->key(),
            'Konfiguration',
            '.env vollständig',
            true
        );
    }
}
