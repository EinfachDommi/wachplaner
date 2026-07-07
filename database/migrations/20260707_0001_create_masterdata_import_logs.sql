CREATE TABLE IF NOT EXISTS masterdata_import_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    import_type VARCHAR(80) NOT NULL,
    file_name VARCHAR(255) NULL,
    processed INT NOT NULL DEFAULT 0,
    created_count INT NOT NULL DEFAULT 0,
    updated_count INT NOT NULL DEFAULT 0,
    skipped_count INT NOT NULL DEFAULT 0,
    error_count INT NOT NULL DEFAULT 0,
    errors_json JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
