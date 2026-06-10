-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Generation Time: Jun 09, 2026 at 04:01 PM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aidflow_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `approvals`
--

DROP TABLE IF EXISTS `approvals`;
CREATE TABLE IF NOT EXISTS `approvals` (
  `id` int NOT NULL AUTO_INCREMENT,
  `request_id` int NOT NULL,
  `user_id` int NOT NULL,
  `action` enum('Review','Recommend','Approve','Reject','Complete') COLLATE utf8mb4_unicode_ci NOT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `request_id` (`request_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `created_at`) VALUES
(1, NULL, 'Register', 'User registered: Gwallaz as Member', '::1', '2026-05-31 12:42:01'),
(2, NULL, 'Create Profile', 'Created member profile for MEM-2026-0001', '::1', '2026-05-31 12:42:01'),
(3, 1, 'Update Setting', 'Updated setting \'backup_email\' to \'backup@aidflow.org\'', '::1', '2026-05-31 13:22:46'),
(4, 1, 'Update Setting', 'Updated setting \'currency\' to \'GHC\'', '::1', '2026-05-31 13:22:46'),
(5, 1, 'Update Setting', 'Updated setting \'monthly_contribution_fee\' to \'50.00\'', '::1', '2026-05-31 13:22:46'),
(6, 1, 'Update Setting', 'Updated setting \'organization_name\' to \'AidFlow Association\'', '::1', '2026-05-31 13:22:46'),
(7, 1, 'Update Setting', 'Updated setting \'welfare_fund_balance\' to \'0.00\'', '::1', '2026-05-31 13:22:46'),
(8, 1, 'Database Backup', 'System database backup downloaded.', '::1', '2026-05-31 13:22:59'),
(9, 1, 'Update User Status', 'Updated user ID 4 status to Active', '::1', '2026-05-31 13:23:49'),
(10, 1, 'Update User Status', 'Updated user ID 4 status to Active', '::1', '2026-05-31 13:23:55'),
(11, 1, 'Update User Status', 'Updated user ID 4 status to Active', '::1', '2026-05-31 13:41:26'),
(12, NULL, 'Update Profile', 'Updated member profile ID 1', '::1', '2026-05-31 14:02:47'),
(13, NULL, 'Register', 'User registered: Collins as Member', '::1', '2026-05-31 15:08:54'),
(14, NULL, 'Create Profile', 'Created member profile for MEM-2026-0002', '::1', '2026-05-31 15:08:54'),
(15, 1, 'Update Profile', 'Updated member profile ID 2', '::1', '2026-05-31 15:58:03'),
(16, 1, 'Update User Status', 'Updated user ID 5 status to Active', '::1', '2026-05-31 16:05:28'),
(17, 1, 'Update User Status', 'Updated user ID 5 status to Active', '::1', '2026-05-31 16:05:34'),
(18, NULL, 'Update Profile', 'Updated member profile ID 2', '::1', '2026-05-31 22:26:20'),
(20, 1, 'Update User Status', 'Updated user ID 5 status to Inactive', '::1', '2026-06-01 09:21:40'),
(21, 1, 'Update User Status', 'Updated user ID 4 status to Inactive', '::1', '2026-06-01 09:21:53'),
(22, NULL, 'Register', 'User registered: Joaking as Member', '::1', '2026-06-01 09:24:39'),
(23, NULL, 'Create Profile', 'Created member profile for MEM-2026-0003', '::1', '2026-06-01 09:24:39'),
(24, 7, 'Register', 'User registered: Joaking as Member', '::1', '2026-06-09 13:56:58'),
(25, 7, 'Create Profile', 'Created member profile for MEM-2026-0001', '::1', '2026-06-09 13:56:58');

-- --------------------------------------------------------

--
-- Table structure for table `contributions`
--

DROP TABLE IF EXISTS `contributions`;
CREATE TABLE IF NOT EXISTS `contributions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `member_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `contribution_month` date NOT NULL,
  `payment_date` datetime NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recorded_by` int NOT NULL,
  `receipt_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Pending','Verified') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Verified',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference_number` (`reference_number`),
  KEY `member_id` (`member_id`),
  KEY `recorded_by` (`recorded_by`),
  KEY `contribution_month` (`contribution_month`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `disbursements`
--

DROP TABLE IF EXISTS `disbursements`;
CREATE TABLE IF NOT EXISTS `disbursements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `request_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `disbursed_by` int NOT NULL,
  `disbursed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `receipt_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference_number` (`reference_number`),
  KEY `request_id` (`request_id`),
  KEY `disbursed_by` (`disbursed_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
CREATE TABLE IF NOT EXISTS `members` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `member_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_of_birth` date NOT NULL,
  `join_date` date NOT NULL,
  `status` enum('Active','Inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `member_number` (`member_number`),
  KEY `user_id` (`user_id`),
  KEY `member_number_2` (`member_number`),
  KEY `first_name` (`first_name`,`last_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Unread','Read') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Unread',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `status`, `created_at`) VALUES
(1, 1, 'New Registration Request', 'Member Collins Blay (Collins) has registered and is pending approval.', 'Read', '2026-05-31 15:09:05'),
(4, 1, 'New Registration Request', 'Member Joaking Blay (Joaking) has registered and is pending approval.', 'Read', '2026-06-01 09:24:42'),
(5, 1, 'New Registration Request', 'Member Joaking Blay (Joaking) has registered and is pending approval.', 'Unread', '2026-06-09 13:56:58');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'welfare_fund_balance', '0.00', '2026-05-31 13:22:46'),
(2, 'monthly_contribution_fee', '50.00', '2026-05-31 11:22:34'),
(3, 'organization_name', 'AidFlow Association', '2026-05-31 11:22:34'),
(4, 'currency', 'GHC', '2026-05-31 13:22:46'),
(5, 'backup_email', 'backup@aidflow.org', '2026-05-31 11:22:34');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('Admin','Treasurer','Welfare Officer','Member') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Member',
  `status` enum('Pending','Active','Inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `reset_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `email_2` (`email`),
  KEY `role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `status`, `reset_token`, `reset_token_expiry`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@aidflow.org', '$2y$10$t/vXIQtS5MMQ/YGkUq5GTeN/.A8UohE2I4eDR4DX2PvqPdkp6YbFa', 'Admin', 'Active', NULL, NULL, '2026-05-31 11:22:34', '2026-05-31 11:22:34'),
(2, 'treasurer', 'treasurer@aidflow.org', '$2y$10$t/vXIQtS5MMQ/YGkUq5GTeN/.A8UohE2I4eDR4DX2PvqPdkp6YbFa', 'Treasurer', 'Active', NULL, NULL, '2026-05-31 11:22:34', '2026-05-31 11:22:34'),
(3, 'welfare_officer', 'officer@aidflow.org', '$2y$10$t/vXIQtS5MMQ/YGkUq5GTeN/.A8UohE2I4eDR4DX2PvqPdkp6YbFa', 'Welfare Officer', 'Active', NULL, NULL, '2026-05-31 11:22:34', '2026-05-31 11:22:34'),
(7, 'Joaking', 'joaking@gmail.com', '$2y$10$T253PiDZSeimxsuDtaj4POXo5p.y88YPr5y14Q2moRjJAzsBgJd..', 'Member', 'Pending', NULL, NULL, '2026-06-09 13:56:58', '2026-06-09 13:56:58');

-- --------------------------------------------------------

--
-- Table structure for table `welfare_categories`
--

DROP TABLE IF EXISTS `welfare_categories`;
CREATE TABLE IF NOT EXISTS `welfare_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `max_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `welfare_categories`
--

INSERT INTO `welfare_categories` (`id`, `name`, `description`, `max_amount`, `created_at`) VALUES
(1, 'Medical Support', 'Assistance for medical treatments, surgery, and purchasing prescription drugs.', 1000.00, '2026-05-31 11:22:34'),
(2, 'Funeral Support', 'Support provided to members who lose immediate family members or for members\' own funeral expenses.', 1500.00, '2026-05-31 11:22:34'),
(3, 'Educational Support', 'Scholarship and fee assistance for members or their direct dependents.', 800.00, '2026-05-31 11:22:34'),
(4, 'Marriage Support', 'Token support for members getting officially married.', 500.00, '2026-05-31 11:22:34'),
(5, 'Emergency Support', 'Urgent aid for situations that do not fit into other specific categories.', 600.00, '2026-05-31 11:22:34'),
(6, 'Disaster Relief', 'Aid for natural disasters, fires, floods, or other severe incidents.', 2000.00, '2026-05-31 11:22:34'),
(7, 'Other', 'General welfare requests that do not fall under preset categories.', 300.00, '2026-05-31 11:22:34');

-- --------------------------------------------------------

--
-- Table structure for table `welfare_requests`
--

DROP TABLE IF EXISTS `welfare_requests`;
CREATE TABLE IF NOT EXISTS `welfare_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `member_id` int NOT NULL,
  `category_id` int NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `requested_amount` decimal(10,2) NOT NULL,
  `supporting_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Pending','Under Review','Approved','Rejected','Completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `category_id` (`category_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `approvals`
--
ALTER TABLE `approvals`
  ADD CONSTRAINT `approvals_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `welfare_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `approvals_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `contributions`
--
ALTER TABLE `contributions`
  ADD CONSTRAINT `contributions_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contributions_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `disbursements`
--
ALTER TABLE `disbursements`
  ADD CONSTRAINT `disbursements_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `welfare_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `disbursements_ibfk_2` FOREIGN KEY (`disbursed_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `welfare_requests`
--
ALTER TABLE `welfare_requests`
  ADD CONSTRAINT `welfare_requests_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `welfare_requests_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `welfare_categories` (`id`) ON DELETE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
