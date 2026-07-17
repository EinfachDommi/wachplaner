<?php

declare(strict_types=1);

namespace Wachplaner\Core\System\Health;

use Wachplaner\Core\System\Contracts\SystemCheckInterface;

final class PhpExtensionCheck implements SystemCheckInterface
{
    /** @param array<string, string> $extensions */
    public function __construct(
        private readonly array $extensions = [
            'pdo_mysql' => 'PDO MySQL',
            'dom' => 'DOM/XML',
            'simplexml' => 'SimpleXML',
            'zip' => 'ZIP',
            'mbstring' => 'mbstring',
            'openssl' => 'OpenSSL',
        ]
    ) {
    }

    public function key(): string
    {
        return 'php_extensions';
    }

    public function run(): CheckResult
    {
        $missing = [];

        foreach ($this->extensions as $extension => $label) {
            if (!extension_loaded($extension)) {
                $missing[] = $label;
            }
        }

        if ($missing !== []) {
            return CheckResult::error(
                $this->key(),
                'PHP-Erweiterungen',
                'fehlen: ' . implode(', ', $missing)
            );
        }

        return CheckResult::ok(
            $this->key(),
            'PHP-Erweiterungen',
            sprintf('%d erforderlich, %d verfügbar', count($this->extensions), count($this->extensions)),
            true
        );
    }
}
