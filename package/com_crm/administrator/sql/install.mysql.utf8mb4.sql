-- This file is used by com_crm installer (Joomla).
-- It is identical to /sql/install.mysql.utf8mb4.sql in the repo root.

CREATE TABLE IF NOT EXISTS `#__companies` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `current_stage` VARCHAR(64) NOT NULL,
  `created_at` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  PRIMARY KEY (`id`),
  KEY `idx_companies_stage_created` (`current_stage`, `created_at`),
  KEY `idx_companies_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__crm_events` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `event_type` VARCHAR(64) NOT NULL,
  `payload` JSON NOT NULL,
  `created_at` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  PRIMARY KEY (`id`),
  KEY `idx_events_company_created` (`company_id`, `created_at`),
  KEY `idx_events_type_created` (`event_type`, `created_at`),
  CONSTRAINT `fk_events_company`
    FOREIGN KEY (`company_id`) REFERENCES `#__companies` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__discovery_forms` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` BIGINT UNSIGNED NULL,
  `form_key` VARCHAR(64) NOT NULL,
  `data` JSON NOT NULL,
  `created_at` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  PRIMARY KEY (`id`),
  KEY `idx_forms_company_created` (`company_id`, `created_at`),
  KEY `idx_forms_formkey_created` (`form_key`, `created_at`),
  CONSTRAINT `fk_forms_company`
    FOREIGN KEY (`company_id`) REFERENCES `#__companies` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

