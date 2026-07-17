CREATE TABLE IF NOT EXISTS security_rate_limits (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    scope VARCHAR(50) NOT NULL,
    subject_hash CHAR(64) NOT NULL,
    attempt_count INT UNSIGNED NOT NULL DEFAULT 0,
    block_count INT UNSIGNED NOT NULL DEFAULT 0,
    window_started_at DATETIME NOT NULL,
    blocked_until DATETIME NULL,
    last_attempt_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_security_rate_limit_scope_subject (scope, subject_hash),
    KEY idx_security_rate_limit_blocked_until (blocked_until),
    KEY idx_security_rate_limit_updated_at (updated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO system_settings (
    setting_key,
    setting_value,
    updated_at
) VALUES
    ('security.login.max_attempts', '10', NOW()),
    ('security.login.window_seconds', '900', NOW()),
    ('security.login.block_seconds', '300', NOW()),
    ('security.login.escalation_factor', '2', NOW()),
    ('security.login.max_block_seconds', '86400', NOW()),
    ('security.register.max_attempts', '3', NOW()),
    ('security.register.window_seconds', '3600', NOW()),
    ('security.register.block_seconds', '3600', NOW()),
    ('security.register.escalation_factor', '2', NOW()),
    ('security.register.max_block_seconds', '86400', NOW())
ON DUPLICATE KEY UPDATE
    setting_value = VALUES(setting_value),
    updated_at = VALUES(updated_at);
