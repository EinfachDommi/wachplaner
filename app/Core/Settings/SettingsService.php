<?php

declare(strict_types=1);

namespace Wachplaner\Core\Settings;

final class SettingsService
{
    public function __construct(private readonly SettingRepository $repository)
    {
    }

    /** @return array<string, string> */
    public function system(): array
    {
        return $this->repository->getMany([
            'app_timezone' => 'Europe/Berlin',
            'app_locale' => 'de_DE',
            'registration_enabled' => '1',
            'maintenance_enabled' => '0',
            'maintenance_message' => 'Der Wachplaner wird aktuell gewartet.',
            'maintenance_started_at' => '',
        ]);
    }

    public function boolean(string $key, bool $default = false): bool
    {
        $value = $this->repository->get($key, $default ? '1' : '0');

        return filter_var($value, FILTER_VALIDATE_BOOL);
    }

    public function string(string $key, string $default = ''): string
    {
        return (string) $this->repository->get($key, $default);
    }

    /** @param array<string, string> $settings */
    public function save(array $settings, ?int $userId = null): void
    {
        $this->repository->setMany($settings, $userId);
    }
}
