CREATE TABLE IF NOT EXISTS system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(120) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    updated_by INT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_system_settings_updated_by (updated_by),
    CONSTRAINT fk_system_settings_updated_by
        FOREIGN KEY (updated_by) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO system_settings (setting_key, setting_value)
VALUES
    ('maintenance_enabled', '0'),
    ('maintenance_message', 'Der Wachplaner wird aktuell gewartet.'),
    ('maintenance_started_at', ''),
    ('registration_enabled', '1')
ON DUPLICATE KEY UPDATE setting_key = VALUES(setting_key);

INSERT INTO app_meta (meta_key, meta_value)
VALUES ('app_version', '0.2.2')
ON DUPLICATE KEY UPDATE meta_value = VALUES(meta_value);
