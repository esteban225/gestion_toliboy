-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema toliboy
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `toliboy`;
CREATE SCHEMA IF NOT EXISTS `toliboy` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `toliboy` ;

-- -----------------------------------------------------
-- Table `toliboy`.`roles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `toliboy`.`roles` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `permissions` JSON NULL DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) VISIBLE,
  INDEX `idx_role_active` (`is_active` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;


-- -----------------------------------------------------
-- Table `toliboy`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `toliboy`.`users` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role_id` BIGINT NULL DEFAULT NULL,
  `position` VARCHAR(100) NULL DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `last_login` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) VISIBLE,
  INDEX `fk_users_roles_idx` (`role_id` ASC) VISIBLE,
  INDEX `idx_user_active` (`is_active` ASC) VISIBLE,
  INDEX `idx_user_email` (`email` ASC) VISIBLE,
  CONSTRAINT `fk_users_roles`
    FOREIGN KEY (`role_id`)
    REFERENCES `toliboy`.`roles` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;


-- -----------------------------------------------------
-- Table `toliboy`.`raw_materials`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `toliboy`.`raw_materials` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `code` VARCHAR(50) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `unit_of_measure` VARCHAR(50) NOT NULL, -- ej: kg, litros, unidades
  `stock` DECIMAL(10,2) NOT NULL DEFAULT 0, -- stock físico registrado (sincronizar con movimientos)
  `min_stock` DECIMAL(10,2) DEFAULT 0,   -- stock mínimo permitido
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_by` BIGINT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `code_UNIQUE` (`code`),
  INDEX `idx_material_active` (`is_active`),
  CONSTRAINT `fk_raw_materials_users`
    FOREIGN KEY (`created_by`) REFERENCES `toliboy`.`users` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;





-- -----------------------------------------------------
-- Table `toliboy`.`personal_data`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `toliboy`.`personal_data` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT NOT NULL,
  `num_phone` VARCHAR(20) NULL DEFAULT NULL,
  `num_phone_alt` VARCHAR(20) NULL DEFAULT NULL,
  `num_identification` VARCHAR(50) NULL DEFAULT NULL,
  `identification_type` VARCHAR(45) NULL DEFAULT NULL,
  `address` VARCHAR(255) NULL DEFAULT NULL,
  `emergency_contact` VARCHAR(100) NULL DEFAULT NULL,
  `emergency_phone` VARCHAR(20) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `num_identification_UNIQUE` (`num_identification` ASC) VISIBLE,
  UNIQUE INDEX `user_id_UNIQUE` (`user_id` ASC) VISIBLE,
  INDEX `fk_personal_data_users_idx` (`user_id` ASC) VISIBLE,
  CONSTRAINT `fk_personal_data_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `toliboy`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;


-- -----------------------------------------------------
-- Table `toliboy`.`audit_logs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `toliboy`.`audit_logs` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `table_name` VARCHAR(100) NOT NULL,
  `record_id` BIGINT NOT NULL,
  `action` ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
  `old_values` JSON NULL DEFAULT NULL,
  `new_values` JSON NULL DEFAULT NULL,
  `user_id` BIGINT NULL DEFAULT NULL,
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_audit_table` (`table_name` ASC) VISIBLE,
  INDEX `idx_audit_record` (`table_name` ASC, `record_id` ASC) VISIBLE,
  INDEX `idx_audit_created` (`created_at` ASC) VISIBLE,
  INDEX `idx_audit_user` (`user_id` ASC) VISIBLE,
  INDEX `idx_audit_action` (`action` ASC) VISIBLE,
  CONSTRAINT `fk_audit_logs_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `toliboy`.`users` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;


-- -----------------------------------------------------
-- Table `toliboy`.`products`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `toliboy`.`products` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `code` VARCHAR(50) NOT NULL,
  `category` VARCHAR(100) NULL DEFAULT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `specifications` JSON NULL DEFAULT NULL,
  `unit_price` DECIMAL(10,2) NULL DEFAULT 0.00,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_by` BIGINT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `code_UNIQUE` (`code` ASC) VISIBLE,
  INDEX `idx_product_category` (`category` ASC) VISIBLE,
  INDEX `idx_product_active` (`is_active` ASC) VISIBLE,
  INDEX `fk_products_creator_idx` (`created_by` ASC) VISIBLE,
  CONSTRAINT `fk_products_creator`
    FOREIGN KEY (`created_by`)
    REFERENCES `toliboy`.`users` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;


-- -----------------------------------------------------
-- Table `toliboy`.`batches`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `toliboy`.`batches` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `code` VARCHAR(50) NOT NULL,
  `product_id` BIGINT NULL DEFAULT NULL,
  `start_date` DATETIME NOT NULL,
  `expected_end_date` DATETIME NULL DEFAULT NULL,
  `actual_end_date` DATETIME NULL DEFAULT NULL,
  `status` ENUM('planned', 'in_process', 'paused', 'completed', 'delivered', 'cancelled') NOT NULL DEFAULT 'planned',
  `quantity` INT NOT NULL DEFAULT 0,
  `defect_quantity` INT NULL DEFAULT 0,
  `notes` TEXT NULL DEFAULT NULL,
  `created_by` BIGINT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `code_UNIQUE` (`code` ASC) VISIBLE,
  INDEX `fk_batches_products_idx` (`product_id` ASC) VISIBLE,
  INDEX `idx_batch_status` (`status` ASC) VISIBLE,
  INDEX `idx_batch_dates` (`start_date` ASC, `expected_end_date` ASC) VISIBLE,
  INDEX `fk_batches_creator_idx` (`created_by` ASC) VISIBLE,
  CONSTRAINT `fk_batches_products`
    FOREIGN KEY (`product_id`)
    REFERENCES `toliboy`.`products` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_batches_creator`
    FOREIGN KEY (`created_by`)
    REFERENCES `toliboy`.`users` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;



-- -----------------------------------------------------
-- Table `toliboy`.`inventory_movements`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `toliboy`.`inventory_movements` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `raw_material_id` BIGINT NOT NULL,
  `batch_id` BIGINT NULL, -- si se usó para un lote
  `movement_type` ENUM('in', 'out', 'adjustment') NOT NULL,
    `production_line` VARCHAR(100) NOT NULL, -- nueva columna para línea de producción
  `quantity` DECIMAL(10,2) NOT NULL,
  `unit_cost` DECIMAL(10,2) NULL DEFAULT 0.00, -- opcional, para costos promedio
  `notes` TEXT NULL DEFAULT NULL,
  `created_by` BIGINT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_inventory_material` (`raw_material_id`),
  INDEX `idx_inventory_batch` (`batch_id`),
  INDEX `idx_inventory_type` (`movement_type`),
  CONSTRAINT `fk_inventory_material`
    FOREIGN KEY (`raw_material_id`) REFERENCES `toliboy`.`raw_materials` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_batch`
    FOREIGN KEY (`batch_id`) REFERENCES `toliboy`.`batches` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_users`
    FOREIGN KEY (`created_by`) REFERENCES `toliboy`.`users` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- -----------------------------------------------------
-- Table `toliboy`.`current_user_sessions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `toliboy`.`current_user_sessions` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,  -- Laravel espera un string como id de sesión
  `user_id` BIGINT NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` TEXT NULL,
  `payload` TEXT NOT NULL, -- AQUI Laravel guarda la información de la sesión
  `last_activity` INT NOT NULL, -- Timestamp UNIX del último acceso
  `connection_id` INT NOT NULL UNIQUE,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` TIMESTAMP NULL DEFAULT (CURRENT_TIMESTAMP + INTERVAL 1 HOUR),
  PRIMARY KEY (`id`),
  INDEX `idx_user_id` (`user_id` ASC),
  INDEX `idx_ip_address` (`ip_address` ASC),
  INDEX `idx_last_activity` (`last_activity` ASC),
  INDEX `idx_expires_at` (`expires_at` ASC),
  CONSTRAINT `fk_current_user_sessions_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `toliboy`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `toliboy`.`forms`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `toliboy`.`forms` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `code` VARCHAR(50) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `version` VARCHAR(20) NOT NULL DEFAULT '1.0',
  `created_by` BIGINT NULL DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `display_order` INT NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `code_version_UNIQUE` (`code` ASC, `version` ASC) VISIBLE,
  INDEX `fk_forms_users_idx` (`created_by` ASC) VISIBLE,
  INDEX `idx_form_active` (`is_active` ASC) VISIBLE,
  CONSTRAINT `fk_forms_users`
    FOREIGN KEY (`created_by`)
    REFERENCES `toliboy`.`users` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;


-- -----------------------------------------------------
-- Table `toliboy`.`form_fields`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `toliboy`.`form_fields` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `form_id` BIGINT NOT NULL,
  `label` VARCHAR(255) NOT NULL,
  `field_code` VARCHAR(100) NOT NULL,
  `type` ENUM('text', 'number', 'date', 'time', 'select', 'checkbox', 'radio', 'textarea', 'file') NOT NULL DEFAULT 'text',
  `required` TINYINT(1) NOT NULL DEFAULT 0,
  `options` JSON NULL DEFAULT NULL,
  `validation_rules` JSON NULL DEFAULT NULL,
  `field_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `form_field_code_UNIQUE` (`form_id` ASC, `field_code` ASC) VISIBLE,
  INDEX `fk_form_fields_forms_idx` (`form_id` ASC) VISIBLE,
  INDEX `idx_form_field_order` (`form_id` ASC, `field_order` ASC) VISIBLE,
  INDEX `idx_field_active` (`is_active` ASC) VISIBLE,
  CONSTRAINT `fk_form_fields_forms`
    FOREIGN KEY (`form_id`)
    REFERENCES `toliboy`.`forms` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;


-- -----------------------------------------------------
-- Table `toliboy`.`form_responses`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `toliboy`.`form_responses` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `form_id` BIGINT NOT NULL,
  `user_id` BIGINT NOT NULL,
  `batch_id` BIGINT NULL DEFAULT NULL,
  `status` ENUM('pending', 'in_progress', 'completed', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  `submitted_at` TIMESTAMP NULL DEFAULT NULL,
  `reviewed_by` BIGINT NULL DEFAULT NULL,
  `reviewed_at` TIMESTAMP NULL DEFAULT NULL,
  `review_notes` TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_form_responses_forms_idx` (`form_id` ASC) VISIBLE,
  INDEX `fk_form_responses_users_idx` (`user_id` ASC) VISIBLE,
  INDEX `fk_form_responses_batches_idx` (`batch_id` ASC) VISIBLE,
  INDEX `idx_response_status` (`status` ASC) VISIBLE,
  INDEX `idx_response_created` (`created_at` ASC) VISIBLE,
  INDEX `fk_form_responses_reviewer_idx` (`reviewed_by` ASC) VISIBLE,
  CONSTRAINT `fk_form_responses_batches`
    FOREIGN KEY (`batch_id`)
    REFERENCES `toliboy`.`batches` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_form_responses_forms`
    FOREIGN KEY (`form_id`)
    REFERENCES `toliboy`.`forms` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_form_responses_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `toliboy`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_form_responses_reviewer`
    FOREIGN KEY (`reviewed_by`)
    REFERENCES `toliboy`.`users` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;


-- -----------------------------------------------------
-- Table `toliboy`.`form_response_values`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `toliboy`.`form_response_values` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `response_id` BIGINT NOT NULL,
  `field_id` BIGINT NOT NULL,
  `value` TEXT NULL DEFAULT NULL,
  `file_path` VARCHAR(500) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `unique_response_field` (`response_id` ASC, `field_id` ASC) VISIBLE,
  INDEX `fk_form_response_values_responses_idx` (`response_id` ASC) VISIBLE,
  INDEX `fk_form_response_values_fields_idx` (`field_id` ASC) VISIBLE,
  INDEX `idx_response_value` (`response_id` ASC, `field_id` ASC) VISIBLE,
  CONSTRAINT `fk_form_response_values_fields`
    FOREIGN KEY (`field_id`)
    REFERENCES `toliboy`.`form_fields` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_form_response_values_responses`
    FOREIGN KEY (`response_id`)
    REFERENCES `toliboy`.`form_responses` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;


-- -----------------------------------------------------
-- Table `toliboy`.`work_logs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `toliboy`.`work_logs` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT NOT NULL,
  `date` DATE NOT NULL,
  `start_time` TIME NULL DEFAULT NULL,
  `end_time` TIME NULL DEFAULT NULL,
  `total_hours` TIME NULL,
  `overtime_hours` TIME NULL,
  `batch_id` BIGINT NULL DEFAULT NULL,
  `task_description` TEXT NULL DEFAULT NULL,
  `notes` TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_work_logs_users_idx` (`user_id` ASC) VISIBLE,
  INDEX `fk_work_logs_batches_idx` (`batch_id` ASC) VISIBLE,
  INDEX `idx_work_log_date` (`date` ASC) VISIBLE,
  INDEX `idx_work_log_user_date` (`user_id` ASC, `date` ASC) VISIBLE,
  CONSTRAINT `fk_work_logs_batches`
    FOREIGN KEY (`batch_id`)
    REFERENCES `toliboy`.`batches` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_work_logs_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `toliboy`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;


-- Notificaciones base (igual a la tuya, solo añadimos `scope`)
 CREATE TABLE IF NOT EXISTS `notifications` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `type` ENUM('info', 'warning', 'error', 'success') NOT NULL DEFAULT 'info',
  `scope` ENUM('individual','group','global') NOT NULL DEFAULT 'individual',
  `related_table` VARCHAR(100) NULL DEFAULT NULL,
  `related_id` BIGINT NULL DEFAULT NULL,
  `user_id` BIGINT NULL DEFAULT NULL,
  `expires_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_notification_type` (`type` ASC) VISIBLE,
  INDEX `idx_notification_related` (`related_table` ASC, `related_id` ASC) VISIBLE
) ENGINE=InnoDB
  AUTO_INCREMENT=1
  DEFAULT CHARACTER SET=utf8mb4
  COLLATE=utf8mb4_general_ci;


-- Tabla pivote usuarios <-> notificaciones
CREATE TABLE IF NOT EXISTS `notification_user` (
  `notification_id` BIGINT NOT NULL,
  `user_id` BIGINT NOT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `read_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`notification_id`, `user_id`),
  FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  INDEX `idx_notification_user` (`user_id` ASC, `is_read` ASC)
) ENGINE=InnoDB
  DEFAULT CHARACTER SET=utf8mb4
  COLLATE=utf8mb4_general_ci;



-- -----------------------------------------------------
-- Funciones para manejo de sesiones (AHORA ANTES de los triggers)
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS toliboy.set_current_user;
DELIMITER $$

CREATE PROCEDURE toliboy.set_current_user(
    IN p_id CHAR(36),          -- 👈 nuevo parámetro
    IN p_user_id BIGINT,
    IN p_ip_address VARCHAR(45),
    IN p_user_agent TEXT
)
SQL SECURITY INVOKER
BEGIN
    DECLARE v_connection_id INT;
    SET v_connection_id = CONNECTION_ID();

    IF p_ip_address IS NULL THEN SET p_ip_address = '0.0.0.0'; END IF;
    IF p_user_agent IS NULL THEN SET p_user_agent = 'Desconocido'; END IF;

    -- Borra la sesión de esta conexión
    DELETE FROM toliboy.current_user_sessions
    WHERE connection_id = v_connection_id
    LIMIT 1;

    -- Limpia expiradas
    DELETE FROM toliboy.current_user_sessions
    WHERE expires_at <= NOW()
    LIMIT 10000;

    -- Inserta con el id UUID generado en Laravel
	INSERT INTO toliboy.current_user_sessions
      (connection_id, user_id, ip_address, user_agent, payload, last_activity, created_at, expires_at)
	VALUES
      (v_connection_id, p_user_id, p_ip_address, p_user_agent, '',
       UNIX_TIMESTAMP(), NOW(), TIMESTAMPADD(HOUR, 1, CURRENT_TIMESTAMP));
END$$




-- Funciones usadas por los triggers para saber el usuario/IP/UA actual
DROP FUNCTION IF EXISTS toliboy.get_current_user_id $$
CREATE FUNCTION toliboy.get_current_user_id()
RETURNS BIGINT
SQL SECURITY INVOKER
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_user_id BIGINT;
    SELECT user_id
      INTO v_user_id
    FROM toliboy.current_user_sessions
    WHERE connection_id = CONNECTION_ID()
      AND expires_at > NOW()
    ORDER BY id DESC
    LIMIT 1;
    RETURN v_user_id;
END$$

DROP FUNCTION IF EXISTS toliboy.get_current_ip_address $$
CREATE FUNCTION toliboy.get_current_ip_address()
RETURNS VARCHAR(45)
SQL SECURITY INVOKER
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_ip VARCHAR(45);
    SELECT ip_address
      INTO v_ip
    FROM toliboy.current_user_sessions
    WHERE connection_id = CONNECTION_ID()
      AND expires_at > NOW()
    ORDER BY id DESC
    LIMIT 1;
    RETURN v_ip;
END$$

DROP FUNCTION IF EXISTS toliboy.get_current_user_agent $$
CREATE FUNCTION toliboy.get_current_user_agent()
RETURNS VARCHAR(1024)
SQL SECURITY INVOKER
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_ua VARCHAR(1024);
    SELECT user_agent
      INTO v_ua
    FROM toliboy.current_user_sessions
    WHERE connection_id = CONNECTION_ID()
      AND expires_at > NOW()
    ORDER BY id DESC
    LIMIT 1;
    RETURN v_ua;
END$$
DELIMITER ;

-- ============================
-- AUDITORÍA
-- ============================

-- Actividad de auditoría por tabla y acción (totales y últimos 30 días)
CREATE OR REPLACE VIEW `v_audit_activity_by_table` AS
SELECT
  al.table_name,
  al.action,
  COUNT(*) AS total_count,
  SUM(CASE WHEN al.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) AS last_30d_count,
  MAX(al.created_at) AS last_activity_at
FROM audit_logs al
GROUP BY al.table_name, al.action;

-- Actividad diaria por usuario (últimos 30 días)
CREATE OR REPLACE VIEW `v_audit_activity_by_user_day` AS
SELECT
  DATE(al.created_at) AS activity_date,
  al.user_id,
  u.name AS user_name,
  al.action,
  COUNT(*) AS events_count
FROM audit_logs al
LEFT JOIN users u ON u.id = al.user_id
WHERE al.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
GROUP BY DATE(al.created_at), al.user_id, u.name, al.action;
-- -----------------------------------------------------
-- View `toliboy`.`active_sessions`
-- -----------------------------------------------------
CREATE OR REPLACE VIEW `toliboy`.`active_sessions` AS
SELECT
    c.id,
    c.connection_id,
    c.user_id,
    u.name as user_name,
    u.email,
    c.ip_address,
    c.user_agent,
    c.created_at,
    c.expires_at,
    TIMEDIFF(c.expires_at, NOW()) as remaining_time
FROM `toliboy`.`current_user_sessions` c
JOIN `toliboy`.`users` u ON c.user_id = u.id
WHERE c.expires_at > NOW()
ORDER BY c.created_at DESC;

-- -----------------------------------------------------
-- Procedimientos almacenados
-- -----------------------------------------------------

DELIMITER $$

-- -----------------------------------------------------
-- Procedure `toliboy`.`clean_expired_sessions`
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS toliboy.clean_expired_sessions $$
CREATE PROCEDURE toliboy.clean_expired_sessions()
BEGIN
    DELETE FROM toliboy.current_user_sessions WHERE expires_at <= NOW();
END$$

DELIMITER ;

-- -----------------------------------------------------
-- Event `toliboy`.`session_cleanup_event`
-- -----------------------------------------------------
DELIMITER $$
CREATE EVENT IF NOT EXISTS `session_cleanup_event`
ON SCHEDULE EVERY 1 HOUR
DO
BEGIN
    CALL toliboy.clean_expired_sessions();
END$$
DELIMITER ;


-- -----------------------------------------------------
-- Triggers for audit logs
-- -----------------------------------------------------

DELIMITER $$

/* ============================
   Table: roles
   ============================ */
DROP TRIGGER IF EXISTS `roles_after_insert` $$
CREATE TRIGGER `roles_after_insert` AFTER INSERT ON `roles` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, new_values, user_id, ip_address, user_agent)
    VALUES (
        'roles', NEW.id, 'INSERT',
        JSON_OBJECT(
            'name', NEW.name,
            'description', NEW.description,
            'permissions', NEW.permissions,
            'is_active', NEW.is_active
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$

DROP TRIGGER IF EXISTS `roles_after_update` $$
CREATE TRIGGER `roles_after_update` AFTER UPDATE ON `roles` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, new_values, user_id, ip_address, user_agent)
    VALUES (
        'roles', NEW.id, 'UPDATE',
        JSON_OBJECT(
            'name', OLD.name,
            'description', OLD.description,
            'permissions', OLD.permissions,
            'is_active', OLD.is_active
        ),
        JSON_OBJECT(
            'name', NEW.name,
            'description', NEW.description,
            'permissions', NEW.permissions,
            'is_active', NEW.is_active
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$

DROP TRIGGER IF EXISTS `roles_after_delete` $$
CREATE TRIGGER `roles_after_delete` AFTER DELETE ON `roles` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, user_id, ip_address, user_agent)
    VALUES (
        'roles', OLD.id, 'DELETE',
        JSON_OBJECT(
            'name', OLD.name,
            'description', OLD.description,
            'permissions', OLD.permissions,
            'is_active', OLD.is_active
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$


/* ============================
   Table: users
   ============================ */
DROP TRIGGER IF EXISTS `users_after_insert` $$
CREATE TRIGGER `users_after_insert` AFTER INSERT ON `users` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, new_values, user_id, ip_address, user_agent)
    VALUES (
        'users', NEW.id, 'INSERT',
        JSON_OBJECT(
            'name', NEW.name,
            'email', NEW.email,
            'role_id', NEW.role_id,
            'position', NEW.position,
            'is_active', NEW.is_active,
            'last_login', NEW.last_login
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$

DROP TRIGGER IF EXISTS `users_after_update` $$
CREATE TRIGGER `users_after_update` AFTER UPDATE ON `users` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, new_values, user_id, ip_address, user_agent)
    VALUES (
        'users', NEW.id, 'UPDATE',
        JSON_OBJECT(
            'name', OLD.name,
            'email', OLD.email,
            'role_id', OLD.role_id,
            'position', OLD.position,
            'is_active', OLD.is_active,
            'last_login', OLD.last_login
        ),
        JSON_OBJECT(
            'name', NEW.name,
            'email', NEW.email,
            'role_id', NEW.role_id,
            'position', NEW.position,
            'is_active', NEW.is_active,
            'last_login', NEW.last_login
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$

DROP TRIGGER IF EXISTS `users_after_delete` $$
CREATE TRIGGER `users_after_delete` AFTER DELETE ON `users` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, user_id, ip_address, user_agent)
    VALUES (
        'users', OLD.id, 'DELETE',
        JSON_OBJECT(
            'name', OLD.name,
            'email', OLD.email,
            'role_id', OLD.role_id,
            'position', OLD.position,
            'is_active', OLD.is_active,
            'last_login', OLD.last_login
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$


/* ============================
   Table: raw_materials
   ============================ */
DROP TRIGGER IF EXISTS `raw_materials_after_insert` $$
CREATE TRIGGER `raw_materials_after_insert` AFTER INSERT ON `raw_materials` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, new_values, user_id, ip_address, user_agent)
    VALUES (
        'raw_materials', NEW.id, 'INSERT',
        JSON_OBJECT(
            'name', NEW.name,
            'code', NEW.code,
            'description', NEW.description,
            'unit_of_measure', NEW.unit_of_measure,
            'stock', NEW.stock,
            'min_stock', NEW.min_stock,
            'is_active', NEW.is_active,
            'created_by', NEW.created_by
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$

DROP TRIGGER IF EXISTS `raw_materials_after_update` $$
CREATE TRIGGER `raw_materials_after_update` AFTER UPDATE ON `raw_materials` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, new_values, user_id, ip_address, user_agent)
    VALUES (
        'raw_materials', NEW.id, 'UPDATE',
        JSON_OBJECT(
            'name', OLD.name,
            'code', OLD.code,
            'description', OLD.description,
            'unit_of_measure', OLD.unit_of_measure,
            'stock', OLD.stock,
            'min_stock', OLD.min_stock,
            'is_active', OLD.is_active,
            'created_by', OLD.created_by
        ),
        JSON_OBJECT(
            'name', NEW.name,
            'code', NEW.code,
            'description', NEW.description,
            'unit_of_measure', NEW.unit_of_measure,
            'stock', NEW.stock,
            'min_stock', NEW.min_stock,
            'is_active', NEW.is_active,
            'created_by', NEW.created_by
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$

DROP TRIGGER IF EXISTS `raw_materials_after_delete` $$
CREATE TRIGGER `raw_materials_after_delete` AFTER DELETE ON `raw_materials` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, user_id, ip_address, user_agent)
    VALUES (
        'raw_materials', OLD.id, 'DELETE',
        JSON_OBJECT(
            'name', OLD.name,
            'code', OLD.code,
            'description', OLD.description,
            'unit_of_measure', OLD.unit_of_measure,
            'stock', OLD.stock,
            'min_stock', OLD.min_stock,
            'is_active', OLD.is_active,
            'created_by', OLD.created_by
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$


/* ============================
   Table: personal_data
   ============================ */
DROP TRIGGER IF EXISTS `personal_data_after_insert` $$
CREATE TRIGGER `personal_data_after_insert` AFTER INSERT ON `personal_data` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, new_values, user_id, ip_address, user_agent)
    VALUES (
        'personal_data', NEW.id, 'INSERT',
        JSON_OBJECT(
            'user_id', NEW.user_id,
            'num_phone', NEW.num_phone,
            'num_phone_alt', NEW.num_phone_alt,
            'num_identification', NEW.num_identification,
            'identification_type', NEW.identification_type,
            'address', NEW.address,
            'emergency_contact', NEW.emergency_contact,
            'emergency_phone', NEW.emergency_phone
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$

DROP TRIGGER IF EXISTS `personal_data_after_update` $$
CREATE TRIGGER `personal_data_after_update` AFTER UPDATE ON `personal_data` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, new_values, user_id, ip_address, user_agent)
    VALUES (
        'personal_data', NEW.id, 'UPDATE',
        JSON_OBJECT(
            'user_id', OLD.user_id,
            'num_phone', OLD.num_phone,
            'num_phone_alt', OLD.num_phone_alt,
            'num_identification', OLD.num_identification,
            'identification_type', OLD.identification_type,
            'address', OLD.address,
            'emergency_contact', OLD.emergency_contact,
            'emergency_phone', OLD.emergency_phone
        ),
        JSON_OBJECT(
            'user_id', NEW.user_id,
            'num_phone', NEW.num_phone,
            'num_phone_alt', NEW.num_phone_alt,
            'num_identification', NEW.num_identification,
            'identification_type', NEW.identification_type,
            'address', NEW.address,
            'emergency_contact', NEW.emergency_contact,
            'emergency_phone', NEW.emergency_phone
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$

DROP TRIGGER IF EXISTS `personal_data_after_delete` $$
CREATE TRIGGER `personal_data_after_delete` AFTER DELETE ON `personal_data` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, user_id, ip_address, user_agent)
    VALUES (
        'personal_data', OLD.id, 'DELETE',
        JSON_OBJECT(
            'user_id', OLD.user_id,
            'num_phone', OLD.num_phone,
            'num_phone_alt', OLD.num_phone_alt,
            'num_identification', OLD.num_identification,
            'identification_type', OLD.identification_type,
            'address', OLD.address,
            'emergency_contact', OLD.emergency_contact,
            'emergency_phone', OLD.emergency_phone
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$


/* ============================
   Table: products
   ============================ */
DROP TRIGGER IF EXISTS `products_after_insert` $$
CREATE TRIGGER `products_after_insert` AFTER INSERT ON `products` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, new_values, user_id, ip_address, user_agent)
    VALUES (
        'products', NEW.id, 'INSERT',
        JSON_OBJECT(
            'name', NEW.name,
            'code', NEW.code,
            'category', NEW.category,
            'description', NEW.description,
            'specifications', NEW.specifications,
            'unit_price', NEW.unit_price,
            'is_active', NEW.is_active,
            'created_by', NEW.created_by
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$

DROP TRIGGER IF EXISTS `products_after_update` $$
CREATE TRIGGER `products_after_update` AFTER UPDATE ON `products` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, new_values, user_id, ip_address, user_agent)
    VALUES (
        'products', NEW.id, 'UPDATE',
        JSON_OBJECT(
            'name', OLD.name,
            'code', OLD.code,
            'category', OLD.category,
            'description', OLD.description,
            'specifications', OLD.specifications,
            'unit_price', OLD.unit_price,
            'is_active', OLD.is_active,
            'created_by', OLD.created_by
        ),
        JSON_OBJECT(
            'name', NEW.name,
            'code', NEW.code,
            'category', NEW.category,
            'description', NEW.description,
            'specifications', NEW.specifications,
            'unit_price', NEW.unit_price,
            'is_active', NEW.is_active,
            'created_by', NEW.created_by
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$

DROP TRIGGER IF EXISTS `products_after_delete` $$
CREATE TRIGGER `products_after_delete` AFTER DELETE ON `products` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, user_id, ip_address, user_agent)
    VALUES (
        'products', OLD.id, 'DELETE',
        JSON_OBJECT(
            'name', OLD.name,
            'code', OLD.code,
            'category', OLD.category,
            'description', OLD.description,
            'specifications', OLD.specifications,
            'unit_price', OLD.unit_price,
            'is_active', OLD.is_active,
            'created_by', OLD.created_by
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$


/* ============================
   Table: batches
   ============================ */
DROP TRIGGER IF EXISTS `batches_after_insert` $$
CREATE TRIGGER `batches_after_insert` AFTER INSERT ON `batches` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, new_values, user_id, ip_address, user_agent)
    VALUES (
        'batches', NEW.id, 'INSERT',
        JSON_OBJECT(
            'name', NEW.name,
            'code', NEW.code,
            'product_id', NEW.product_id,
            'start_date', NEW.start_date,
            'expected_end_date', NEW.expected_end_date,
            'actual_end_date', NEW.actual_end_date,
            'status', NEW.status,
            'quantity', NEW.quantity,
            'defect_quantity', NEW.defect_quantity,
            'notes', NEW.notes,
            'created_by', NEW.created_by
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$

DROP TRIGGER IF EXISTS `batches_after_update` $$
CREATE TRIGGER `batches_after_update` AFTER UPDATE ON `batches` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, new_values, user_id, ip_address, user_agent)
    VALUES (
        'batches', NEW.id, 'UPDATE',
        JSON_OBJECT(
            'name', OLD.name,
            'code', OLD.code,
            'product_id', OLD.product_id,
            'start_date', OLD.start_date,
            'expected_end_date', OLD.expected_end_date,
            'actual_end_date', OLD.actual_end_date,
            'status', OLD.status,
            'quantity', OLD.quantity,
            'defect_quantity', OLD.defect_quantity,
            'notes', OLD.notes,
            'created_by', OLD.created_by
        ),
        JSON_OBJECT(
            'name', NEW.name,
            'code', NEW.code,
            'product_id', NEW.product_id,
            'start_date', NEW.start_date,
            'expected_end_date', NEW.expected_end_date,
            'actual_end_date', NEW.actual_end_date,
            'status', NEW.status,
            'quantity', NEW.quantity,
            'defect_quantity', NEW.defect_quantity,
            'notes', NEW.notes,
            'created_by', NEW.created_by
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$

DROP TRIGGER IF EXISTS `batches_after_delete` $$
CREATE TRIGGER `batches_after_delete` AFTER DELETE ON `batches` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, user_id, ip_address, user_agent)
    VALUES (
        'batches', OLD.id, 'DELETE',
        JSON_OBJECT(
            'name', OLD.name,
            'code', OLD.code,
            'product_id', OLD.product_id,
            'start_date', OLD.start_date,
            'expected_end_date', OLD.expected_end_date,
            'actual_end_date', OLD.actual_end_date,
            'status', OLD.status,
            'quantity', OLD.quantity,
            'defect_quantity', OLD.defect_quantity,
            'notes', OLD.notes,
            'created_by', OLD.created_by
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$


/* ============================
   Table: inventory_movements
   ============================ */
DROP TRIGGER IF EXISTS `inventory_movements_after_insert` $$
CREATE TRIGGER `inventory_movements_after_insert` AFTER INSERT ON `inventory_movements` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, new_values, user_id, ip_address, user_agent)
    VALUES (
        'inventory_movements', NEW.id, 'INSERT',
        JSON_OBJECT(
            'raw_material_id', NEW.raw_material_id,
            'batch_id', NEW.batch_id,
            'movement_type', NEW.movement_type,
            'quantity', NEW.quantity,
            'unit_cost', NEW.unit_cost,
            'notes', NEW.notes,
            'created_by', NEW.created_by
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$

DROP TRIGGER IF EXISTS `inventory_movements_after_update` $$
CREATE TRIGGER `inventory_movements_after_update` AFTER UPDATE ON `inventory_movements` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, new_values, user_id, ip_address, user_agent)
    VALUES (
        'inventory_movements', NEW.id, 'UPDATE',
        JSON_OBJECT(
            'raw_material_id', OLD.raw_material_id,
            'batch_id', OLD.batch_id,
            'movement_type', OLD.movement_type,
            'quantity', OLD.quantity,
            'unit_cost', OLD.unit_cost,
            'notes', OLD.notes,
            'created_by', OLD.created_by
        ),
        JSON_OBJECT(
            'raw_material_id', NEW.raw_material_id,
            'batch_id', NEW.batch_id,
            'movement_type', NEW.movement_type,
            'quantity', NEW.quantity,
            'unit_cost', NEW.unit_cost,
            'notes', NEW.notes,
            'created_by', NEW.created_by
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$

DROP TRIGGER IF EXISTS `inventory_movements_after_delete` $$
CREATE TRIGGER `inventory_movements_after_delete` AFTER DELETE ON `inventory_movements` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, user_id, ip_address, user_agent)
    VALUES (
        'inventory_movements', OLD.id, 'DELETE',
        JSON_OBJECT(
            'raw_material_id', OLD.raw_material_id,
            'batch_id', OLD.batch_id,
            'movement_type', OLD.movement_type,
            'quantity', OLD.quantity,
            'unit_cost', OLD.unit_cost,
            'notes', OLD.notes,
            'created_by', OLD.created_by
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$


DROP TRIGGER IF EXISTS `inventory_movements_sync_after_insert` $$
CREATE TRIGGER `inventory_movements_sync_after_insert` AFTER INSERT ON `inventory_movements` FOR EACH ROW
BEGIN
    DECLARE v_effect DECIMAL(18,6) DEFAULT 0;

    SET v_effect = CASE
        WHEN NEW.movement_type = 'in' THEN NEW.quantity
        WHEN NEW.movement_type = 'out' THEN -NEW.quantity
        WHEN NEW.movement_type = 'adjustment' THEN NEW.quantity
        ELSE 0
    END;

    UPDATE raw_materials
    SET stock = COALESCE(stock,0) + v_effect
    WHERE id = NEW.raw_material_id;
END $$

DROP TRIGGER IF EXISTS `inventory_movements_sync_after_update` $$
CREATE TRIGGER `inventory_movements_sync_after_update` AFTER UPDATE ON `inventory_movements` FOR EACH ROW
BEGIN
    DECLARE v_old_effect DECIMAL(18,6) DEFAULT 0;
    DECLARE v_new_effect DECIMAL(18,6) DEFAULT 0;

    SET v_old_effect = CASE
        WHEN OLD.movement_type = 'in' THEN OLD.quantity
        WHEN OLD.movement_type = 'out' THEN -OLD.quantity
        WHEN OLD.movement_type = 'adjustment' THEN OLD.quantity
        ELSE 0
    END;

    SET v_new_effect = CASE
        WHEN NEW.movement_type = 'in' THEN NEW.quantity
        WHEN NEW.movement_type = 'out' THEN -NEW.quantity
        WHEN NEW.movement_type = 'adjustment' THEN NEW.quantity
        ELSE 0
    END;

    -- If material changed, revert old effect on OLD.raw_material_id and apply new effect on NEW.raw_material_id
    IF OLD.raw_material_id <> NEW.raw_material_id THEN
        UPDATE raw_materials
        SET stock = COALESCE(stock,0) - v_old_effect
        WHERE id = OLD.raw_material_id;

        UPDATE raw_materials
        SET stock = COALESCE(stock,0) + v_new_effect
        WHERE id = NEW.raw_material_id;
    ELSE
        -- Same material: apply delta = new - old
        UPDATE raw_materials
        SET stock = COALESCE(stock,0) + (v_new_effect - v_old_effect)
        WHERE id = NEW.raw_material_id;
    END IF;
END $$

DROP TRIGGER IF EXISTS `inventory_movements_sync_after_delete` $$
CREATE TRIGGER `inventory_movements_sync_after_delete` AFTER DELETE ON `inventory_movements` FOR EACH ROW
BEGIN
    DECLARE v_old_effect DECIMAL(18,6) DEFAULT 0;

    SET v_old_effect = CASE
        WHEN OLD.movement_type = 'in' THEN OLD.quantity
        WHEN OLD.movement_type = 'out' THEN -OLD.quantity
        WHEN OLD.movement_type = 'adjustment' THEN OLD.quantity
        ELSE 0
    END;

    -- Revert the old effect
    UPDATE raw_materials
    SET stock = COALESCE(stock,0) - v_old_effect
    WHERE id = OLD.raw_material_id;
END $$



/* ============================
   Table: forms
   ============================ */
DROP TRIGGER IF EXISTS `forms_after_insert` $$
CREATE TRIGGER `forms_after_insert` AFTER INSERT ON `forms` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, new_values, user_id, ip_address, user_agent)
    VALUES (
        'forms', NEW.id, 'INSERT',
        JSON_OBJECT(
            'name', NEW.name,
            'code', NEW.code,
            'description', NEW.description,
            'version', NEW.version,
            'created_by', NEW.created_by,
            'is_active', NEW.is_active,
            'display_order', NEW.display_order
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$

DROP TRIGGER IF EXISTS `forms_after_update` $$
CREATE TRIGGER `forms_after_update` AFTER UPDATE ON `forms` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, new_values, user_id, ip_address, user_agent)
    VALUES (
        'forms', NEW.id, 'UPDATE',
        JSON_OBJECT(
            'name', OLD.name,
            'code', OLD.code,
            'description', OLD.description,
            'version', OLD.version,
            'created_by', OLD.created_by,
            'is_active', OLD.is_active,
            'display_order', OLD.display_order
        ),
        JSON_OBJECT(
            'name', NEW.name,
            'code', NEW.code,
            'description', NEW.description,
            'version', NEW.version,
            'created_by', NEW.created_by,
            'is_active', NEW.is_active,
            'display_order', NEW.display_order
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$

DROP TRIGGER IF EXISTS `forms_after_delete` $$
CREATE TRIGGER `forms_after_delete` AFTER DELETE ON `forms` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, user_id, ip_address, user_agent)
    VALUES (
        'forms', OLD.id, 'DELETE',
        JSON_OBJECT(
            'name', OLD.name,
            'code', OLD.code,
            'description', OLD.description,
            'version', OLD.version,
            'created_by', OLD.created_by,
            'is_active', OLD.is_active,
            'display_order', OLD.display_order
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$


/* ============================
   Table: form_fields
   ============================ */
DROP TRIGGER IF EXISTS `form_fields_after_insert` $$
CREATE TRIGGER `form_fields_after_insert` AFTER INSERT ON `form_fields` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, new_values, user_id, ip_address, user_agent)
    VALUES (
        'form_fields', NEW.id, 'INSERT',
        JSON_OBJECT(
            'form_id', NEW.form_id,
            'label', NEW.label,
            'field_code', NEW.field_code,
            'type', NEW.type,
            'required', NEW.required,
            'options', NEW.options,
            'validation_rules', NEW.validation_rules,
            'field_order', NEW.field_order,
            'is_active', NEW.is_active
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$

DROP TRIGGER IF EXISTS `form_fields_after_update` $$
CREATE TRIGGER `form_fields_after_update` AFTER UPDATE ON `form_fields` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, new_values, user_id, ip_address, user_agent)
    VALUES (
        'form_fields', NEW.id, 'UPDATE',
        JSON_OBJECT(
            'form_id', OLD.form_id,
            'label', OLD.label,
            'field_code', OLD.field_code,
            'type', OLD.type,
            'required', OLD.required,
            'options', OLD.options,
            'validation_rules', OLD.validation_rules,
            'field_order', OLD.field_order,
            'is_active', OLD.is_active
        ),
        JSON_OBJECT(
            'form_id', NEW.form_id,
            'label', NEW.label,
            'field_code', NEW.field_code,
            'type', NEW.type,
            'required', NEW.required,
            'options', NEW.options,
            'validation_rules', NEW.validation_rules,
            'field_order', NEW.field_order,
            'is_active', NEW.is_active
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$

DROP TRIGGER IF EXISTS `form_fields_after_delete` $$
CREATE TRIGGER `form_fields_after_delete` AFTER DELETE ON `form_fields` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, user_id, ip_address, user_agent)
    VALUES (
        'form_fields', OLD.id, 'DELETE',
        JSON_OBJECT(
            'form_id', OLD.form_id,
            'label', OLD.label,
            'field_code', OLD.field_code,
            'type', OLD.type,
            'required', OLD.required,
            'options', OLD.options,
            'validation_rules', OLD.validation_rules,
            'field_order', OLD.field_order,
            'is_active', OLD.is_active
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$


/* ============================
   Table: form_responses
   ============================ */
DROP TRIGGER IF EXISTS `form_responses_after_insert` $$
CREATE TRIGGER `form_responses_after_insert` AFTER INSERT ON `form_responses` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, new_values, user_id, ip_address, user_agent)
    VALUES (
        'form_responses', NEW.id, 'INSERT',
        JSON_OBJECT(
            'form_id', NEW.form_id,
            'user_id', NEW.user_id,
            'batch_id', NEW.batch_id,
            'status', NEW.status,
            'submitted_at', NEW.submitted_at,
            'reviewed_by', NEW.reviewed_by,
            'reviewed_at', NEW.reviewed_at,
            'review_notes', NEW.review_notes
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$

DROP TRIGGER IF EXISTS `form_responses_after_update` $$
CREATE TRIGGER `form_responses_after_update` AFTER UPDATE ON `form_responses` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, new_values, user_id, ip_address, user_agent)
    VALUES (
        'form_responses', NEW.id, 'UPDATE',
        JSON_OBJECT(
            'form_id', OLD.form_id,
            'user_id', OLD.user_id,
            'batch_id', OLD.batch_id,
            'status', OLD.status,
            'submitted_at', OLD.submitted_at,
            'reviewed_by', OLD.reviewed_by,
            'reviewed_at', OLD.reviewed_at,
            'review_notes', OLD.review_notes
        ),
        JSON_OBJECT(
            'form_id', NEW.form_id,
            'user_id', NEW.user_id,
            'batch_id', NEW.batch_id,
            'status', NEW.status,
            'submitted_at', NEW.submitted_at,
            'reviewed_by', NEW.reviewed_by,
            'reviewed_at', NEW.reviewed_at,
            'review_notes', NEW.review_notes
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$

DROP TRIGGER IF EXISTS `form_responses_after_delete` $$
CREATE TRIGGER `form_responses_after_delete` AFTER DELETE ON `form_responses` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, user_id, ip_address, user_agent)
    VALUES (
        'form_responses', OLD.id, 'DELETE',
        JSON_OBJECT(
            'form_id', OLD.form_id,
            'user_id', OLD.user_id,
            'batch_id', OLD.batch_id,
            'status', OLD.status,
            'submitted_at', OLD.submitted_at,
            'reviewed_by', OLD.reviewed_by,
            'reviewed_at', OLD.reviewed_at,
            'review_notes', OLD.review_notes
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$


/* ============================
   Table: form_response_values
   ============================ */
DROP TRIGGER IF EXISTS `form_response_values_after_insert` $$
CREATE TRIGGER `form_response_values_after_insert` AFTER INSERT ON `form_response_values` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, new_values, user_id, ip_address, user_agent)
    VALUES (
        'form_response_values', NEW.id, 'INSERT',
        JSON_OBJECT(
            'response_id', NEW.response_id,
            'field_id', NEW.field_id,
            'value', NEW.value,
            'file_path', NEW.file_path
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$

DROP TRIGGER IF EXISTS `form_response_values_after_update` $$
CREATE TRIGGER `form_response_values_after_update` AFTER UPDATE ON `form_response_values` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, new_values, user_id, ip_address, user_agent)
    VALUES (
        'form_response_values', NEW.id, 'UPDATE',
        JSON_OBJECT(
            'response_id', OLD.response_id,
            'field_id', OLD.field_id,
            'value', OLD.value,
            'file_path', OLD.file_path
        ),
        JSON_OBJECT(
            'response_id', NEW.response_id,
            'field_id', NEW.field_id,
            'value', NEW.value,
            'file_path', NEW.file_path
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$

DROP TRIGGER IF EXISTS `form_response_values_after_delete` $$
CREATE TRIGGER `form_response_values_after_delete` AFTER DELETE ON `form_response_values` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, user_id, ip_address, user_agent)
    VALUES (
        'form_response_values', OLD.id, 'DELETE',
        JSON_OBJECT(
            'response_id', OLD.response_id,
            'field_id', OLD.field_id,
            'value', OLD.value,
            'file_path', OLD.file_path
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$


/* ============================
   Table: work_logs
   ============================ */
DROP TRIGGER IF EXISTS `work_logs_after_insert` $$
CREATE TRIGGER `work_logs_after_insert` AFTER INSERT ON `work_logs` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, new_values, user_id, ip_address, user_agent)
    VALUES (
        'work_logs', NEW.id, 'INSERT',
        JSON_OBJECT(
            'user_id', NEW.user_id,
            'date', NEW.date,
            'start_time', NEW.start_time,
            'end_time', NEW.end_time,
            'overtime_hours', NEW.overtime_hours,
            'batch_id', NEW.batch_id,
            'task_description', NEW.task_description,
            'notes', NEW.notes
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$

DROP TRIGGER IF EXISTS `work_logs_after_update` $$
CREATE TRIGGER `work_logs_after_update` AFTER UPDATE ON `work_logs` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, new_values, user_id, ip_address, user_agent)
    VALUES (
        'work_logs', NEW.id, 'UPDATE',
        JSON_OBJECT(
            'user_id', OLD.user_id,
            'date', OLD.date,
            'start_time', OLD.start_time,
            'end_time', OLD.end_time,
            'overtime_hours', OLD.overtime_hours,
            'batch_id', OLD.batch_id,
            'task_description', OLD.task_description,
            'notes', OLD.notes
        ),
        JSON_OBJECT(
            'user_id', NEW.user_id,
            'date', NEW.date,
            'start_time', NEW.start_time,
            'end_time', NEW.end_time,
            'overtime_hours', NEW.overtime_hours,
            'batch_id', NEW.batch_id,
            'task_description', NEW.task_description,
            'notes', NEW.notes
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$

DROP TRIGGER IF EXISTS `work_logs_after_delete` $$
CREATE TRIGGER `work_logs_after_delete` AFTER DELETE ON `work_logs` FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, user_id, ip_address, user_agent)
    VALUES (
        'work_logs', OLD.id, 'DELETE',
        JSON_OBJECT(
            'user_id', OLD.user_id,
            'date', OLD.date,
            'start_time', OLD.start_time,
            'end_time', OLD.end_time,
            'overtime_hours', OLD.overtime_hours,
            'batch_id', OLD.batch_id,
            'task_description', OLD.task_description,
            'notes', OLD.notes
        ),
        get_current_user_id(), get_current_ip_address(), get_current_user_agent()
    );
END $$

DELIMITER ;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
