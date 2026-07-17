CREATE TABLE IF NOT EXISTS security_audit_logs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    event_type VARCHAR(100) NOT NULL,
    severity ENUM('info','warning','critical') NOT NULL DEFAULT 'info',
    result VARCHAR(80) NOT NULL,
    request_id VARCHAR(64) NOT NULL,
    user_id INT UNSIGNED NULL,
    subject_hash CHAR(64) NULL,
    ip_prefix_hash CHAR(64) NULL,
    user_agent_hash CHAR(64) NULL,
    metadata_json JSON NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_security_audit_created_at (created_at),
    KEY idx_security_audit_event_type (event_type),
    KEY idx_security_audit_severity (severity),
    KEY idx_security_audit_request_id (request_id),
    KEY idx_security_audit_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO system_settings (`key`, `value`, updated_at)
VALUES
    ('security.registration_mode', 'disabled', NOW()),
    ('security.audit_enabled', '1', NOW())
ON DUPLICATE KEY UPDATE
    `value` = VALUES(`value`),
    updated_at = VALUES(updated_at);
