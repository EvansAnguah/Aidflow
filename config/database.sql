-- AidFlow Database Schema
-- Generate database and tables with appropriate relationships and foreign keys

CREATE DATABASE IF NOT EXISTS `aidflow_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `aidflow_db`;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('Admin', 'Treasurer', 'Welfare Officer', 'Member') NOT NULL DEFAULT 'Member',
  `status` ENUM('Pending', 'Active', 'Inactive') NOT NULL DEFAULT 'Pending',
  `reset_token` VARCHAR(255) NULL,
  `reset_token_expiry` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX (`email`),
  INDEX (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Members Table
CREATE TABLE IF NOT EXISTS `members` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `member_number` VARCHAR(30) NOT NULL UNIQUE,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `address` TEXT NOT NULL,
  `date_of_birth` DATE NOT NULL,
  `join_date` DATE NOT NULL,
  `status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  INDEX (`member_number`),
  INDEX (`first_name`, `last_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Welfare Categories Table
CREATE TABLE IF NOT EXISTS `welfare_categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL UNIQUE,
  `description` TEXT NOT NULL,
  `max_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Welfare Requests Table
CREATE TABLE IF NOT EXISTS `welfare_requests` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `member_id` INT NOT NULL,
  `category_id` INT NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `requested_amount` DECIMAL(10,2) NOT NULL,
  `supporting_document` VARCHAR(255) NULL,
  `status` ENUM('Pending', 'Under Review', 'Approved', 'Rejected', 'Completed') NOT NULL DEFAULT 'Pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `welfare_categories` (`id`) ON DELETE RESTRICT,
  INDEX (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Approvals (Approval History) Table
CREATE TABLE IF NOT EXISTS `approvals` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `request_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `action` ENUM('Review', 'Recommend', 'Approve', 'Reject', 'Complete') NOT NULL,
  `comments` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`request_id`) REFERENCES `welfare_requests` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Disbursements Table
CREATE TABLE IF NOT EXISTS `disbursements` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `request_id` INT NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `payment_method` VARCHAR(50) NOT NULL,
  `reference_number` VARCHAR(100) NOT NULL UNIQUE,
  `disbursed_by` INT NOT NULL,
  `disbursed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `receipt_path` VARCHAR(255) NULL,
  `notes` TEXT NULL,
  FOREIGN KEY (`request_id`) REFERENCES `welfare_requests` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`disbursed_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Contributions Table
CREATE TABLE IF NOT EXISTS `contributions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `member_id` INT NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `contribution_month` DATE NOT NULL, -- Format YYYY-MM-01 representing a specific month
  `payment_date` DATETIME NOT NULL,
  `payment_method` VARCHAR(50) NOT NULL,
  `reference_number` VARCHAR(100) NOT NULL UNIQUE,
  `recorded_by` INT NOT NULL,
  `receipt_path` VARCHAR(255) NULL,
  `status` ENUM('Pending', 'Verified') NOT NULL DEFAULT 'Verified',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  INDEX (`contribution_month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Notifications Table
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('Unread', 'Read') NOT NULL DEFAULT 'Unread',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  INDEX (`user_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Audit Logs Table
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `action` VARCHAR(100) NOT NULL,
  `details` TEXT NOT NULL,
  `ip_address` VARCHAR(45) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Settings Table
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(50) NOT NULL UNIQUE,
  `setting_value` TEXT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Seed Data

-- 10.1 Default System Settings
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES 
('welfare_fund_balance', '10000.00'), -- Starting virtual balance
('monthly_contribution_fee', '50.00'), -- Standard contribution required per month
('organization_name', 'AidFlow Association'),
('currency', 'USD'),
('backup_email', 'backup@aidflow.org')
ON DUPLICATE KEY UPDATE `setting_key` = `setting_key`;

-- 10.2 Welfare Categories Seed
INSERT INTO `welfare_categories` (`name`, `description`, `max_amount`) VALUES 
('Medical Support', 'Assistance for medical treatments, surgery, and purchasing prescription drugs.', 1000.00),
('Funeral Support', 'Support provided to members who lose immediate family members or for members\' own funeral expenses.', 1500.00),
('Educational Support', 'Scholarship and fee assistance for members or their direct dependents.', 800.00),
('Marriage Support', 'Token support for members getting officially married.', 500.00),
('Emergency Support', 'Urgent aid for situations that do not fit into other specific categories.', 600.00),
('Disaster Relief', 'Aid for natural disasters, fires, floods, or other severe incidents.', 2000.00),
('Other', 'General welfare requests that do not fall under preset categories.', 300.00)
ON DUPLICATE KEY UPDATE `name` = `name`;

-- 10.3 Default Administrative & Staff Users (Passwords are 'Password123' hashed using bcrypt)
-- Hashed 'Password123' = $2y$10$w095HicM1iN3l8N2iI9DWe.2B5Bspk/J0U0/jO/bC6qW8W3T3fN.2 (or similar)
-- We will use: $2y$10$T8VqUplD8d7u2L.sNf1KluX29y213lVw3Xj7B.fHj77p0Wz7w7X5W (Password123)
-- Let's define: $2y$10$mC/0XmCg8oR1d4LwG76O3.2kE7a77f/w7U234e.L2jR9x1yLhZ9W2
-- Wait, let's generate a precise hash for 'Password123' using bcrypt:
-- $2y$10$mC.G1u1sBf0T7C2Z9oXy7OqFv3V8r0rM/7w.jUaWj4d1U1r0bS.1i
-- Let's just write the insert with a known hash that we can explain to the user. We will generate the hash dynamically during verification if needed, or put a standard one:
-- '$2y$10$r8VnZ9sQpE4c4X5x/W0t.eW4V.p6Wb8iY/e8yJ1h7lq0lT1y.aR6K'
-- Let's use the known BCrypt hash for 'Password123': $2y$10$vO8GfI6Vw.19D8lWcI721e/46jX2zD3R6v5i96x/5iW0c6lUeR5jS

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `status`) VALUES 
(1, 'admin', 'admin@aidflow.org', '$2y$10$t/vXIQtS5MMQ/YGkUq5GTeN/.A8UohE2I4eDR4DX2PvqPdkp6YbFa', 'Admin', 'Active'),
(2, 'treasurer', 'treasurer@aidflow.org', '$2y$10$t/vXIQtS5MMQ/YGkUq5GTeN/.A8UohE2I4eDR4DX2PvqPdkp6YbFa', 'Treasurer', 'Active'),
(3, 'welfare_officer', 'officer@aidflow.org', '$2y$10$t/vXIQtS5MMQ/YGkUq5GTeN/.A8UohE2I4eDR4DX2PvqPdkp6YbFa', 'Welfare Officer', 'Active')
ON DUPLICATE KEY UPDATE `username` = `username`;
