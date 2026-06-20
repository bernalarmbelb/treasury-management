-- MySQL / MariaDB dump generated from SQLite
-- Source: database/database.sqlite
-- Generated: 2026-06-20T12:47:13

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT NULL,
  `action` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `created_at`, `updated_at`) VALUES
(1, 1, 'User Management - Add User', '2023-01-14 09:00:00', '2023-01-14 09:00:00'),
(2, 1, 'Collection Management - Add Entry', '2023-03-02 10:30:00', '2023-03-02 10:30:00'),
(3, 1, 'Collection Management - Add Entry', '2023-08-19 11:15:00', '2023-08-19 11:15:00'),
(4, 1, 'Collection Management - Add Entry', '2024-02-05 14:20:00', '2024-02-05 14:20:00'),
(5, 1, 'User Management - Add User', '2024-06-17 09:45:00', '2024-06-17 09:45:00'),
(6, 1, 'Collection Management - Add Entry', '2024-11-30 13:10:00', '2024-11-30 13:10:00'),
(7, 1, 'User Management - Add User', '2025-04-12 08:30:00', '2025-04-12 08:30:00'),
(8, 1, 'Collection Management - Add Entry', '2025-09-25 16:00:00', '2025-09-25 16:00:00'),
(9, 1, 'User Management - Activate User', '2026-06-15 23:37:48', '2026-06-15 23:37:48'),
(10, 1, 'User Management - Disable User', '2026-06-15 23:37:52', '2026-06-15 23:37:52'),
(11, 1, 'User Management - Activate User', '2026-06-15 23:44:04', '2026-06-15 23:44:04'),
(12, 1, 'User Management - Disable User', '2026-06-15 23:44:11', '2026-06-15 23:44:11'),
(13, 1, 'User Management - Add User', '2026-06-15 23:48:29', '2026-06-15 23:48:29'),
(14, 1, 'User Management - Activate User', '2026-06-15 23:48:40', '2026-06-15 23:48:40'),
(15, 1, 'User Management - Edit User', '2026-06-15 23:51:07', '2026-06-15 23:51:07'),
(16, 1, 'User Management - Reset Password', '2026-06-15 23:52:09', '2026-06-15 23:52:09'),
(17, 1, 'User Management - Disable User', '2026-06-15 23:53:19', '2026-06-15 23:53:19'),
(18, 1, 'User Management - Activate User', '2026-06-15 23:54:03', '2026-06-15 23:54:03'),
(19, 1, 'User Management - Change Permission', '2026-06-15 23:58:22', '2026-06-15 23:58:22'),
(20, 1, 'User Management - Change Permission', '2026-06-15 23:58:30', '2026-06-15 23:58:30'),
(21, 1, 'User Management - Change Permission', '2026-06-15 23:58:58', '2026-06-15 23:58:58'),
(22, 1, 'User Management - Change Permission', '2026-06-15 23:59:33', '2026-06-15 23:59:33'),
(23, 1, 'User Management - Change Permission', '2026-06-15 23:59:57', '2026-06-15 23:59:57'),
(24, 1, 'User Management - Change Permission', '2026-06-16 00:00:59', '2026-06-16 00:00:59'),
(25, 1, 'User Management - Change Permission', '2026-06-16 00:01:40', '2026-06-16 00:01:40'),
(26, 1, 'User Management - Change Permission', '2026-06-16 00:02:05', '2026-06-16 00:02:05'),
(27, 1, 'User Management - Archive User', '2026-06-16 00:06:39', '2026-06-16 00:06:39'),
(28, 1, 'User Management - Change Permission', '2026-06-16 00:21:10', '2026-06-16 00:21:10'),
(29, 1, 'User Management - Disable User', '2026-06-16 00:34:49', '2026-06-16 00:34:49'),
(30, 1, 'User Management - Disable User', '2026-06-16 00:34:53', '2026-06-16 00:34:53'),
(31, 1, 'User Management - Disable User', '2026-06-16 00:41:20', '2026-06-16 00:41:20'),
(32, 1, 'User Management - Disable User', '2026-06-16 00:41:21', '2026-06-16 00:41:21'),
(33, 1, 'User Management - Disable User', '2026-06-16 00:41:23', '2026-06-16 00:41:23'),
(34, 1, 'User Management - Activate User', '2026-06-16 00:41:25', '2026-06-16 00:41:25'),
(35, 1, 'User Management - Disable User', '2026-06-16 00:41:28', '2026-06-16 00:41:28'),
(36, 1, 'User Management - Disable User', '2026-06-16 00:41:33', '2026-06-16 00:41:33'),
(37, 1, 'User Management - Activate User', '2026-06-16 00:45:36', '2026-06-16 00:45:36'),
(38, 1, 'User Management - Activate User', '2026-06-16 00:45:36', '2026-06-16 00:45:36'),
(39, 1, 'User Management - Disable User', '2026-06-16 00:45:43', '2026-06-16 00:45:43'),
(40, 1, 'User Management - Disable User', '2026-06-16 00:45:43', '2026-06-16 00:45:43'),
(41, 1, 'User Management - Add User', '2026-06-16 00:46:58', '2026-06-16 00:46:58'),
(42, 1, 'User Management - Change Permission', '2026-06-16 00:52:54', '2026-06-16 00:52:54'),
(43, 1, 'User Management - Change Permission', '2026-06-16 00:53:06', '2026-06-16 00:53:06'),
(44, 1, 'User Management - Change Permission', '2026-06-16 00:53:57', '2026-06-16 00:53:57'),
(45, 1, 'User Management - Change Permission', '2026-06-16 00:54:46', '2026-06-16 00:54:46'),
(46, 1, 'User Management - Change Permission', '2026-06-16 00:54:59', '2026-06-16 00:54:59'),
(47, 1, 'User Management - Change Permission', '2026-06-16 00:55:09', '2026-06-16 00:55:09'),
(48, 1, 'User Management - Disable User', '2026-06-16 01:38:02', '2026-06-16 01:38:02'),
(49, 1, 'User Management - Activate User', '2026-06-16 01:39:22', '2026-06-16 01:39:22'),
(50, 1, 'User Management - Disable User', '2026-06-16 02:27:03', '2026-06-16 02:27:03'),
(51, 1, 'User Management - Change Permission', '2026-06-16 03:22:24', '2026-06-16 03:22:24'),
(52, 1, 'User Management - Change Permission', '2026-06-16 03:22:53', '2026-06-16 03:22:53'),
(53, 10, 'User Management - Disable User', '2026-06-16 07:01:00', '2026-06-16 07:01:00'),
(56, 10, 'Collection Management - Add Entry', '2026-06-16 07:45:07', '2026-06-16 07:45:07'),
(57, 10, 'Collection Management - Add Entry', '2026-06-16 08:34:03', '2026-06-16 08:34:03'),
(58, 10, 'Collection Management - Add Entry', '2026-06-16 08:43:02', '2026-06-16 08:43:02'),
(59, 10, 'Collection Management - Add Entry', '2026-06-16 08:49:20', '2026-06-16 08:49:20'),
(60, 10, 'Collection Management - Add Entry', '2026-06-16 09:06:26', '2026-06-16 09:06:26'),
(61, 10, 'User Management - Reset Password', '2026-06-16 12:27:38', '2026-06-16 12:27:38'),
(62, 10, 'User Management - Activate User', '2026-06-16 12:28:15', '2026-06-16 12:28:15'),
(63, 1, 'User Management - Reset Password', '2026-06-16 12:28:41', '2026-06-16 12:28:41'),
(64, 1, 'User Management - Archive User', '2026-06-16 12:30:43', '2026-06-16 12:30:43'),
(65, 1, 'User Management - Add User', '2026-06-16 12:32:24', '2026-06-16 12:32:24'),
(66, 1, 'Collection Management - Add Entry', '2026-06-16 12:35:17', '2026-06-16 12:35:17'),
(67, 1, 'Collection Management - Add Entry', '2026-06-16 12:41:25', '2026-06-16 12:41:25'),
(68, 1, 'Collection Management - Add Entry', '2026-06-16 12:46:12', '2026-06-16 12:46:12'),
(69, 1, 'Collection Management - Add Entry', '2026-06-16 12:57:05', '2026-06-16 12:57:05'),
(70, 1, 'Collection Management - Add Entry', '2026-06-16 12:59:27', '2026-06-16 12:59:27'),
(71, 1, 'Collection Management - Add Entry', '2026-06-16 13:16:21', '2026-06-16 13:16:21'),
(72, 1, 'Collection Management - Add Entry', '2026-06-16 13:26:34', '2026-06-16 13:26:34'),
(73, 1, 'Collection Management - Add Entry', '2026-06-16 13:42:54', '2026-06-16 13:42:54'),
(74, 1, 'Collection Management - Add Entry', '2026-06-16 13:47:57', '2026-06-16 13:47:57'),
(75, 12, 'User Management - Change Permission', '2026-06-17 16:58:19', '2026-06-17 16:58:19'),
(76, 12, 'Collection Management - Add Entry', '2026-06-17 17:18:17', '2026-06-17 17:18:17'),
(77, 12, 'Collection Management - Add Entry', '2026-06-17 21:00:20', '2026-06-17 21:00:20'),
(78, 12, 'Collection Management - Add Entry', '2026-06-17 23:43:28', '2026-06-17 23:43:28'),
(79, 12, 'Collection Management - Add Entry', '2026-06-17 23:55:16', '2026-06-17 23:55:16'),
(80, 12, 'Collection Management - Add Entry', '2026-06-18 00:15:33', '2026-06-18 00:15:33'),
(81, 12, 'Collection Management - Add Entry', '2026-06-18 00:38:14', '2026-06-18 00:38:14'),
(82, 12, 'Collection Management - Add Entry', '2026-06-18 00:40:27', '2026-06-18 00:40:27'),
(83, 12, 'User Management - Change Permission', '2026-06-18 00:46:20', '2026-06-18 00:46:20'),
(84, 12, 'Collection Management - Add Entry', '2026-06-18 00:51:31', '2026-06-18 00:51:31'),
(85, 12, 'Collection Management - Cancel Transaction', '2026-06-19 00:49:33', '2026-06-19 00:49:33'),
(86, 12, 'Collection Management - Cancel Transaction', '2026-06-19 00:49:45', '2026-06-19 00:49:45'),
(87, 12, 'Collection Management - Cancel Transaction', '2026-06-19 00:50:12', '2026-06-19 00:50:12'),
(88, 12, 'Collection Management - Cancel Transaction', '2026-06-19 00:55:37', '2026-06-19 00:55:37'),
(89, 12, 'Collection Management - Cancel Transaction', '2026-06-19 00:57:17', '2026-06-19 00:57:17'),
(90, 12, 'Collection Management - Cancel Transaction', '2026-06-19 00:57:27', '2026-06-19 00:57:27'),
(91, 12, 'Collection Management - Bulk Cancel Transaction', '2026-06-19 01:01:32', '2026-06-19 01:01:32'),
(92, 12, 'Collection Management - Bulk Archive Transaction', '2026-06-19 01:07:07', '2026-06-19 01:07:07'),
(93, 12, 'Collection Management - Archive Transaction', '2026-06-19 01:11:41', '2026-06-19 01:11:41'),
(94, 12, 'Collection Management - Bulk Archive Transaction', '2026-06-19 01:15:42', '2026-06-19 01:15:42'),
(95, 12, 'Collection Management - Bulk Archive Transaction', '2026-06-19 01:22:31', '2026-06-19 01:22:31'),
(96, 12, 'Collection Management - Cancel Transaction', '2026-06-19 01:22:37', '2026-06-19 01:22:37'),
(97, 12, 'User Management - Activate User', '2026-06-19 01:28:19', '2026-06-19 01:28:19'),
(98, 12, 'User Management - Change Permission', '2026-06-19 01:29:26', '2026-06-19 01:29:26'),
(99, 12, 'User Management - Change Permission', '2026-06-19 01:29:29', '2026-06-19 01:29:29'),
(100, 8, 'Collection Management - Request Cancel', '2026-06-19 01:32:22', '2026-06-19 01:32:22'),
(101, 8, 'Collection Management - Request Cancel', '2026-06-19 01:40:07', '2026-06-19 01:40:07'),
(102, 8, 'Collection Management - Request Cancel', '2026-06-19 01:40:58', '2026-06-19 01:40:58'),
(103, 12, 'Collection Management - Reject Cancel Request', '2026-06-19 01:52:15', '2026-06-19 01:52:15'),
(104, 12, 'Collection Management - Reject Cancel Request', '2026-06-19 01:58:03', '2026-06-19 01:58:03'),
(105, 12, 'User Management - Unarchive User', '2026-06-19 02:32:23', '2026-06-19 02:32:23'),
(106, 12, 'User Management - Archive User', '2026-06-19 02:32:38', '2026-06-19 02:32:38'),
(107, 12, 'Collection Management - Reject Cancel Request', '2026-06-19 13:17:05', '2026-06-19 13:17:05'),
(108, 8, 'Collection Management - Request Cancel - Marriage License - Armbel Besalo Bernal & Cleofe Dioneda Villanueva - No. 2027024', '2026-06-19 13:27:26', '2026-06-19 13:27:26'),
(109, 12, 'Collection Management - Reject Cancel Request - Marriage License - Armbel Besalo Bernal & Cleofe Dioneda Villanueva - No. 2027024 - requested by Ramon Torres', '2026-06-19 13:28:27', '2026-06-19 13:28:27'),
(110, 12, 'Reporting & Abstract - Export Report - Consolidated Report of Accountability for Accountable Forms (CRAAF) - June 2026', '2026-06-19 17:32:33', '2026-06-19 17:32:33'),
(111, 1, 'Reporting & Abstract - Export Report - Consolidated Report of Accountability for Accountable Forms (CRAAF) - June 2026', '2026-06-19 17:37:37', '2026-06-19 17:37:37'),
(112, 12, 'Reporting & Abstract - Export Report - Treasurer\'s Monthly Report of Accountability for Accountable Forms - June 2026', '2026-06-19 17:38:02', '2026-06-19 17:38:02'),
(113, 12, 'Reporting & Abstract - Export Report - Treasurer\'s Monthly Report of Accountability for Accountable Forms - June 2026', '2026-06-19 17:38:24', '2026-06-19 17:38:24'),
(114, 12, 'Reporting & Abstract - Export Report - Treasurer\'s Monthly Report of Accountability for Accountable Forms - June 2026', '2026-06-19 19:26:14', '2026-06-19 19:26:14'),
(115, 12, 'Reporting & Abstract - Export Report - Treasurer\'s Monthly Report of Accountability for Accountable Forms - June 2026', '2026-06-19 19:26:18', '2026-06-19 19:26:18'),
(116, 1, 'Reporting & Abstract - Export Report - Treasurer\'s Monthly Report of Accountability for Accountable Forms - June 2026', '2026-06-19 19:27:26', '2026-06-19 19:27:26'),
(117, 1, 'Reporting & Abstract - Export Report - Treasurer\'s Monthly Report of Accountability for Accountable Forms - June 2026', '2026-06-19 19:29:19', '2026-06-19 19:29:19'),
(118, 12, 'Reporting & Abstract - Export Report - Treasurer\'s Monthly Report of Accountability for Accountable Forms - June 2026', '2026-06-19 19:29:59', '2026-06-19 19:29:59'),
(119, 1, 'Reporting & Abstract - Export Report - Treasurer\'s Monthly Report of Accountability for Accountable Forms - June 2026', '2026-06-19 19:29:59', '2026-06-19 19:29:59'),
(120, 1, 'Reporting & Abstract - Export Report - Treasurer\'s Monthly Report of Accountability for Accountable Forms - June 2026', '2026-06-19 19:30:26', '2026-06-19 19:30:26'),
(121, 12, 'Reporting & Abstract - Export Report - Treasurer\'s Monthly Report of Accountability for Accountable Forms - June 2026', '2026-06-19 19:45:29', '2026-06-19 19:45:29'),
(122, 12, 'Reporting & Abstract - Export Report - Treasurer\'s Monthly Report of Accountability for Accountable Forms - June 2026', '2026-06-19 19:45:54', '2026-06-19 19:45:54'),
(123, 12, 'Reporting & Abstract - Export Report - Treasurer\'s Monthly Report of Accountability for Accountable Forms - June 2026', '2026-06-19 19:46:04', '2026-06-19 19:46:04'),
(124, 12, 'Reporting & Abstract - Export Report - Treasurer\'s Monthly Report of Accountability for Accountable Forms - June 2026', '2026-06-19 19:46:10', '2026-06-19 19:46:10'),
(125, 12, 'Reporting & Abstract - Export Report - Treasurer\'s Monthly Report of Accountability for Accountable Forms - June 2026', '2026-06-19 19:53:36', '2026-06-19 19:53:36'),
(126, 12, 'Reporting & Abstract - Export Report - Treasurer\'s Monthly Report of Accountability for Accountable Forms - June 2026', '2026-06-19 19:53:50', '2026-06-19 19:53:50'),
(127, 12, 'Reporting & Abstract - Export Report - Treasurer\'s Monthly Report of Accountability for Accountable Forms - June 2026', '2026-06-19 20:02:05', '2026-06-19 20:02:05'),
(128, 12, 'Reporting & Abstract - Export Report - Treasurer\'s Monthly Report of Accountability for Accountable Forms - June 2026', '2026-06-19 20:05:32', '2026-06-19 20:05:32'),
(129, 12, 'User Management - Activate User - Carmen Lopez', '2026-06-19 22:32:04', '2026-06-19 22:32:04');

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` VARCHAR(255) NOT NULL,
  `value` TEXT NOT NULL,
  `expiration` BIGINT NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- (runtime table `cache` left empty)

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` VARCHAR(255) NOT NULL,
  `owner` VARCHAR(255) NOT NULL,
  `expiration` BIGINT NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- (runtime table `cache_locks` left empty)

DROP TABLE IF EXISTS `cancel_requests`;
CREATE TABLE `cancel_requests` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `transaction_log_id` BIGINT NOT NULL,
  `requested_by` BIGINT NULL,
  `reason` TEXT NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'pending',
  `reviewed_by` BIGINT NULL,
  `reviewed_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `notified_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cancel_requests` (`id`, `transaction_log_id`, `requested_by`, `reason`, `status`, `reviewed_by`, `reviewed_at`, `created_at`, `updated_at`, `notified_at`) VALUES
(1, 120, 8, NULL, 'rejected', 12, '2026-06-19 01:58:03', '2026-06-19 01:32:22', '2026-06-19 01:58:19', '2026-06-19 01:58:19'),
(2, 15, 8, 'There are so many reasons to cancel', 'rejected', 12, '2026-06-19 13:17:05', '2026-06-19 01:40:07', '2026-06-19 13:28:35', '2026-06-19 13:28:35'),
(3, 119, 8, NULL, 'rejected', 12, '2026-06-19 01:52:15', '2026-06-19 01:40:58', '2026-06-19 01:52:24', '2026-06-19 01:52:24'),
(4, 107, 8, 'For testing some modules.', 'rejected', 12, '2026-06-19 13:28:27', '2026-06-19 13:27:25', '2026-06-19 13:28:35', '2026-06-19 13:28:35');

DROP TABLE IF EXISTS `ctc_corporation_transactions`;
CREATE TABLE `ctc_corporation_transactions` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `form_stock_id` BIGINT NOT NULL,
  `certificate_number` VARCHAR(255) NOT NULL,
  `year` BIGINT NOT NULL,
  `place_of_issue` VARCHAR(255) NULL,
  `date_issued` DATE NULL,
  `company_name` VARCHAR(255) NOT NULL,
  `tin` VARCHAR(255) NULL,
  `date_of_registration` DATE NULL,
  `address` VARCHAR(255) NULL,
  `kind_of_organization` VARCHAR(255) NULL,
  `nature_of_business` VARCHAR(255) NULL,
  `a_community_tax_due` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `item1_taxable_amount` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `item1_community_tax_due` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `item2_taxable_amount` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `item2_community_tax_due` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `total_community_tax_due` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `interest` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `amount_paid` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `amount_in_words` VARCHAR(255) NULL,
  `treasurer_name` VARCHAR(255) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `certificate_prefix` VARCHAR(255) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ctc_corporation_transactions` (`id`, `form_stock_id`, `certificate_number`, `year`, `place_of_issue`, `date_issued`, `company_name`, `tin`, `date_of_registration`, `address`, `kind_of_organization`, `nature_of_business`, `a_community_tax_due`, `item1_taxable_amount`, `item1_community_tax_due`, `item2_taxable_amount`, `item2_community_tax_due`, `total_community_tax_due`, `interest`, `amount_paid`, `amount_in_words`, `treasurer_name`, `created_at`, `updated_at`, `certificate_prefix`) VALUES
(1, 2, '00259338', 2026, NULL, NULL, 'Acme Corporation', '', NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 500, 'Five hundred pesos only', 'Gemma D. Ferrer', '2026-06-13 13:08:37', '2026-06-13 13:08:37', NULL),
(5, 2, '00123456', 2026, 'Prieto-Diaz, Sorsogon City, Sorsogon', NULL, 'SOLEM IT & Digital Solutions Corporation', '123456789011000', '2026-06-13 00:00:00', NULL, 'Corporation', NULL, 1, 1, 1, 0, 1, 3, 1, 4, 'Four Pesos Only', 'Gemma D. Ferrer', '2026-06-13 14:37:47', '2026-06-13 14:37:47', NULL);

DROP TABLE IF EXISTS `ctc_individual_transactions`;
CREATE TABLE `ctc_individual_transactions` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `form_stock_id` BIGINT NOT NULL,
  `certificate_number` VARCHAR(255) NOT NULL,
  `year` BIGINT NOT NULL,
  `place_of_issue` VARCHAR(255) NULL,
  `date_issued` DATE NULL,
  `date_issued_2` DATE NULL,
  `surname` VARCHAR(255) NOT NULL,
  `first_name` VARCHAR(255) NOT NULL,
  `middle_name` VARCHAR(255) NULL,
  `tin` VARCHAR(255) NULL,
  `sex` VARCHAR(255) NULL,
  `citizenship` VARCHAR(255) NULL,
  `icr_no` VARCHAR(255) NULL,
  `place_of_birth` VARCHAR(255) NULL,
  `height` VARCHAR(255) NULL,
  `civil_status` VARCHAR(255) NULL,
  `weight` VARCHAR(255) NULL,
  `date_of_birth` DATE NULL,
  `profession` VARCHAR(255) NULL,
  `a_community_tax_due` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `item1_taxable_amount` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `item1_community_tax_due` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `item2_taxable_amount` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `item2_community_tax_due` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `item3_taxable_amount` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `item3_community_tax_due` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `total_community_tax_due` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `interest` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `amount_paid` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `amount_in_words` VARCHAR(255) NULL,
  `treasurer_name` VARCHAR(255) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `certificate_prefix` VARCHAR(255) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ctc_individual_transactions` (`id`, `form_stock_id`, `certificate_number`, `year`, `place_of_issue`, `date_issued`, `date_issued_2`, `surname`, `first_name`, `middle_name`, `tin`, `sex`, `citizenship`, `icr_no`, `place_of_birth`, `height`, `civil_status`, `weight`, `date_of_birth`, `profession`, `a_community_tax_due`, `item1_taxable_amount`, `item1_community_tax_due`, `item2_taxable_amount`, `item2_community_tax_due`, `item3_taxable_amount`, `item3_community_tax_due`, `total_community_tax_due`, `interest`, `amount_paid`, `amount_in_words`, `treasurer_name`, `created_at`, `updated_at`, `certificate_prefix`) VALUES
(1, 1, '13476955', 2026, NULL, NULL, NULL, 'Dela Cruz', 'Juan', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 500, 'Five hundred pesos only', 'Gemma D. Ferrer', '2026-06-13 11:49:36', '2026-06-13 11:49:36', NULL),
(2, 1, '13476955', 2026, NULL, NULL, NULL, 'Dela Cruz', 'Juan', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 500, 'Five hundred pesos only', 'Gemma D. Ferrer', '2026-06-13 11:50:07', '2026-06-13 11:50:07', NULL),
(3, 1, '13476955', 2026, NULL, NULL, NULL, 'Dela Cruz', 'Juan', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 500, 'Five hundred pesos only', 'Gemma D. Ferrer', '2026-06-13 11:51:22', '2026-06-13 11:51:22', NULL),
(4, 1, '13476955', 2026, NULL, NULL, NULL, 'Bernal', 'Armbel', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 500, 'Five hundred pesos only', 'Gemma D. Ferrer', '2026-06-13 13:57:59', '2026-06-13 13:57:59', NULL),
(6, 1, '13476955', 2026, 'Sorsogon City', '2026-06-13 00:00:00', '2026-06-13 00:00:00', 'Bernal', 'Armbel', 'Besalo', '123456789101000', 'on', 'Filipino', NULL, 'Tramo, Pasig', '141', 'on', '75', '2026-06-13 00:00:00', NULL, 5, 0, 1, 0, 1, 0, 10, 17, 0, 17, 'Seventeen Pesos Only', 'Gemma D. Ferrer', '2026-06-13 14:34:18', '2026-06-13 14:34:18', NULL),
(7, 1, '13476955', 2026, 'Prieto-Diaz, Sorsogon City, Sorsogon', '2026-06-15 00:00:00', NULL, 'Bernal', 'Armbel', 'Besalo', '123456789011123', 'on', 'Filipino', NULL, 'Tramo, Pasig', '141', 'on', '75', NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 4, 3, 7, 'Five hundred pesos only', 'Gemma D. Ferrer', '2026-06-14 17:25:11', '2026-06-14 17:25:11', NULL),
(8, 1, '00001', 2026, 'Prieto-Diaz, Sorsogon City, Sorsogon', '2026-06-15 00:00:00', NULL, 'Bernal', 'Armbel', 'Besalo', '123451512412351', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 500, 'Five hundred pesos only', 'Gemma D. Ferrer', '2026-06-14 17:37:43', '2026-06-14 17:37:43', NULL),
(9, 1, '00006', 2026, 'Prieto-Diaz, Sorsogon City, Sorsogon', NULL, NULL, 'Bernal', 'Armbel', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 500, 'Five hundred pesos only', 'Gemma D. Ferrer', '2026-06-15 02:09:13', '2026-06-15 02:09:13', NULL),
(10, 1, '00007', 2026, NULL, NULL, NULL, 'Bernal', 'Armbel', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 500, 'Five hundred pesos only', 'Gemma D. Ferrer', '2026-06-15 02:10:09', '2026-06-15 02:10:09', NULL),
(11, 1, '00002', 2026, NULL, NULL, NULL, 'Bernal', 'Armbel', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 500, 'Five hundred pesos only', 'Gemma D. Ferrer', '2026-06-15 02:11:53', '2026-06-15 02:11:53', NULL),
(12, 1, '00009', 2026, NULL, NULL, NULL, 'Bernal', 'Armbel', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 500, 'Five hundred pesos only', 'Gemma D. Ferrer', '2026-06-15 02:21:41', '2026-06-15 02:21:41', NULL),
(13, 1, '00001', 2026, NULL, NULL, NULL, 'TestSurname', 'TestFirst', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 100, 'Five hundred pesos only', 'Gemma D. Ferrer', '2026-06-15 02:39:26', '2026-06-15 02:39:26', 'CCI2026-'),
(14, 1, '00003', 2026, NULL, NULL, NULL, 'Bernal', 'Armbel', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 500, 'Five hundred pesos only', 'Gemma D. Ferrer', '2026-06-15 03:20:21', '2026-06-15 03:20:21', 'CCI2026-');

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(255) NOT NULL,
  `connection` VARCHAR(255) NOT NULL,
  `queue` VARCHAR(255) NOT NULL,
  `payload` TEXT NOT NULL,
  `exception` TEXT NOT NULL,
  `failed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`, `queue`, `failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- (runtime table `failed_jobs` left empty)

DROP TABLE IF EXISTS `form_batches`;
CREATE TABLE `form_batches` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `form_stock_id` BIGINT NOT NULL,
  `registration_date` DATE NOT NULL,
  `purchase_date` DATE NOT NULL,
  `starting_serial_number` VARCHAR(255) NOT NULL,
  `ending_serial_number` VARCHAR(255) NOT NULL,
  `added_by` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `assigned_to` VARCHAR(255) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `form_batches` (`id`, `form_stock_id`, `registration_date`, `purchase_date`, `starting_serial_number`, `ending_serial_number`, `added_by`, `created_at`, `updated_at`, `assigned_to`) VALUES
(7, 6, '2026-06-13 10:04:58', '2026-06-13 10:04:58', '2026-001', '025', 'System', '2026-06-13 10:04:58', '2026-06-19 23:06:04', NULL),
(9, 8, '2026-06-14 16:41:48', '2026-06-14 16:41:48', 'AB001', 'AB010', 'System', '2026-06-14 16:41:48', '2026-06-14 16:41:48', NULL),
(10, 1, '2026-06-14 17:21:52', '2026-06-14 17:21:52', '2026-00001', '005', 'System', '2026-06-14 17:21:52', '2026-06-14 17:21:52', NULL),
(12, 1, '2026-06-15 02:49:31', '2026-06-15 02:49:31', '2026-00006', '007', 'System', '2026-06-15 02:49:31', '2026-06-15 02:49:31', NULL),
(13, 1, '2026-06-15 03:25:46', '2026-06-15 03:25:46', '2026-00050', '052', 'System', '2026-06-15 03:25:46', '2026-06-15 03:25:46', NULL),
(16, 5, '2026-06-16 08:46:57', '2026-06-16 08:46:57', '8104350', '8104351', 'Armbel Bernal', '2026-06-16 08:46:57', '2026-06-20 00:24:10', 'Jose Ramirez'),
(17, 5, '2026-06-16 12:39:54', '2026-06-16 12:39:54', '2027021', '2027022', 'Marlaw Sol Emata', '2026-06-16 12:39:54', '2026-06-20 02:02:51', 'Jose Ramirez'),
(18, 5, '2026-06-16 12:49:59', '2026-06-16 12:49:59', '2027021', '2027021', 'Marlaw Sol Emata', '2026-06-16 12:49:59', '2026-06-20 02:02:53', 'Jose Ramirez'),
(19, 5, '2026-06-16 12:50:26', '2026-06-16 12:50:26', '2027021', '2027021', 'Marlaw Sol Emata', '2026-06-16 12:50:26', '2026-06-20 02:02:55', 'Jose Ramirez'),
(20, 5, '2026-06-16 12:56:32', '2026-06-16 12:56:32', '2027023', '2027023', 'Marlaw Sol Emata', '2026-06-16 12:56:32', '2026-06-20 02:02:57', 'Jose Ramirez'),
(21, 5, '2026-06-16 12:58:57', '2026-06-16 12:58:57', '2027024', '2027025', 'Marlaw Sol Emata', '2026-06-16 12:58:57', '2026-06-20 02:02:59', 'Jose Ramirez'),
(22, 5, '2026-06-16 13:25:22', '2026-06-16 13:25:22', '2027026', '2027030', 'Marlaw Sol Emata', '2026-06-16 13:25:22', '2026-06-20 00:31:18', 'Barangay Cabid-an'),
(23, 5, '2026-06-17 08:07:53', '2026-06-17 08:07:53', '2027023', '2027023', 'Cleofe Villanueva', '2026-06-17 08:07:53', '2026-06-20 00:25:41', 'Juan Dela Cruz'),
(24, 7, '2026-06-17 17:01:56', '2026-06-17 17:01:56', 'ORRPT000', 'ORRPT001', 'Cleofe Villanueva', '2026-06-17 17:01:56', '2026-06-20 02:03:36', 'Juan Dela Cruz'),
(25, 5, '2026-06-18 00:12:59', '2026-06-18 00:12:59', '2027030', '2027034', 'Cleofe Villanueva', '2026-06-18 00:12:59', '2026-06-20 02:03:01', 'Jose Ramirez');

DROP TABLE IF EXISTS `form_stocks`;
CREATE TABLE `form_stocks` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `qty` BIGINT NOT NULL,
  `form_name` VARCHAR(255) NOT NULL,
  `form_code` VARCHAR(255) NOT NULL,
  `added_date` DATE NOT NULL,
  `added_by` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `added_time` TIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `form_stocks` (`id`, `qty`, `form_name`, `form_code`, `added_date`, `added_by`, `created_at`, `updated_at`, `added_time`) VALUES
(1, 4, 'Individual Cedula', 'BIR0016', '2026-06-15 03:25:46', 'Marlaw Sol Emata', '2026-06-13 07:27:13', '2026-06-15 03:25:46', '03:25:46'),
(2, 0, 'Corporation Cedula', 'BIR0017', '2021-12-19 00:00:00', 'Marlaw Sol Emata', '2026-06-13 07:27:13', '2026-06-14 14:50:02', '10:45:00'),
(3, 0, 'Certificate of Ownership of Large Cattle', 'Form 53', '2022-03-01 00:00:00', 'Marlaw Sol Emata', '2026-06-13 07:27:13', '2026-06-14 14:50:02', '11:00:00'),
(4, 0, 'Certificate of Transfer of Large Cattle', 'Form 28A', '2022-07-04 00:00:00', 'Marlaw Sol Emata', '2026-06-13 07:27:13', '2026-06-14 14:50:02', '12:30:00'),
(5, 5, 'Marriage License', 'Form 10', '2026-06-18 00:12:59', 'Marlaw Sol Emata', '2026-06-13 07:27:13', '2026-06-18 00:40:27', '00:12:59'),
(6, 0, 'Official Receipt', 'Form 5IC', '2026-06-13 10:04:58', 'Marlaw Sol Emata', '2026-06-13 07:27:13', '2026-06-14 14:50:02', '14:50:00'),
(7, 1, 'OR RPT', 'Form 56', '2026-06-17 17:01:56', 'Marlaw Sol Emata', '2026-06-13 07:27:13', '2026-06-18 00:51:31', '17:01:56'),
(8, 10, 'Burial', 'Form 58', '2026-06-14 16:41:48', 'Marlaw Sol Emata', '2026-06-13 07:27:13', '2026-06-14 16:41:48', '16:41:48');

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `total_jobs` BIGINT NOT NULL,
  `pending_jobs` BIGINT NOT NULL,
  `failed_jobs` BIGINT NOT NULL,
  `failed_job_ids` TEXT NOT NULL,
  `options` TEXT NULL,
  `cancelled_at` BIGINT NULL,
  `created_at` BIGINT NOT NULL,
  `finished_at` BIGINT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- (runtime table `job_batches` left empty)

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `queue` VARCHAR(255) NOT NULL,
  `payload` TEXT NOT NULL,
  `attempts` BIGINT NOT NULL,
  `reserved_at` BIGINT NULL,
  `available_at` BIGINT NOT NULL,
  `created_at` BIGINT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- (runtime table `jobs` left empty)

DROP TABLE IF EXISTS `marriage_certificate_transactions`;
CREATE TABLE `marriage_certificate_transactions` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `form_stock_id` BIGINT NOT NULL,
  `certificate_number` VARCHAR(255) NOT NULL,
  `husband_name` VARCHAR(255) NOT NULL,
  `husband_age_years` BIGINT NULL,
  `husband_age_months` BIGINT NULL,
  `husband_address` VARCHAR(255) NULL,
  `wife_name` VARCHAR(255) NOT NULL,
  `wife_age_years` BIGINT NULL,
  `wife_age_months` BIGINT NULL,
  `wife_address` VARCHAR(255) NULL,
  `witness_day` VARCHAR(255) NULL,
  `witness_month` VARCHAR(255) NULL,
  `witness_year` VARCHAR(255) NULL,
  `instructions_day` VARCHAR(255) NULL,
  `instructions_month` VARCHAR(255) NULL,
  `instructions_year` VARCHAR(255) NULL,
  `registry_number` VARCHAR(255) NULL,
  `local_civil_registrar_of` VARCHAR(255) NULL,
  `email` VARCHAR(255) NULL,
  `message` TEXT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `marriage_certificate_transactions` (`id`, `form_stock_id`, `certificate_number`, `husband_name`, `husband_age_years`, `husband_age_months`, `husband_address`, `wife_name`, `wife_age_years`, `wife_age_months`, `wife_address`, `witness_day`, `witness_month`, `witness_year`, `instructions_day`, `instructions_month`, `instructions_year`, `registry_number`, `local_civil_registrar_of`, `email`, `message`, `created_at`, `updated_at`) VALUES
(1, 5, '0000001', 'Juan de la Cruz', 30, 0, 'Prieto Diaz', 'Maria Santos', 28, 0, 'Prieto Diaz', '16', 'June', '26', '16', 'June', '26', '12345', 'Prieto Diaz', NULL, NULL, '2026-06-16 07:45:07', '2026-06-16 07:45:07'),
(2, 5, '0000354', 'Armbel Besalo Bernal', 35, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', 'Cleofe Dioneda Villanueva', 30, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', '16', 'June', '26', '16', 'June', '26', '123456-7123-11-2', 'Dasmariñas, Cavite, 4115', NULL, NULL, '2026-06-16 08:34:03', '2026-06-16 08:34:03'),
(3, 5, '0000002', 'Armbel Besalo Bernal', 35, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', 'Cleofe Dioneda Villanueva', 30, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', '16', 'June', '26', '16', 'June', '26', '123456-7123-11-2', 'Dasmariñas, Cavite, 4115', NULL, NULL, '2026-06-16 08:43:02', '2026-06-16 08:43:02'),
(4, 5, '8104352', 'Armbel Besalo Bernal', 35, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', 'Cleofe Dioneda Villanueva', 30, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', '16', 'June', '26', '16', 'June', '26', '123456-7123-11-2', 'Dasmariñas, Cavite, 4115', NULL, NULL, '2026-06-16 08:49:20', '2026-06-16 08:49:20'),
(5, 5, '8104350', 'Armbel Besalo Bernal', 35, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', 'Cleofe Dioneda Villanueva', 31, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', '16', 'June', '26', '16', 'June', '26', '123456-7123-11-2', 'Dasmariñas, Cavite, 4115', NULL, NULL, '2026-06-16 09:06:25', '2026-06-16 09:06:25'),
(6, 5, '8104351', 'Armbel Besalo Bernal', 35, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', 'Cleofe Dioneda Villanueva', 30, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', '16', 'June', '26', '16', 'June', '26', '123456-7123-11-2', 'Dasmariñas, Cavite, 4115', NULL, NULL, '2026-06-16 12:35:17', '2026-06-16 12:35:17'),
(7, 5, '2027021', 'Armbel Besalo Bernal', 35, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', 'Cleofe Dioneda Villanueva', 35, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', '16', 'June', '26', '16', 'June', '26', '123456-7123-11-2', 'Dasmariñas, Cavite, 4115', NULL, NULL, '2026-06-16 12:41:25', '2026-06-16 12:41:25'),
(8, 5, '2027022', 'Armbel Besalo Bernal', 35, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', 'Cleofe Dioneda Villanueva', 34, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', '16', 'June', '26', '16', 'June', '26', '123456-7123-11-2', 'Dasmariñas, Cavite, 4115', NULL, NULL, '2026-06-16 12:46:12', '2026-06-16 12:46:12'),
(9, 5, '2027023', 'Armbel Besalo Bernal', 35, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', 'Cleofe Dioneda Villanueva', 35, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', '16', 'June', '26', '16', 'June', '26', '123456-7123-11-2', 'Dasmariñas, Cavite, 4115', NULL, NULL, '2026-06-16 12:57:05', '2026-06-16 12:57:05'),
(10, 5, '2027024', 'Armbel Besalo Bernal', 35, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', 'Cleofe Dioneda Villanueva', 35, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', '16', 'June', '26', '16', 'June', '26', '123456-7123-11-2', 'Dasmariñas, Cavite, 4115', NULL, NULL, '2026-06-16 12:59:27', '2026-06-16 12:59:27'),
(11, 5, '2027025', 'Armbel Besalo Bernal', 35, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', 'Cleofe Dioneda Villanueva', 30, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', '16', 'June', '26', '16', 'June', '26', '123456-7123-11-2', 'Dasmariñas, Cavite, 4115', NULL, NULL, '2026-06-16 13:16:21', '2026-06-16 13:16:21'),
(12, 5, '2027026', 'Armbel Besalo Bernal', 35, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', 'Cleofe Dioneda Villanueva', 30, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', '16', 'June', '26', '16', 'June', '26', '123456-7123-11-2', 'Dasmariñas, Cavite, 4115', NULL, NULL, '2026-06-16 13:26:34', '2026-06-16 13:26:34'),
(13, 5, '2027027', 'Armbel Besalo Bernal', 35, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', 'Cleofe Dioneda Villanueva', 30, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', '16', 'June', '26', '16', 'June', '26', '123456-7123-11-2', 'Dasmariñas, Cavite, 4115', NULL, NULL, '2026-06-16 13:42:54', '2026-06-16 13:42:54'),
(14, 5, '2027028', 'Armbel Besalo Bernal', 35, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', 'Cleofe Dioneda Villanueva', 30, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', '16', 'June', '26', '16', 'June', '26', '123456-7123-11-2', 'Dasmariñas, Cavite, 4115', NULL, NULL, '2026-06-16 13:47:57', '2026-06-16 13:47:57'),
(15, 5, '2027029', 'Armbel Besalo Bernal', 35, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', 'Cleofe Dioneda Villanueva', 30, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', '16', 'June', '26', '16', 'June', '26', '123456-7123-11-2', 'Dasmariñas, Cavite, 4115', NULL, NULL, '2026-06-17 23:43:28', '2026-06-17 23:43:28'),
(16, 5, '2027030', 'Armbel Besalo Bernal', NULL, NULL, NULL, 'Cleofe Dioneda Villanueva', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-17 23:55:16', '2026-06-17 23:55:16'),
(17, 5, '2027031', 'Armbel Besalo Bernal', NULL, NULL, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', 'Cleofe Dioneda Villanueva', NULL, NULL, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-18 00:15:33', '2026-06-18 00:15:33'),
(18, 5, '2027032', 'Armbel Besalo Bernal', 35, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', 'Cleofe Dioneda Villanueva', 30, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', '16', 'June', '26', '16', 'June', '26', '123456-7123-11-2', 'Dasmariñas, Cavite, 4115', NULL, NULL, '2026-06-18 00:38:14', '2026-06-18 00:38:14'),
(19, 5, '2027033', 'Armbel Besalo Bernal', 26, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', 'Cleofe Dioneda Villanueva', 20, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', '16', 'June', '26', '16', 'June', '26', '123456-7123-11-2', 'Dasmariñas, Cavite, 4115', NULL, NULL, '2026-06-18 00:40:27', '2026-06-18 00:40:27');

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `batch` BIGINT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_06_12_140401_create_transaction_logs_table', 1),
(5, '2026_06_13_120000_create_form_stocks_table', 2),
(6, '2026_06_13_130000_create_form_batches_table', 3),
(7, '2026_06_13_140000_create_ctc_individual_transactions_table', 4),
(8, '2026_06_13_150000_create_ctc_corporation_transactions_table', 5),
(9, '2026_06_13_160000_create_or_rpt_transactions_table', 6),
(10, '2026_06_14_100000_create_or_transactions_table', 7),
(11, '2026_06_15_100000_add_added_time_to_form_stocks_table', 8),
(12, '2026_06_15_110000_add_certificate_prefix_to_ctc_transactions_tables', 9),
(13, '2026_06_15_120000_add_account_fields_to_users_table', 10),
(14, '2026_06_15_130000_create_roles_table', 10),
(15, '2026_06_15_140000_create_role_module_permissions_table', 10),
(16, '2026_06_15_150000_create_activity_logs_table', 10),
(17, '2026_06_15_160000_add_username_and_archived_at_to_users_table', 11),
(18, '2026_06_16_100000_create_marriage_certificate_transactions_table', 12),
(19, '2026_06_17_100000_add_polymorphic_and_archived_at_to_transaction_logs', 13),
(20, '2026_06_17_110000_create_cancel_requests_table', 13),
(21, '2026_06_19_015005_add_notified_at_to_cancel_requests_table', 14),
(22, '2026_06_19_224250_add_assigned_to_to_form_batches_table', 15);

DROP TABLE IF EXISTS `or_rpt_transactions`;
CREATE TABLE `or_rpt_transactions` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `form_stock_id` BIGINT NOT NULL,
  `certificate_number` VARCHAR(255) NOT NULL,
  `previous_receipt_number` VARCHAR(255) NULL,
  `previous_receipt_date` VARCHAR(255) NULL,
  `previous_receipt_year` VARCHAR(255) NULL,
  `municipality_province` VARCHAR(255) NULL,
  `city` VARCHAR(255) NULL,
  `transaction_date` DATE NULL,
  `client_name` VARCHAR(255) NOT NULL,
  `payment_in_words` VARCHAR(255) NULL,
  `amount_paid` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `treasurer_deputy` VARCHAR(255) NULL,
  `basic_tax` TINYINT(1) NOT NULL DEFAULT '0',
  `special_education_fund` TINYINT(1) NOT NULL DEFAULT '0',
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `or_rpt_transactions` (`id`, `form_stock_id`, `certificate_number`, `previous_receipt_number`, `previous_receipt_date`, `previous_receipt_year`, `municipality_province`, `city`, `transaction_date`, `client_name`, `payment_in_words`, `amount_paid`, `treasurer_deputy`, `basic_tax`, `special_education_fund`, `created_at`, `updated_at`) VALUES
(3, 7, '0000001', NULL, NULL, NULL, 'Prieto-Diaz, Sorsogon', 'Sorsogon', '2026-06-13 00:00:00', 'Armbel Bernal', NULL, 500, 'Gemma D. Ferrer', 0, 0, '2026-06-13 16:34:37', '2026-06-13 16:34:37'),
(4, 7, '0000004', NULL, NULL, NULL, 'Prieto-Diaz, Sorsogon', 'Sorsogon', '2026-06-14 00:00:00', 'Armbel Bernal', NULL, 15000, 'Gemma D. Ferrer', 0, 0, '2026-06-14 07:29:41', '2026-06-14 07:29:41'),
(5, 7, '0000005', NULL, NULL, NULL, 'Prieto-Diaz, Sorsogon', 'Sorsogon', '2026-06-14 00:00:00', 'Cleofe Villanue', NULL, 40000, 'Gemma D. Ferrer', 0, 0, '2026-06-14 07:41:01', '2026-06-14 07:41:01'),
(6, 7, '0000006', NULL, NULL, NULL, 'Prieto-Diaz, Sorsogon', 'Sorsogon', '2026-06-14 00:00:00', 'Armbel Bernal', NULL, 10, 'Gemma D. Ferrer', 0, 0, '2026-06-14 08:21:43', '2026-06-14 08:21:43'),
(7, 7, '0000007', '211133456-2026', 'June 14', '26', 'Prieto-Diaz, Sorsogon', 'Sorsogon', '2026-06-14 00:00:00', 'Armbel Bernal', 'Forty-Thousand Pesos', 40000, 'Gemma D. Ferrer', 1, 0, '2026-06-14 08:22:40', '2026-06-14 08:22:40'),
(8, 7, '0000008', '211133456-2026', 'June 14', '26', 'Prieto-Diaz, Sorsogon', 'Sorsogon', '2026-06-18 00:00:00', 'Armbel Bernal', 'Forty-Thousand Pesos', 40000, 'Gemma D. Ferrer', 1, 0, '2026-06-18 00:51:31', '2026-06-18 00:51:31');

DROP TABLE IF EXISTS `or_transactions`;
CREATE TABLE `or_transactions` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `form_stock_id` BIGINT NOT NULL,
  `certificate_number` VARCHAR(255) NOT NULL,
  `date_issued` DATE NULL,
  `agency` VARCHAR(255) NULL,
  `fund` VARCHAR(255) NULL,
  `payor` VARCHAR(255) NOT NULL,
  `items` TEXT NOT NULL,
  `total` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `amount_in_words` VARCHAR(255) NULL,
  `payment_method` VARCHAR(255) NOT NULL DEFAULT 'cash',
  `drawee_bank` VARCHAR(255) NULL,
  `check_number` VARCHAR(255) NULL,
  `check_date` DATE NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `or_transactions` (`id`, `form_stock_id`, `certificate_number`, `date_issued`, `agency`, `fund`, `payor`, `items`, `total`, `amount_in_words`, `payment_method`, `drawee_bank`, `check_number`, `check_date`, `created_at`, `updated_at`) VALUES
(3, 6, '0000001', '2026-06-17 00:00:00', 'SOLEM', NULL, 'Armbel Bernal', '[{"description":null,"account_code":null,"amount":0},{"description":null,"account_code":null,"amount":0},{"description":null,"account_code":null,"amount":0},{"description":null,"account_code":null,"amount":0},{"description":null,"account_code":null,"amount":0},{"description":null,"account_code":null,"amount":0},{"description":null,"account_code":null,"amount":0},{"description":null,"account_code":null,"amount":0}]', 0, NULL, 'cash', NULL, NULL, NULL, '2026-06-17 17:18:17', '2026-06-17 17:18:17'),
(5, 6, '0000004', '2026-06-17 00:00:00', 'SOLEM IT & Digital Solutions', '75000', 'Armbel Bernal', '[{"description":"Business Permit","account_code":"BP01","amount":"25000"},{"description":null,"account_code":null,"amount":0},{"description":null,"account_code":null,"amount":0},{"description":null,"account_code":null,"amount":0},{"description":null,"account_code":null,"amount":0},{"description":null,"account_code":null,"amount":0},{"description":null,"account_code":null,"amount":0},{"description":null,"account_code":null,"amount":0}]', 25000, 'Twenty-Five Thousand Pesos', 'cash', NULL, NULL, '2026-06-17 00:00:00', '2026-06-17 21:00:20', '2026-06-17 21:00:20');

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `role_module_permissions`;
CREATE TABLE `role_module_permissions` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `role_id` BIGINT NOT NULL,
  `module` VARCHAR(255) NOT NULL,
  `view` TINYINT(1) NOT NULL DEFAULT '0',
  `add` TINYINT(1) NOT NULL DEFAULT '0',
  `generate_report` TINYINT(1) NOT NULL DEFAULT '0',
  `print` TINYINT(1) NOT NULL DEFAULT '0',
  `export` TINYINT(1) NOT NULL DEFAULT '0',
  `request_admin_cancellation` TINYINT(1) NOT NULL DEFAULT '0',
  `reset_password` TINYINT(1) NOT NULL DEFAULT '0',
  `change_permission` TINYINT(1) NOT NULL DEFAULT '0',
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_module_permissions_role_id_module_unique` (`role_id`, `module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `role_module_permissions` (`id`, `role_id`, `module`, `view`, `add`, `generate_report`, `print`, `export`, `request_admin_cancellation`, `reset_password`, `change_permission`, `created_at`, `updated_at`) VALUES
(1, 1, 'collections', 1, 1, 1, 1, 1, 0, 0, 0, '2026-06-15 23:07:26', '2026-06-19 01:29:29'),
(2, 1, 'official-receipts-accountable-forms', 1, 1, 1, 1, 1, 0, 0, 0, '2026-06-15 23:07:26', '2026-06-19 01:29:29'),
(3, 1, 'reporting-abstract', 1, 1, 1, 1, 1, 0, 0, 0, '2026-06-15 23:07:26', '2026-06-19 01:29:29'),
(4, 1, 'bank-deposit-reconciliation', 1, 1, 1, 1, 1, 0, 0, 0, '2026-06-15 23:07:26', '2026-06-19 01:29:29'),
(5, 1, 'cheque-management', 1, 1, 1, 1, 1, 0, 0, 0, '2026-06-15 23:07:26', '2026-06-19 01:29:29'),
(6, 1, 'user-management', 1, 1, 1, 1, 1, 0, 1, 1, '2026-06-15 23:07:26', '2026-06-19 01:29:29'),
(7, 1, 'archives', 1, 1, 1, 1, 1, 0, 0, 0, '2026-06-15 23:07:26', '2026-06-19 01:29:29'),
(8, 1, 'records', 1, 1, 1, 1, 1, 0, 0, 0, '2026-06-15 23:07:26', '2026-06-19 01:29:29'),
(9, 2, 'collections', 1, 1, 0, 0, 0, 1, 0, 0, '2026-06-15 23:07:26', '2026-06-19 01:29:29'),
(10, 2, 'official-receipts-accountable-forms', 1, 1, 0, 0, 0, 1, 0, 0, '2026-06-15 23:07:26', '2026-06-19 01:29:29'),
(11, 2, 'reporting-abstract', 1, 1, 0, 0, 0, 1, 0, 0, '2026-06-15 23:07:26', '2026-06-19 01:29:29'),
(12, 2, 'bank-deposit-reconciliation', 1, 1, 0, 0, 0, 1, 0, 0, '2026-06-15 23:07:26', '2026-06-19 01:29:29'),
(13, 2, 'cheque-management', 1, 1, 0, 0, 0, 1, 0, 0, '2026-06-15 23:07:26', '2026-06-19 01:29:29'),
(14, 2, 'user-management', 0, 0, 0, 0, 0, 1, 0, 0, '2026-06-15 23:07:26', '2026-06-19 01:29:29'),
(15, 2, 'archives', 0, 0, 0, 0, 0, 1, 0, 0, '2026-06-15 23:07:26', '2026-06-19 01:29:29'),
(16, 2, 'records', 1, 1, 0, 0, 0, 1, 0, 0, '2026-06-15 23:07:26', '2026-06-19 01:29:29'),
(17, 3, 'collections', 1, 1, 1, 1, 1, 1, 0, 0, '2026-06-15 23:07:26', '2026-06-19 01:29:29'),
(18, 3, 'official-receipts-accountable-forms', 1, 1, 1, 1, 1, 1, 0, 0, '2026-06-15 23:07:26', '2026-06-19 01:29:29'),
(19, 3, 'reporting-abstract', 1, 1, 1, 1, 1, 1, 0, 0, '2026-06-15 23:07:26', '2026-06-19 01:29:29'),
(20, 3, 'bank-deposit-reconciliation', 1, 1, 1, 1, 1, 1, 0, 0, '2026-06-15 23:07:26', '2026-06-19 01:29:29'),
(21, 3, 'cheque-management', 1, 1, 1, 1, 1, 1, 0, 0, '2026-06-15 23:07:26', '2026-06-19 01:29:29'),
(22, 3, 'user-management', 1, 1, 1, 1, 1, 1, 0, 0, '2026-06-15 23:07:26', '2026-06-19 01:29:29'),
(23, 3, 'archives', 1, 1, 1, 1, 1, 1, 0, 0, '2026-06-15 23:07:26', '2026-06-19 01:29:29'),
(24, 3, 'records', 1, 1, 1, 1, 1, 1, 0, 0, '2026-06-15 23:07:26', '2026-06-19 01:29:29');

DROP TABLE IF EXISTS `role_user`;
CREATE TABLE `role_user` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `role_id` BIGINT NOT NULL,
  `user_id` BIGINT NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `role_user` (`id`, `role_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(2, 2, 2, NULL, NULL),
(3, 2, 3, NULL, NULL),
(4, 3, 4, NULL, NULL),
(5, 3, 5, NULL, NULL),
(6, 2, 6, NULL, NULL),
(7, 3, 7, NULL, NULL),
(8, 2, 8, NULL, NULL),
(9, 1, 9, NULL, NULL),
(10, 1, 10, NULL, NULL),
(12, 1, 12, NULL, NULL);

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin', '2026-06-15 23:07:19', '2026-06-15 23:07:19'),
(2, 'Collector', 'collector', '2026-06-15 23:07:19', '2026-06-15 23:07:19'),
(3, 'Abstract Reporting Officer', 'abstract-reporting-officer', '2026-06-15 23:07:19', '2026-06-15 23:07:19');

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` VARCHAR(255) NOT NULL,
  `user_id` BIGINT NULL,
  `ip_address` VARCHAR(255) NULL,
  `user_agent` TEXT NULL,
  `payload` TEXT NOT NULL,
  `last_activity` BIGINT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_last_activity_index` (`last_activity`),
  KEY `sessions_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- (runtime table `sessions` left empty)

DROP TABLE IF EXISTS `transaction_logs`;
CREATE TABLE `transaction_logs` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `serial_number` VARCHAR(255) NOT NULL,
  `payee` VARCHAR(255) NOT NULL,
  `transacted_at` DATETIME NOT NULL,
  `form_type` VARCHAR(255) NOT NULL,
  `status` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `transaction_type` VARCHAR(255) NULL,
  `transaction_id` BIGINT NULL,
  `archived_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_logs_transaction_type_transaction_id_index` (`transaction_type`, `transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `transaction_logs` (`id`, `serial_number`, `payee`, `transacted_at`, `form_type`, `status`, `created_at`, `updated_at`, `transaction_type`, `transaction_id`, `archived_at`) VALUES
(1, '324-5678-1234-02', 'Smith, John A', '2023-01-12 02:15:00', 'Form 58', 'Cancelled', '2026-06-13 05:54:20', '2026-06-19 01:15:42', NULL, NULL, '2026-06-19 01:15:42'),
(2, '324-5678-1234-02', 'Johnson, Emily B', '2021-12-19 17:45:00', 'BIR0017', 'Cancelled', '2026-06-13 05:54:20', '2026-06-19 01:15:42', NULL, NULL, '2026-06-19 01:15:42'),
(3, '324-5678-1234-02', 'Williams, Michael C', '2022-03-01 17:00:00', 'Form 53', 'Cancelled', '2026-06-13 05:54:20', '2026-06-19 01:15:42', NULL, NULL, '2026-06-19 01:15:42'),
(4, '324-5678-1234-02', 'Brown, Sarah D', '2022-07-04 01:45:00', 'Form 28A', 'Cancelled', '2026-06-13 05:54:20', '2026-06-19 01:15:42', NULL, NULL, '2026-06-19 01:15:42'),
(5, '324-5678-1234-02', 'Jones, David E', '2023-03-08 15:00:00', 'BIR0016', 'Cancelled', '2026-06-13 05:54:20', '2026-06-19 01:22:31', NULL, NULL, '2026-06-19 01:22:31'),
(7, '324-5678-1234-02', 'Martinez, Carlos G', '2022-09-14 20:15:00', 'Form 5IC', 'Cancelled', '2026-06-13 05:54:20', '2026-06-19 01:15:42', NULL, NULL, '2026-06-19 01:15:42'),
(8, '324-5678-1234-02', 'Davis, Jennifer H', '2022-10-11 07:00:00', 'Form 56', 'Cancelled', '2026-06-13 05:54:20', '2026-06-19 01:15:42', NULL, NULL, '2026-06-19 01:15:42'),
(9, '324-5678-1234-02', 'Rodriguez, Daniel I', '2020-04-30 12:45:00', 'Form 58', 'Cancelled', '2026-06-13 05:54:20', '2026-06-19 01:15:42', NULL, NULL, '2026-06-19 01:15:42'),
(10, '324-5678-1234-02', 'Wilson, Jessica J', '2023-05-15 21:30:00', 'BIR0017', 'Cancelled', '2026-06-13 05:54:20', '2026-06-19 01:22:31', NULL, NULL, '2026-06-19 01:22:31'),
(11, '324-5678-1234-02', 'Anderson, Brian K', '2021-08-23 04:00:00', 'Form 53', 'Cancelled', '2026-06-13 05:54:20', '2026-06-19 01:15:42', NULL, NULL, '2026-06-19 01:15:42'),
(12, '324-5678-1234-02', 'Thomas, Angela L', '2021-06-30 23:15:00', 'Form 28A', 'Cancelled', '2026-06-13 05:54:20', '2026-06-19 01:15:42', NULL, NULL, '2026-06-19 01:15:42'),
(13, '324-5678-1234-02', 'Taylor, Kevin M', '2022-11-05 06:00:00', 'BIR0016', 'Cancelled', '2026-06-13 05:54:20', '2026-06-19 01:15:42', NULL, NULL, '2026-06-19 01:15:42'),
(15, '324-5678-1234-04', 'Cassin, Phyllis z', '2025-06-23 16:42:38', 'Form 5IC', 'Completed', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(16, '324-5678-1234-47', 'Lubowitz, Rosa j', '2024-07-18 17:51:57', 'Form 56', 'Cancelled', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(17, '324-5678-1234-80', 'Kovacek, Samara l', '2025-01-20 10:30:02', 'BIR0016', 'Cancelled', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(18, '324-5678-1234-37', 'Osinski, Halie u', '2024-08-19 05:43:25', 'BIR0016', 'Completed', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(19, '324-5678-1234-42', 'Becker, Mathilde a', '2026-05-13 08:32:38', 'Form 28A', 'Completed', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(20, '324-5678-1234-56', 'Kozey, Otho g', '2023-07-18 15:25:48', 'BIR0016', 'Completed', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(21, '324-5678-1234-19', 'Shanahan, Maci m', '2023-11-28 19:11:51', 'Form 5IC', 'Cancelled', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(22, '324-5678-1234-36', 'Kemmer, Corrine f', '2025-01-08 01:23:05', 'Form 58', 'Cancelled', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(24, '324-5678-1234-48', 'Pfannerstill, Julien p', '2025-01-27 19:23:48', 'Form 53', 'Cancelled', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(25, '324-5678-1234-71', 'Conn, Ernesto w', '2023-10-24 09:01:10', 'BIR0016', 'Completed', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(26, '324-5678-1234-92', 'Torphy, Virgie g', '2025-11-23 06:22:48', 'Form 53', 'Cancelled', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(27, '324-5678-1234-28', 'Pfannerstill, Maryse k', '2023-10-19 09:49:29', 'BIR0016', 'Cancelled', '2026-06-13 05:54:20', '2026-06-19 01:22:31', NULL, NULL, '2026-06-19 01:22:31'),
(28, '324-5678-1234-17', 'Cassin, Zita q', '2023-07-18 11:06:50', 'BIR0017', 'Cancelled', '2026-06-13 05:54:20', '2026-06-19 01:22:31', NULL, NULL, '2026-06-19 01:22:31'),
(29, '324-5678-1234-43', 'Stanton, Magdalena s', '2026-03-01 08:54:39', 'Form 56', 'Cancelled', '2026-06-13 05:54:20', '2026-06-19 00:55:37', NULL, NULL, NULL),
(30, '324-5678-1234-18', 'Muller, Blanche o', '2025-04-08 08:27:09', 'Form 5IC', 'Cancelled', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(31, '324-5678-1234-84', 'Fritsch, Dolly z', '2026-05-29 20:39:39', 'Form 28A', 'Completed', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(32, '324-5678-1234-61', 'Lynch, Russ x', '2025-05-03 05:31:55', 'BIR0017', 'Completed', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(33, '324-5678-1234-80', 'Kuhlman, Oceane a', '2024-11-21 00:41:53', 'Form 58', 'Cancelled', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(34, '324-5678-1234-87', 'Blanda, Lea w', '2025-09-16 17:24:07', 'Form 58', 'Completed', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(35, '324-5678-1234-56', 'O\'Connell, Ava x', '2024-11-02 03:29:45', 'Form 56', 'Completed', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(36, '324-5678-1234-80', 'Beahan, Jamie z', '2025-02-27 11:46:07', 'BIR0016', 'Completed', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(37, '324-5678-1234-25', 'Okuneva, Shanny p', '2025-07-25 01:38:36', 'BIR0017', 'Cancelled', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(38, '324-5678-1234-84', 'Bailey, Archibald y', '2024-05-15 14:46:26', 'Form 53', 'Cancelled', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(39, '324-5678-1234-54', 'Jerde, Willis a', '2024-06-23 15:16:00', 'Form 5IC', 'Cancelled', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(41, '324-5678-1234-48', 'Stiedemann, Norene p', '2025-01-19 15:19:45', 'Form 58', 'Completed', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(42, '324-5678-1234-61', 'Gutkowski, Jamal h', '2025-12-16 13:32:22', 'BIR0016', 'Completed', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(43, '324-5678-1234-96', 'Durgan, Juanita p', '2024-11-25 04:04:05', 'BIR0016', 'Cancelled', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(44, '324-5678-1234-85', 'Kirlin, Novella f', '2023-11-11 00:40:00', 'BIR0017', 'Cancelled', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(45, '324-5678-1234-64', 'Anderson, Augustine u', '2024-10-21 17:09:03', 'Form 5IC', 'Completed', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(46, '324-5678-1234-90', 'Kulas, Madie a', '2024-05-02 03:23:07', 'BIR0016', 'Cancelled', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(47, '324-5678-1234-94', 'Roberts, Wanda v', '2025-03-05 08:07:01', 'Form 5IC', 'Completed', '2026-06-13 05:54:20', '2026-06-13 05:54:20', NULL, NULL, NULL),
(49, '324-5678-1234-38', 'Berge, Norene p', '2026-05-17 18:19:22', 'Form 58', 'Cancelled', '2026-06-13 05:54:21', '2026-06-13 05:54:21', NULL, NULL, NULL),
(50, '324-5678-1234-86', 'Cremin, Isadore w', '2024-12-21 14:10:17', 'Form 28A', 'Cancelled', '2026-06-13 05:54:21', '2026-06-13 05:54:21', NULL, NULL, NULL),
(51, '324-5678-1234-57', 'Lockman, Maegan b', '2024-01-09 18:57:41', 'BIR0017', 'Completed', '2026-06-13 05:54:21', '2026-06-13 05:54:21', NULL, NULL, NULL),
(52, '324-5678-1234-90', 'Zulauf, Autumn e', '2024-11-26 12:55:20', 'BIR0016', 'Completed', '2026-06-13 05:54:21', '2026-06-13 05:54:21', NULL, NULL, NULL),
(53, '324-5678-1234-32', 'Miller, Bethany a', '2024-12-28 21:44:12', 'Form 58', 'Completed', '2026-06-13 05:54:21', '2026-06-13 05:54:21', NULL, NULL, NULL),
(54, '324-5678-1234-97', 'Farrell, Johnnie j', '2023-08-12 16:52:34', 'Form 53', 'Cancelled', '2026-06-13 05:54:21', '2026-06-19 01:22:31', NULL, NULL, '2026-06-19 01:22:31'),
(55, '324-5678-1234-28', 'Schowalter, Selina y', '2023-07-24 06:47:26', 'BIR0017', 'Completed', '2026-06-13 05:54:21', '2026-06-13 05:54:21', NULL, NULL, NULL),
(56, '324-5678-1234-97', 'Conroy, Felicita g', '2025-03-18 21:32:11', 'Form 58', 'Completed', '2026-06-13 05:54:21', '2026-06-13 05:54:21', NULL, NULL, NULL),
(57, '324-5678-1234-58', 'Becker, Roslyn l', '2026-02-22 12:16:30', 'BIR0016', 'Cancelled', '2026-06-13 05:54:21', '2026-06-13 05:54:21', NULL, NULL, NULL),
(58, '324-5678-1234-63', 'Lowe, Gage j', '2023-10-12 05:52:25', 'Form 53', 'Cancelled', '2026-06-13 05:54:21', '2026-06-19 01:22:37', NULL, NULL, NULL),
(60, '324-5678-1234-98', 'Miller, Tianna a', '2025-07-26 05:14:29', 'Form 58', 'Completed', '2026-06-13 05:54:21', '2026-06-13 05:54:21', NULL, NULL, NULL),
(63, '324-5678-1234-38', 'King, Ellen s', '2024-02-02 09:29:43', 'BIR0017', 'Cancelled', '2026-06-13 05:54:21', '2026-06-13 05:54:21', NULL, NULL, NULL),
(64, '324-5678-1234-10', 'Hill, Bernice h', '2025-09-26 09:01:01', 'Form 5IC', 'Cancelled', '2026-06-13 05:54:21', '2026-06-13 05:54:21', NULL, NULL, NULL),
(65, '324-5678-1234-10', 'Berge, Markus c', '2025-09-28 01:47:48', 'Form 56', 'Completed', '2026-06-13 05:54:21', '2026-06-13 05:54:21', NULL, NULL, NULL),
(66, '324-5678-1234-12', 'Rau, Vincenza v', '2026-05-08 12:57:18', 'Form 5IC', 'Cancelled', '2026-06-13 05:54:21', '2026-06-13 05:54:21', NULL, NULL, NULL),
(67, '324-5678-1234-49', 'Hammes, Steve a', '2023-09-04 10:00:20', 'Form 58', 'Completed', '2026-06-13 05:54:21', '2026-06-13 05:54:21', NULL, NULL, NULL),
(68, '324-5678-1234-78', 'Weimann, Orrin l', '2024-07-05 09:49:06', 'BIR0017', 'Cancelled', '2026-06-13 05:54:21', '2026-06-13 05:54:21', NULL, NULL, NULL),
(69, '324-5678-1234-51', 'Anderson, Orlo u', '2025-11-06 00:34:56', 'Form 5IC', 'Cancelled', '2026-06-13 05:54:21', '2026-06-13 05:54:21', NULL, NULL, NULL),
(70, '324-5678-1234-68', 'Stehr, Graham z', '2024-08-13 04:15:56', 'Form 5IC', 'Cancelled', '2026-06-13 05:54:21', '2026-06-13 05:54:21', NULL, NULL, NULL),
(71, '324-5678-1234-67', 'Fahey, Lisette j', '2025-08-02 02:11:53', 'BIR0016', 'Cancelled', '2026-06-13 05:54:21', '2026-06-13 05:54:21', NULL, NULL, NULL),
(72, '324-5678-1234-18', 'Swaniawski, Leslie i', '2023-07-15 16:16:21', 'BIR0016', 'Cancelled', '2026-06-13 05:54:21', '2026-06-19 01:22:31', NULL, NULL, '2026-06-19 01:22:31'),
(73, '324-5678-1234-37', 'Mraz, Alexa q', '2026-01-19 17:19:47', 'Form 53', 'Cancelled', '2026-06-13 05:54:21', '2026-06-13 05:54:21', NULL, NULL, NULL),
(74, '324-5678-1234-11', 'Cremin, Margarette z', '2025-08-02 18:51:37', 'BIR0017', 'Cancelled', '2026-06-13 05:54:21', '2026-06-13 05:54:21', NULL, NULL, NULL),
(79, 'CCI2022 13476955', 'Bernal, Armbel Besalo', '2026-06-13 14:34:18', 'BIR0016', 'Completed', '2026-06-13 14:34:18', '2026-06-17 20:48:19', 'App\\Models\\CtcIndividualTransaction', 6, NULL),
(80, 'CCC2021 00123456', 'SOLEM IT & Digital Solutions Corporation', '2026-06-13 14:37:47', 'BIR0017', 'Completed', '2026-06-13 14:37:47', '2026-06-17 20:48:20', 'App\\Models\\CtcCorporationTransaction', 5, NULL),
(83, '0000001', 'Armbel Bernal', '2026-06-13 16:34:37', 'Form 56', 'Completed', '2026-06-13 16:34:37', '2026-06-17 20:48:20', 'App\\Models\\OrRptTransaction', 3, NULL),
(84, '0000004', 'Armbel Bernal', '2026-06-14 07:29:41', 'Form 56', 'Completed', '2026-06-14 07:29:41', '2026-06-17 20:48:20', 'App\\Models\\OrRptTransaction', 4, NULL),
(85, '0000005', 'Cleofe Villanue', '2026-06-14 07:41:01', 'Form 56', 'Completed', '2026-06-14 07:41:01', '2026-06-17 20:48:20', 'App\\Models\\OrRptTransaction', 5, NULL),
(86, '0000006', 'Armbel Bernal', '2026-06-14 08:21:43', 'Form 56', 'Completed', '2026-06-14 08:21:43', '2026-06-17 20:48:20', 'App\\Models\\OrRptTransaction', 6, NULL),
(87, '0000007', 'Armbel Bernal', '2026-06-14 08:22:40', 'Form 56', 'Completed', '2026-06-14 08:22:40', '2026-06-17 20:48:20', 'App\\Models\\OrRptTransaction', 7, NULL),
(89, 'No. 0000001 U', 'Juan Dela Cruz', '2026-06-14 12:43:17', 'Form 5IC', 'Completed', '2026-06-14 12:43:18', '2026-06-14 12:43:18', NULL, NULL, NULL),
(90, 'CCI2022 13476955', 'Bernal, Armbel Besalo', '2026-06-14 17:25:11', 'BIR0016', 'Completed', '2026-06-14 17:25:11', '2026-06-17 20:48:19', 'App\\Models\\CtcIndividualTransaction', 7, NULL),
(91, '2026 00001', 'Bernal, Armbel Besalo', '2026-06-14 17:37:43', 'BIR0016', 'Completed', '2026-06-14 17:37:43', '2026-06-17 20:48:19', 'App\\Models\\CtcIndividualTransaction', 8, NULL),
(92, 'CCI2026 00006', 'Bernal, Armbel', '2026-06-15 02:09:13', 'BIR0016', 'Completed', '2026-06-15 02:09:13', '2026-06-17 20:48:19', 'App\\Models\\CtcIndividualTransaction', 9, NULL),
(93, 'CCI2026 00007', 'Bernal, Armbel', '2026-06-15 02:10:09', 'BIR0016', 'Completed', '2026-06-15 02:10:09', '2026-06-17 20:48:19', 'App\\Models\\CtcIndividualTransaction', 10, NULL),
(94, 'CCI2022 00002', 'Bernal, Armbel', '2026-06-15 02:11:53', 'BIR0016', 'Completed', '2026-06-15 02:11:53', '2026-06-17 20:48:20', 'App\\Models\\CtcIndividualTransaction', 11, NULL),
(95, 'CCI2026 00009', 'Bernal, Armbel', '2026-06-15 02:21:41', 'BIR0016', 'Cancelled', '2026-06-15 02:21:41', '2026-06-19 00:57:27', 'App\\Models\\CtcIndividualTransaction', 12, NULL),
(96, 'CCI2026- 00001', 'TestSurname, TestFirst', '2026-06-15 02:39:26', 'BIR0016', 'Cancelled', '2026-06-15 02:39:26', '2026-06-19 00:57:17', 'App\\Models\\CtcIndividualTransaction', 13, NULL),
(97, 'CCI2026- 00003', 'Bernal, Armbel', '2026-06-15 03:20:21', 'BIR0016', 'Completed', '2026-06-15 03:20:21', '2026-06-17 20:48:20', 'App\\Models\\CtcIndividualTransaction', 14, NULL),
(99, 'No. 0000354', 'Armbel Besalo Bernal & Cleofe Dioneda Villanueva', '2026-06-16 08:34:03', 'Form 10', 'Completed', '2026-06-16 08:34:03', '2026-06-17 20:48:20', 'App\\Models\\MarriageCertificateTransaction', 2, NULL),
(100, 'No. 0000002', 'Armbel Besalo Bernal & Cleofe Dioneda Villanueva', '2026-06-16 08:43:02', 'Form 10', 'Completed', '2026-06-16 08:43:02', '2026-06-17 20:48:20', 'App\\Models\\MarriageCertificateTransaction', 3, NULL),
(101, 'No. 8104352', 'Armbel Besalo Bernal & Cleofe Dioneda Villanueva', '2026-06-16 08:49:20', 'Form 10', 'Completed', '2026-06-16 08:49:20', '2026-06-17 20:48:20', 'App\\Models\\MarriageCertificateTransaction', 4, NULL),
(102, 'No. 8104350', 'Armbel Besalo Bernal & Cleofe Dioneda Villanueva', '2026-06-16 09:06:26', 'Form 10', 'Completed', '2026-06-16 09:06:26', '2026-06-16 09:06:26', NULL, NULL, NULL),
(103, 'No. 8104351', 'Armbel Besalo Bernal & Cleofe Dioneda Villanueva', '2026-06-16 12:35:17', 'Form 10', 'Completed', '2026-06-16 12:35:17', '2026-06-17 20:48:20', 'App\\Models\\MarriageCertificateTransaction', 6, NULL),
(104, 'No. 2027021', 'Armbel Besalo Bernal & Cleofe Dioneda Villanueva', '2026-06-16 12:41:25', 'Form 10', 'Completed', '2026-06-16 12:41:25', '2026-06-17 20:48:20', 'App\\Models\\MarriageCertificateTransaction', 7, NULL),
(105, 'No. 2027022', 'Armbel Besalo Bernal & Cleofe Dioneda Villanueva', '2026-06-16 12:46:12', 'Form 10', 'Completed', '2026-06-16 12:46:12', '2026-06-17 20:48:20', 'App\\Models\\MarriageCertificateTransaction', 8, NULL),
(106, 'No. 2027023', 'Armbel Besalo Bernal & Cleofe Dioneda Villanueva', '2026-06-16 12:57:05', 'Form 10', 'Completed', '2026-06-16 12:57:05', '2026-06-17 20:48:20', 'App\\Models\\MarriageCertificateTransaction', 9, NULL),
(107, 'No. 2027024', 'Armbel Besalo Bernal & Cleofe Dioneda Villanueva', '2026-06-16 12:59:27', 'Form 10', 'Completed', '2026-06-16 12:59:27', '2026-06-17 20:48:20', 'App\\Models\\MarriageCertificateTransaction', 10, NULL),
(108, 'No. 2027025', 'Armbel Besalo Bernal & Cleofe Dioneda Villanueva', '2026-06-16 13:16:21', 'Form 10', 'Completed', '2026-06-16 13:16:21', '2026-06-17 20:48:20', 'App\\Models\\MarriageCertificateTransaction', 11, NULL),
(109, 'No. 2027026', 'Armbel Besalo Bernal & Cleofe Dioneda Villanueva', '2026-06-16 13:26:34', 'Form 10', 'Completed', '2026-06-16 13:26:34', '2026-06-17 20:48:20', 'App\\Models\\MarriageCertificateTransaction', 12, NULL),
(110, 'No. 2027027', 'Armbel Besalo Bernal & Cleofe Dioneda Villanueva', '2026-06-16 13:42:54', 'Form 10', 'Completed', '2026-06-16 13:42:54', '2026-06-17 20:48:20', 'App\\Models\\MarriageCertificateTransaction', 13, NULL),
(111, 'No. 2027028', 'Armbel Besalo Bernal & Cleofe Dioneda Villanueva', '2026-06-16 13:47:57', 'Form 10', 'Completed', '2026-06-16 13:47:57', '2026-06-17 20:48:20', 'App\\Models\\MarriageCertificateTransaction', 14, NULL),
(112, 'No. 0000001 U', 'Armbel Bernal', '2026-06-17 17:18:17', 'Form 5IC', 'Completed', '2026-06-17 17:18:17', '2026-06-17 20:45:28', 'App\\Models\\OrTransaction', 3, NULL),
(114, 'No. 0000004 U', 'Armbel Bernal', '2026-06-17 21:00:20', 'Form 5IC', 'Cancelled', '2026-06-17 21:00:20', '2026-06-19 01:11:41', 'App\\Models\\OrTransaction', 5, '2026-06-19 01:11:41'),
(115, 'No. 2027029', 'Armbel Besalo Bernal & Cleofe Dioneda Villanueva', '2026-06-17 23:43:28', 'Form 10', 'Cancelled', '2026-06-17 23:43:28', '2026-06-19 01:07:07', 'App\\Models\\MarriageCertificateTransaction', 15, '2026-06-19 01:07:07'),
(116, 'No. 2027030', 'Armbel Besalo Bernal & Cleofe Dioneda Villanueva', '2026-06-17 23:55:16', 'Form 10', 'Cancelled', '2026-06-17 23:55:16', '2026-06-19 01:07:07', 'App\\Models\\MarriageCertificateTransaction', 16, '2026-06-19 01:07:07'),
(117, 'No. 2027031', 'Armbel Besalo Bernal & Cleofe Dioneda Villanueva', '2026-06-18 00:15:33', 'Form 10', 'Completed', '2026-06-18 00:15:33', '2026-06-18 00:15:33', 'App\\Models\\MarriageCertificateTransaction', 17, NULL),
(118, 'No. 2027032', 'Armbel Besalo Bernal & Cleofe Dioneda Villanueva', '2026-06-18 00:38:14', 'Form 10', 'Completed', '2026-06-18 00:38:14', '2026-06-18 00:38:14', 'App\\Models\\MarriageCertificateTransaction', 18, NULL),
(119, 'No. 2027033', 'Armbel Besalo Bernal & Cleofe Dioneda Villanueva', '2026-06-18 00:40:27', 'Form 10', 'Completed', '2026-06-18 00:40:27', '2026-06-18 00:40:27', 'App\\Models\\MarriageCertificateTransaction', 19, NULL),
(120, '0000008', 'Armbel Bernal', '2026-06-18 00:51:31', 'Form 56', 'Completed', '2026-06-18 00:51:31', '2026-06-18 00:51:31', 'App\\Models\\OrRptTransaction', 8, NULL);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `email_verified_at` DATETIME NULL,
  `password` VARCHAR(255) NOT NULL,
  `remember_token` VARCHAR(255) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `mobile` VARCHAR(255) NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'activated',
  `added_by` VARCHAR(255) NULL,
  `username` VARCHAR(255) NULL,
  `archived_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `mobile`, `status`, `added_by`, `username`, `archived_at`) VALUES
(1, 'Marlaw Sol Emata', 'memata@solemitsolutions.com', '2026-06-13 05:54:19', '$2y$12$xj5ikJ8l45EG4adfWFEx5.mz68RdJbgiPx/4aV1/6pyv22WKVTrWC', 'RURqWdHVZ4c2NF8JBB1Zyi6ZHY4fHuabfdOJhYY988Js6nn4jzIjVRN7Jx7e', '2023-01-14 09:00:00', '2026-06-16 12:28:14', '+63 912 345 6780', 'activated', 'System', 'memata', NULL),
(2, 'Juan Dela Cruz', 'jdelacruz@solemitsolutions.com', NULL, '$2y$12$EI66X0mmtIia6zkdEWPyWeCeCkCQcXSc2H9F9ie4F9ZPckz2tnesK', NULL, '2023-03-02 10:30:00', '2026-06-16 06:59:36', '+63 912 345 6781', 'disabled', 'Marlaw Sol Emata', 'jdelacruz', NULL),
(3, 'Maria Santos', 'msantos@solemitsolutions.com', NULL, '$2y$12$syll0tLjS57QF4QeqPeyc.YZBz4N1PZ1WHt14bdr5/LlzloKdpzTi', NULL, '2023-08-19 11:15:00', '2026-06-16 06:59:36', '+63 912 345 6782', 'disabled', 'Marlaw Sol Emata', 'msantos', NULL),
(4, 'Pedro Reyes', 'preyes@solemitsolutions.com', NULL, '$2y$12$rjihwkOx1cTixyvaF.XrG.wyqFwoKH7/9xDxHuB.rlWAqMxV5ZdRe', NULL, '2024-02-05 14:20:00', '2026-06-16 06:59:36', '+63 912 345 6783', 'disabled', 'Marlaw Sol Emata', 'preyes', NULL),
(5, 'Ana Garcia', 'agarcia@solemitsolutions.com', NULL, '$2y$12$ApB0kehBodH8YGF6qNLG9OVwz4d5uFIXdayct1EddSisZhn4SL7xS', NULL, '2024-06-17 09:45:00', '2026-06-16 07:05:28', '+63 912 345 6784', 'disabled', 'Marlaw Sol Emata', 'agarcia', NULL),
(6, 'Jose Ramirez', 'jramirez@solemitsolutions.com', NULL, '$2y$12$S0Rjk25V.wXkjzMVVIzN0.jXchDcZUS3ZQqj4Ff6cZvYCw8Z.A//C', NULL, '2024-11-30 13:10:00', '2026-06-16 06:59:36', '+63 912 345 6785', 'disabled', 'Marlaw Sol Emata', 'jramirez', NULL),
(7, 'Carmen Lopez', 'clopez@solemitsolutions.com', NULL, '$2y$12$YWHBZNdWW7kTWORIVQSFiOhn37A/zpnuwj.YkP7vkuzgTQERmK6nu', NULL, '2025-04-12 08:30:00', '2026-06-19 22:32:04', '+63 912 345 6786', 'activated', 'Marlaw Sol Emata', 'clopez', NULL),
(8, 'Ramon Torres', 'rtorres@solemitsolutions.com', NULL, '$2y$12$rrXMH16ZqP6fMX4Vf//fPuTskNmTe0lSd2qC0OcT4tXZTfUabgX4S', 'ZvEv9j1GmPmhuZrAhYSUf0vMk9TEKaRRLLcVIAlwOhvcDU5e6dwUc3sXDopl', '2025-09-25 16:00:00', '2026-06-19 01:28:19', '+63 912 345 6787', 'activated', 'Marlaw Sol Emata', 'rtorres', NULL),
(9, 'Test User One', 'testuser1@example.com', NULL, '$2y$12$Ld4OMRII523iOEfL.OZxpOhYxLgzcjNmh44cN1MzY1ccORnU3p782', NULL, '2026-06-15 23:48:29', '2026-06-16 00:06:39', '09179998888', 'archived', 'Marlaw Sol Emata', 'testuser1', '2026-06-16 00:06:39'),
(10, 'Armbel Bernal', 'bernalarmbelb@outlook.com', NULL, '$2y$12$xF1ZorbG3CvCB1g7qfPpxO8sHC6hyrEpELgPg9MLf3np.umiY8cha', NULL, '2026-06-16 00:46:58', '2026-06-19 02:32:38', '639475113910', 'archived', 'Marlaw Sol Emata', 'rootAdmin', '2026-06-19 02:32:38'),
(12, 'Cleofe Villanueva', 'cvillanueva@gmail.com', NULL, '$2y$12$GCfVXiF65NqEN0sHHn3wXe0j7L6KvhOazyUxGsQArApLXAbKbTiYG', NULL, '2026-06-16 12:32:24', '2026-06-16 12:32:24', '639475113910', 'activated', 'Marlaw Sol Emata', 'admin', NULL);

-- Foreign keys
ALTER TABLE `activity_logs` ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
ALTER TABLE `cancel_requests` ADD CONSTRAINT `cancel_requests_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
ALTER TABLE `cancel_requests` ADD CONSTRAINT `cancel_requests_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
ALTER TABLE `cancel_requests` ADD CONSTRAINT `cancel_requests_transaction_log_id_foreign` FOREIGN KEY (`transaction_log_id`) REFERENCES `transaction_logs` (`id`) ON DELETE CASCADE;
ALTER TABLE `ctc_corporation_transactions` ADD CONSTRAINT `ctc_corporation_transactions_form_stock_id_foreign` FOREIGN KEY (`form_stock_id`) REFERENCES `form_stocks` (`id`) ON DELETE CASCADE;
ALTER TABLE `ctc_individual_transactions` ADD CONSTRAINT `ctc_individual_transactions_form_stock_id_foreign` FOREIGN KEY (`form_stock_id`) REFERENCES `form_stocks` (`id`) ON DELETE CASCADE;
ALTER TABLE `form_batches` ADD CONSTRAINT `form_batches_form_stock_id_foreign` FOREIGN KEY (`form_stock_id`) REFERENCES `form_stocks` (`id`) ON DELETE CASCADE;
ALTER TABLE `marriage_certificate_transactions` ADD CONSTRAINT `marriage_certificate_transactions_form_stock_id_foreign` FOREIGN KEY (`form_stock_id`) REFERENCES `form_stocks` (`id`) ON DELETE NO ACTION;
ALTER TABLE `or_rpt_transactions` ADD CONSTRAINT `or_rpt_transactions_form_stock_id_foreign` FOREIGN KEY (`form_stock_id`) REFERENCES `form_stocks` (`id`) ON DELETE CASCADE;
ALTER TABLE `or_transactions` ADD CONSTRAINT `or_transactions_form_stock_id_foreign` FOREIGN KEY (`form_stock_id`) REFERENCES `form_stocks` (`id`) ON DELETE CASCADE;
ALTER TABLE `role_module_permissions` ADD CONSTRAINT `role_module_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
ALTER TABLE `role_user` ADD CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `role_user` ADD CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

SET FOREIGN_KEY_CHECKS = 1;
