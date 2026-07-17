<?php

declare(strict_types=1);

namespace Wachplaner\Core\Settings;

final class FeatureFlagService
{
    private const FLAGS = [
        'feature_hermes' => 'Hermes Synchronisation',
        'feature_api' => 'Interne API',
        'feature_backup' => 'Backup-Verwaltung',
        'feature_mail' => 'E-Mail-Versand',
    ];

    public function __construct(private readonly SettingRepository $repository)
    {
    }

    /** @return array<string, array{label:string,enabled:bool}> */
    public function all(): array
    {
        $defaults = array_fill_keys(array_keys(self::FLAGS), '0');
        $stored = $this->repository->getMany($defaults);
        $flags = [];

        foreach (self::FLAGS as $key => $label) {
            $flags[$key] = [
                'label' => $label,
                'enabled' => filter_var($stored[$key] ?? '0', FILTER_VALIDATE_BOOL),
            ];
        }

        return $flags;
    }

    public function enabled(string $key): bool
    {
        if (!array_key_exists($key, self::FLAGS)) {
            return false;
        }

        return filter_var($this->repository->get($key, '0'), FILTER_VALIDATE_BOOL);
    }

    /** @param array<string, bool> $flags */
    public function save(array $flags, ?int $userId = null): void
    {
        $values = [];

        foreach (self::FLAGS as $key => $label) {
            $values[$key] = !empty($flags[$key]) ? '1' : '0';
        }

        $this->repository->setMany($values, $userId);
    }
}
