-- Al-Khair Database Schema
-- Version: 1.0.0

-- Users table (Admin and Staff)
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `role` ENUM('admin', 'staff') NOT NULL DEFAULT 'staff',
    `phone` VARCHAR(20),
    `is_active` TINYINT(1) DEFAULT 1,
    `last_login` DATETIME,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_username` (`username`),
    INDEX `idx_email` (`email`),
    INDEX `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Donors table
CREATE TABLE IF NOT EXISTS `donors` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `donor_code` VARCHAR(20) UNIQUE NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100),
    `phone` VARCHAR(20),
    `whatsapp` VARCHAR(20),
    `address` TEXT,
    `city` VARCHAR(50),
    `district` VARCHAR(50),
    `postal_code` VARCHAR(10),
    `country` VARCHAR(50) DEFAULT 'Bangladesh',
    `notes` TEXT,
    `is_active` TINYINT(1) DEFAULT 1,
    `total_donated` DECIMAL(12, 2) DEFAULT 0.00,
    `created_by` INT UNSIGNED,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_donor_code` (`donor_code`),
    INDEX `idx_full_name` (`full_name`),
    INDEX `idx_phone` (`phone`),
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Projects table (for tracking expenses and beneficiaries)
CREATE TABLE IF NOT EXISTS `projects` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `project_code` VARCHAR(20) UNIQUE NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT,
    `start_date` DATE,
    `end_date` DATE,
    `target_amount` DECIMAL(12, 2) DEFAULT 0.00,
    `spent_amount` DECIMAL(12, 2) DEFAULT 0.00,
    `status` ENUM('planning', 'active', 'completed', 'cancelled') DEFAULT 'planning',
    `created_by` INT UNSIGNED,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_project_code` (`project_code`),
    INDEX `idx_status` (`status`),
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Beneficiaries table
CREATE TABLE IF NOT EXISTS `beneficiaries` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT UNSIGNED NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `age` INT,
    `gender` ENUM('male', 'female', 'other'),
    `address` TEXT,
    `city` VARCHAR(50),
    `district` VARCHAR(50),
    `phone` VARCHAR(20),
    `description` TEXT,
    `amount_received` DECIMAL(12, 2) DEFAULT 0.00,
    `photo_path` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_project_id` (`project_id`),
    INDEX `idx_full_name` (`full_name`),
    FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Donations table
CREATE TABLE IF NOT EXISTS `donations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `donor_id` INT UNSIGNED NOT NULL,
    `project_id` INT UNSIGNED,
    `amount` DECIMAL(12, 2) NOT NULL,
    `donation_date` DATE NOT NULL,
    `month` VARCHAR(7) NOT NULL COMMENT 'Format: YYYY-MM',
    `payment_method` ENUM('cash', 'bank_transfer', 'mobile_banking', 'check', 'other') DEFAULT 'cash',
    `transaction_id` VARCHAR(100),
    `notes` TEXT,
    `receipt_number` VARCHAR(50),
    `created_by` INT UNSIGNED,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_donor_id` (`donor_id`),
    INDEX `idx_project_id` (`project_id`),
    INDEX `idx_donation_date` (`donation_date`),
    INDEX `idx_month` (`month`),
    FOREIGN KEY (`donor_id`) REFERENCES `donors`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Project Expenses table
CREATE TABLE IF NOT EXISTS `project_expenses` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT UNSIGNED NOT NULL,
    `beneficiary_id` INT UNSIGNED,
    `expense_date` DATE NOT NULL,
    `amount` DECIMAL(12, 2) NOT NULL,
    `category` VARCHAR(100),
    `description` TEXT,
    `receipt_path` VARCHAR(255),
    `created_by` INT UNSIGNED,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_project_id` (`project_id`),
    INDEX `idx_beneficiary_id` (`beneficiary_id`),
    INDEX `idx_expense_date` (`expense_date`),
    FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`beneficiary_id`) REFERENCES `beneficiaries`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Attachments table (for documents, photos, etc.)
CREATE TABLE IF NOT EXISTS `attachments` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `entity_type` ENUM('donor', 'project', 'beneficiary', 'expense') NOT NULL,
    `entity_id` INT UNSIGNED NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `file_type` VARCHAR(50),
    `file_size` INT UNSIGNED,
    `title` VARCHAR(200),
    `description` TEXT,
    `uploaded_by` INT UNSIGNED,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_entity` (`entity_type`, `entity_id`),
    FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings table
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) UNIQUE NOT NULL,
    `setting_value` TEXT,
    `setting_type` VARCHAR(50) DEFAULT 'text',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activity Log table
CREATE TABLE IF NOT EXISTS `activity_log` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED,
    `action` VARCHAR(100) NOT NULL,
    `entity_type` VARCHAR(50),
    `entity_id` INT UNSIGNED,
    `description` TEXT,
    `ip_address` VARCHAR(45),
    `user_agent` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_created_at` (`created_at`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`) VALUES
('organization_name', 'Al-Khair Foundation', 'text'),
('organization_address', '', 'textarea'),
('organization_phone', '', 'text'),
('organization_email', '', 'email'),
('organization_website', '', 'url'),
('organization_logo', '', 'file'),
('currency_symbol', '৳', 'text'),
('currency_code', 'BDT', 'text'),
('date_format', 'd/m/Y', 'text'),
('items_per_page', '20', 'number'),
('enable_email_notifications', '1', 'boolean'),
('app_version', '1.0.0', 'text'),
('last_update_check', '', 'datetime');
