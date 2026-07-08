CREATE TABLE IF NOT EXISTS app_meta (
    meta_key VARCHAR(100) PRIMARY KEY,
    meta_value TEXT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO app_meta (meta_key, meta_value) VALUES
('app_version', '0.2.1'),
('app_codename', 'Atlas Hotfix'),
('app_build', '20260708.001')
ON DUPLICATE KEY UPDATE meta_value = VALUES(meta_value);
