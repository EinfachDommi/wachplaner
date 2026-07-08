CREATE TABLE IF NOT EXISTS station_types (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL UNIQUE,
  slug VARCHAR(180) NOT NULL UNIQUE,
  active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS vehicle_types (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(190) NOT NULL UNIQUE,
  slug VARCHAR(210) NOT NULL UNIQUE,
  category VARCHAR(120) NULL,
  station_type VARCHAR(160) NULL,
  required_expansion VARCHAR(190) NULL,
  personnel_required INT NOT NULL DEFAULT 0,
  required_training VARCHAR(255) NULL,
  credits INT NOT NULL DEFAULT 0,
  lss_vehicle_type_id INT NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS expansions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(190) NOT NULL,
  slug VARCHAR(210) NOT NULL UNIQUE,
  station_type VARCHAR(160) NULL,
  cost INT NOT NULL DEFAULT 0,
  build_time VARCHAR(80) NULL,
  unlocks TEXT NULL,
  notes TEXT NULL,
  active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS training_types (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(190) NOT NULL,
  slug VARCHAR(210) NOT NULL,
  organisation VARCHAR(120) NULL,
  school VARCHAR(160) NULL,
  duration_days INT NOT NULL DEFAULT 0,
  UNIQUE KEY uniq_training (slug, organisation, school)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS training_vehicle_requirements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  training_type_id INT NOT NULL,
  vehicle_name VARCHAR(190) NOT NULL,
  UNIQUE KEY uniq_training_vehicle (training_type_id, vehicle_name),
  FOREIGN KEY(training_type_id) REFERENCES training_types(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS station_build_costs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  station_count INT NOT NULL,
  station_type VARCHAR(180) NOT NULL,
  cost INT NOT NULL,
  UNIQUE KEY uniq_cost (station_count, station_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS masterdata_import_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  import_type VARCHAR(80) NOT NULL,
  source_file VARCHAR(255) NULL,
  status ENUM('success','warning','error') NOT NULL DEFAULT 'success',
  rows_total INT NOT NULL DEFAULT 0,
  rows_created INT NOT NULL DEFAULT 0,
  rows_updated INT NOT NULL DEFAULT 0,
  rows_skipped INT NOT NULL DEFAULT 0,
  message TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS app_meta (
  meta_key VARCHAR(100) PRIMARY KEY,
  meta_value TEXT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO app_meta (meta_key, meta_value) VALUES ('app_version', '0.2.0-dev')
ON DUPLICATE KEY UPDATE meta_value = VALUES(meta_value);

INSERT INTO app_meta (meta_key, meta_value) VALUES ('app_codename', 'Atlas')
ON DUPLICATE KEY UPDATE meta_value = VALUES(meta_value);
