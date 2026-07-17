<?php

declare(strict_types=1);

namespace Wachplaner\Core\Settings;

use PDO;

final class SettingRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @param array<string, string> $defaults
     * @return array<string, string>
     */
    public function getMany(array $defaults): array
    {
        if ($defaults === []) {
            return [];
        }

        $keys = array_keys($defaults);
        $placeholders = implode(',', array_fill(0, count($keys), '?'));
        $statement = $this->pdo->prepare(
            "SELECT setting_key, setting_value FROM system_settings WHERE setting_key IN ($placeholders)"
        );
        $statement->execute($keys);

        $settings = $defaults;

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $settings[(string) $row['setting_key']] = (string) ($row['setting_value'] ?? '');
        }

        return $settings;
    }

    public function get(string $key, ?string $default = null): ?string
    {
        $statement = $this->pdo->prepare(
            'SELECT setting_value FROM system_settings WHERE setting_key = :setting_key LIMIT 1'
        );
        $statement->execute(['setting_key' => $key]);
        $value = $statement->fetchColumn();

        return $value === false ? $default : (string) $value;
    }

    public function set(string $key, string $value, ?int $userId = null): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO system_settings (setting_key, setting_value, updated_by)
             VALUES (:setting_key, :setting_value, :updated_by)
             ON DUPLICATE KEY UPDATE
                setting_value = VALUES(setting_value),
                updated_by = VALUES(updated_by),
                updated_at = CURRENT_TIMESTAMP'
        );
        $statement->execute([
            'setting_key' => $key,
            'setting_value' => $value,
            'updated_by' => $userId,
        ]);
    }

    /**
     * @param array<string, string> $settings
     */
    public function setMany(array $settings, ?int $userId = null): void
    {
        $this->pdo->beginTransaction();

        try {
            foreach ($settings as $key => $value) {
                $this->set($key, $value, $userId);
            }
            $this->pdo->commit();
        } catch (\Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }
}
