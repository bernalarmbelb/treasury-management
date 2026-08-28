-- MySQL / MariaDB dump generated from SQLite
-- Source: database/database.sqlite
-- Generated: 2026-08-29T02:49:16

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
(138, 10, 'Collection Management - Add Entry - Corporation Cedula - CCC 202600001', '2026-07-06 00:59:54', '2026-07-06 00:59:54'),
(139, 10, 'Collection Management - Add Entry - Individual Cedula - CCI 202600001', '2026-07-06 02:54:26', '2026-07-06 02:54:26'),
(140, 10, 'Collection Management - Add Entry - Corporation Cedula - CCC 202600002', '2026-07-06 02:56:01', '2026-07-06 02:56:01'),
(141, 10, 'Collection Management - Add Entry - Marriage License - No. 8104351', '2026-07-06 03:04:59', '2026-07-06 03:04:59'),
(142, 10, 'Reporting & Abstract - Export Report - Treasurer\'s Monthly Report of Accountability for Accountable Forms - July 2026', '2026-07-06 03:09:26', '2026-07-06 03:09:26'),
(143, 10, 'Reporting & Abstract - Export Report - Consolidated Report of Accountability for Accountable Forms (CRAAF) - July 2026', '2026-07-06 03:11:40', '2026-07-06 03:11:40'),
(144, 10, 'Reporting & Abstract - Export Report - Summary of Community Tax Certificate - July 2026', '2026-07-06 03:12:17', '2026-07-06 03:12:17'),
(145, 10, 'Reporting & Abstract - Export Report - Abstract of Community Tax Certificate - July 2026', '2026-07-06 03:50:41', '2026-07-06 03:50:41'),
(146, 10, 'Reporting & Abstract - Export Report - Abstract of Community Tax Certificate - July 2026', '2026-07-06 04:12:24', '2026-07-06 04:12:24'),
(147, 10, 'Reporting & Abstract - Export Report - Abstract of Community Tax Certificate - July 2026', '2026-07-06 04:30:17', '2026-07-06 04:30:17'),
(148, 10, 'Collection Management - Add Entry - Burial - No. 6551381 C', '2026-07-06 15:38:18', '2026-07-06 15:38:18'),
(149, 10, 'Reporting & Abstract - Export Report - Treasurer\'s Monthly Report of Accountability for Accountable Forms - July 2026 – August 2026', '2026-08-23 15:57:34', '2026-08-23 15:57:34'),
(150, 10, 'Reporting & Abstract - Export Report - Abstract of Community Tax Certificate - July 2026 – August 2026', '2026-08-23 15:58:48', '2026-08-23 15:58:48'),
(151, 10, 'Collection Management - Add Entry - Burial - No. 6551381 C', '2026-08-23 16:22:43', '2026-08-23 16:22:43'),
(152, 10, 'Collection Management - Add Entry - Burial - No. 6551381 C', '2026-08-23 17:24:09', '2026-08-23 17:24:09'),
(153, 10, 'User Management - Reset Password - Cleofe Villanueva', '2026-08-23 17:29:06', '2026-08-23 17:29:06'),
(154, 10, 'Collection Management - Add Entry - Marriage License - No. 8104352', '2026-08-23 18:19:46', '2026-08-23 18:19:46'),
(155, 10, 'Collection Management - Add Entry - OR RPT - 4218271', '2026-08-23 19:46:12', '2026-08-23 19:46:12'),
(156, 10, 'Collection Management - Add Entry - Official Receipt - No. 0000001', '2026-08-23 19:48:23', '2026-08-23 19:48:23'),
(157, 10, 'Collection Management - Cancel Transaction - Burial - Armbel Bernal - No. 6551381 C', '2026-08-24 00:30:31', '2026-08-24 00:30:31'),
(158, 10, 'Collection Management - Cancel Transaction - Marriage License - Armbel Besalo Bernal & Cleofe Dioneda Villanueva - No. 8104351', '2026-08-24 00:30:43', '2026-08-24 00:30:43'),
(159, 10, 'Collection Management - Add Entry - Burial - No. 6551381 C', '2026-08-24 00:43:42', '2026-08-24 00:43:42'),
(160, 10, 'Collection Management - Add Entry - Corporation Cedula - CCC 202600003', '2026-08-24 00:55:19', '2026-08-24 00:55:19'),
(161, 10, 'Bank Deposit - Record Deposit - 4 collection(s) - ₱75,871.00', '2026-08-24 00:57:43', '2026-08-24 00:57:43'),
(162, 10, 'Bank Deposit - Confirm Online - CCC 202600003', '2026-08-24 00:57:46', '2026-08-24 00:57:46'),
(163, 10, 'Bank Deposit - Confirm Online - No. 6551381 C', '2026-08-24 00:57:47', '2026-08-24 00:57:47'),
(164, 10, 'Bank Deposit - Confirm Online - 4218271', '2026-08-24 00:57:48', '2026-08-24 00:57:48'),
(165, 10, 'Bank Deposit - Confirm Online - No. 6551381 C', '2026-08-24 00:57:49', '2026-08-24 00:57:49'),
(166, 10, 'Bank Deposit - Mark Bounced - Cheque 626895', '2026-08-24 01:01:37', '2026-08-24 01:01:37'),
(167, 10, 'Collection Management - Add Entry - Burial - No. 6551382 C', '2026-08-24 04:59:12', '2026-08-24 04:59:12'),
(168, 10, 'Cheque Management - Cancel Cheque - No. 626923', '2026-08-24 05:02:53', '2026-08-24 05:02:53'),
(169, 10, 'User Management - Reset Password - Ramon Torres', '2026-08-24 12:41:06', '2026-08-24 12:41:06'),
(170, 10, 'Collection Management - Add Entry - Burial - No. 6551383 C', '2026-08-24 13:03:06', '2026-08-24 13:03:06'),
(171, 10, 'Collection Management - Add Entry - Burial - No. 6551384 C', '2026-08-24 13:03:26', '2026-08-24 13:03:26'),
(172, 10, 'Bank Deposit - Confirm Online - No. 6551382 C', '2026-08-24 14:18:25', '2026-08-24 14:18:25'),
(173, 10, 'Bank Deposit - Mark Bounced - Cheque 626932', '2026-08-24 14:18:44', '2026-08-24 14:18:44'),
(174, 10, 'Collection Management - Cancel Transaction - Official Receipt - Marlaw Emata - No. 0000001 U', '2026-08-24 14:19:03', '2026-08-24 14:19:03');

DROP TABLE IF EXISTS `bank_accounts`;
CREATE TABLE `bank_accounts` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `bank_name` VARCHAR(255) NOT NULL,
  `account_number` VARCHAR(255) NOT NULL,
  `account_name` VARCHAR(255) NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT '1',
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `opening_balance` DECIMAL(15,2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bank_accounts` (`id`, `bank_name`, `account_number`, `account_name`, `is_active`, `created_at`, `updated_at`, `opening_balance`) VALUES
(1, 'LBP — Sorsogon Branch', '00782-1019-43', 'Municipality of Prieto Diaz', 1, '2026-08-23 00:38:28', '2026-08-23 00:38:28', 0),
(2, 'DBP — Sorsogon Branch', '1462-1005-88', 'Municipality of Prieto Diaz', 1, '2026-08-23 00:38:28', '2026-08-23 00:38:28', 0);

DROP TABLE IF EXISTS `burial_permit_transactions`;
CREATE TABLE `burial_permit_transactions` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `form_stock_id` BIGINT NOT NULL,
  `certificate_number` VARCHAR(255) NOT NULL,
  `series_letter` VARCHAR(255) NULL,
  `applicant_name` VARCHAR(255) NULL,
  `city_municipality` VARCHAR(255) NULL,
  `province` VARCHAR(255) NULL,
  `permission_type` VARCHAR(255) NULL,
  `deceased_name` VARCHAR(255) NOT NULL,
  `nationality` VARCHAR(255) NULL,
  `age` BIGINT NULL,
  `sex` VARCHAR(255) NULL,
  `date_of_death` DATE NULL,
  `cause_of_death` VARCHAR(255) NULL,
  `cemetery_name` VARCHAR(255) NULL,
  `infectious` VARCHAR(255) NULL,
  `embalmed` VARCHAR(255) NULL,
  `disposition` VARCHAR(255) NULL,
  `fee_amount` DECIMAL(15,2) NULL,
  `date_issued` DATE NULL,
  `municipal_secretary` VARCHAR(255) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `burial_permit_transactions` (`id`, `form_stock_id`, `certificate_number`, `series_letter`, `applicant_name`, `city_municipality`, `province`, `permission_type`, `deceased_name`, `nationality`, `age`, `sex`, `date_of_death`, `cause_of_death`, `cemetery_name`, `infectious`, `embalmed`, `disposition`, `fee_amount`, `date_issued`, `municipal_secretary`, `created_at`, `updated_at`) VALUES
(1, 8, '6551381', 'C', 'Armbel Bernal', 'Sorsogon City', 'Sorsogon', 'Remove', 'Sample deceased', 'Filipino', 100, 'Male', '1971-01-29 00:00:00', 'got scared of beetle', 'acretia', NULL, NULL, NULL, 25000, '2026-07-06 00:00:00', 'Armbel Bernal', '2026-07-06 15:38:18', '2026-07-06 15:38:18'),
(2, 8, '6551381', 'C', 'Armbel Bernal', 'Sorsogon City', 'Sorsogon', 'Remove', 'Sample deceased', 'Filipino', 35, 'Male', '2026-08-23 00:00:00', 'got scared of beetle', 'acretia', NULL, NULL, NULL, 500, '2026-08-23 00:00:00', 'Gemma D. Ferrer', '2026-08-23 16:22:43', '2026-08-23 16:22:43'),
(3, 8, '6551381', 'C', 'Armbel Bernal', 'Sorsogon City', 'Sorsogon', 'Inter', 'Sample deceased', 'Filipino', 123, 'Male', '2026-08-23 00:00:00', NULL, NULL, NULL, NULL, NULL, 500, '2026-08-23 00:00:00', 'Armbel Bernal', '2026-08-23 17:24:09', '2026-08-23 17:24:09'),
(4, 8, '6551381', 'C', 'Armbel Bernal', 'Sorsogon City', 'Sorsogon', 'Inter', 'Sample deceased', 'Filipino', 35, 'Male', '2026-08-24 00:00:00', 'got scared of beetle', 'acretia', NULL, NULL, NULL, 5000, '2026-08-24 00:00:00', 'Armbel Bernal', '2026-08-24 00:43:42', '2026-08-24 00:43:42'),
(5, 8, '6551382', 'C', 'Armbel Bernal', 'Sorsogon City', 'Sorsogon', 'Disinter', 'Sample deceased', 'Filipino', 34, 'Male', '2026-08-24 00:00:00', 'got scared of beetle', NULL, 'zomboid', 'Yes', 'test', 5000, '2026-08-24 00:00:00', 'Gemma D. Ferrer', '2026-08-24 04:59:12', '2026-08-24 04:59:12'),
(6, 8, '6551383', 'C', 'Armbel Bernal', 'Sorsogon City', 'Sorsogon', 'Inter', 'Sample deceased', 'Filipino', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 500, '2026-08-24 00:00:00', 'Armbel Bernal', '2026-08-24 13:03:06', '2026-08-24 13:03:06'),
(7, 8, '6551384', 'C', 'Armbel Bernal', NULL, NULL, 'Inter', 'Sample deceased', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 500, '2026-08-24 00:00:00', 'Armbel Bernal', '2026-08-24 13:03:25', '2026-08-24 13:03:25');

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

DROP TABLE IF EXISTS `cheques`;
CREATE TABLE `cheques` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `bank_account_id` BIGINT NOT NULL,
  `account_name` VARCHAR(255) NOT NULL,
  `cheque_date` DATE NOT NULL,
  `check_number` VARCHAR(255) NOT NULL,
  `pay_to_order_of` VARCHAR(255) NOT NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  `amount_in_words` VARCHAR(255) NOT NULL,
  `nature_of_payment` VARCHAR(255) NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'Issued',
  `created_by` VARCHAR(255) NULL,
  `archived_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `recon_status` VARCHAR(255) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cheques_bank_account_id_check_number_unique` (`bank_account_id`, `check_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cheques` (`id`, `bank_account_id`, `account_name`, `cheque_date`, `check_number`, `pay_to_order_of`, `amount`, `amount_in_words`, `nature_of_payment`, `status`, `created_by`, `archived_at`, `created_at`, `updated_at`, `recon_status`) VALUES
(1, 1, 'Municipality of Prieto Diaz', '2013-12-09 00:00:00', '626877', 'JRE Agrivet Supply', 19772.79, 'Nineteen thousand seven hundred seventy two and 79/100 pesos', 'purchase', 'Issued', 'Seeder', NULL, '2013-12-09 09:54:00', '2026-08-23 00:38:28', 'pending'),
(2, 1, 'Municipality of Prieto Diaz', '2013-12-10 00:00:00', '626878', 'B. Esperida Trading', 24523.86, 'Twenty four thousand five hundred twenty three and 86/100 pesos', 'purchase', 'Issued', 'Seeder', NULL, '2013-12-10 09:58:00', '2026-08-23 00:38:28', 'pending'),
(3, 1, 'Municipality of Prieto Diaz', '2013-12-11 00:00:00', '626880', '', 0, '', NULL, 'Cancelled', 'Seeder', NULL, '2013-12-11 09:53:00', '2026-08-23 00:38:28', 'pending'),
(4, 1, 'Municipality of Prieto Diaz', '2013-12-16 00:00:00', '626882', 'CEE / Municipal Treasurer', 22800, 'Twenty two thousand eight hundred and 00/100 pesos', 'withdrawal', 'Issued', 'Seeder', NULL, '2013-12-16 09:15:00', '2026-08-23 00:38:28', 'pending'),
(5, 1, 'Municipality of Prieto Diaz', '2013-12-19 00:00:00', '626884', 'Sirit Const. & Supply', 31787.7, 'Thirty one thousand seven hundred eighty seven and 70/100 pesos', 'purchase', 'Issued', 'Seeder', NULL, '2013-12-19 09:48:00', '2026-08-23 00:38:28', 'pending'),
(6, 1, 'Municipality of Prieto Diaz', '2013-12-27 00:00:00', '626886', 'CEE / Municipal Treasurer', 6090, 'Six thousand ninety and 00/100 pesos', 'withdrawal', 'Issued', 'Seeder', NULL, '2013-12-27 09:01:00', '2026-08-23 00:38:28', 'pending'),
(7, 1, 'Municipality of Prieto Diaz', '2026-08-04 09:07:00', '626887', 'JRE Agrivet Supply', 61822.08, 'Sixty one thousand eight hundred twenty two and 08/100 pesos', 'purchase', 'Issued', 'Seeder', NULL, '2026-08-04 09:07:00', '2026-08-04 09:07:00', 'pending'),
(8, 1, 'Municipality of Prieto Diaz', '2026-08-07 09:14:00', '626888', 'B. Esperida Trading', 20604.33, 'Twenty thousand six hundred four and 33/100 pesos', 'withdrawal', 'Issued', 'Seeder', NULL, '2026-08-07 09:14:00', '2026-08-07 09:14:00', 'pending'),
(9, 1, 'Municipality of Prieto Diaz', '2026-08-10 09:21:00', '626889', 'Sirit Const. & Supply', 80717.15, 'Eighty thousand seven hundred seventeen and 15/100 pesos', 'salary', 'Issued', 'Seeder', NULL, '2026-08-10 09:21:00', '2026-08-10 09:21:00', 'pending'),
(10, 1, 'Municipality of Prieto Diaz', '2026-08-13 09:28:00', '626890', 'Electroworld Inc.', 78729.09, 'Seventy eight thousand seven hundred twenty nine and 09/100 pesos', 'supplies', 'Issued', 'Seeder', NULL, '2026-08-13 09:28:00', '2026-08-13 09:28:00', 'pending'),
(11, 1, 'Municipality of Prieto Diaz', '2026-08-16 09:35:00', '626891', 'Sorsogon Electric Coop II', 66669.87, 'Sixty six thousand six hundred sixty nine and 87/100 pesos', 'services', 'Issued', 'Seeder', NULL, '2026-08-16 09:35:00', '2026-08-16 09:35:00', 'pending'),
(12, 1, 'Municipality of Prieto Diaz', '2026-08-19 09:42:00', '626892', 'Prieto Diaz Waterworks', 54414.89, 'Fifty four thousand four hundred fourteen and 89/100 pesos', 'fuel', 'Issued', 'Seeder', NULL, '2026-08-19 09:42:00', '2026-08-19 09:42:00', 'pending'),
(13, 1, 'Municipality of Prieto Diaz', '2026-08-22 09:49:00', '626893', 'Grand Imperial Hardware', 56183.58, 'Fifty six thousand one hundred eighty three and 58/100 pesos', 'remittance', 'Issued', 'Seeder', NULL, '2026-08-22 09:49:00', '2026-08-22 09:49:00', 'pending'),
(14, 1, 'Municipality of Prieto Diaz', '2026-08-25 09:56:00', '626894', 'Petron - Prieto Diaz', 67407.46, 'Sixty seven thousand four hundred seven and 46/100 pesos', 'purchase', 'Issued', 'Seeder', NULL, '2026-08-25 09:56:00', '2026-08-25 09:56:00', 'pending'),
(15, 1, 'Municipality of Prieto Diaz', '2026-08-28 09:03:00', '626895', 'MDF Office Supplies', 32340.87, 'Thirty two thousand three hundred forty and 87/100 pesos', 'withdrawal', 'Issued', 'Seeder', NULL, '2026-08-28 09:03:00', '2026-08-24 01:01:37', 'failed'),
(16, 1, 'Municipality of Prieto Diaz', '2026-08-03 09:10:00', '626896', 'Bicol Medical Supplies', 76157.71, 'Seventy six thousand one hundred fifty seven and 71/100 pesos', 'salary', 'Issued', 'Seeder', NULL, '2026-08-03 09:10:00', '2026-08-03 09:10:00', 'pending'),
(17, 1, 'Municipality of Prieto Diaz', '2026-08-06 09:17:00', '626897', 'Sorsogon Printing Press', 52966.48, 'Fifty two thousand nine hundred sixty six and 48/100 pesos', 'supplies', 'Issued', 'Seeder', NULL, '2026-08-06 09:17:00', '2026-08-06 09:17:00', 'pending'),
(18, 1, 'Municipality of Prieto Diaz', '2026-08-09 09:24:00', '626898', 'Ace Hardware Sorsogon', 76058.57, 'Seventy six thousand fifty eight and 57/100 pesos', 'services', 'Issued', 'Seeder', NULL, '2026-08-09 09:24:00', '2026-08-09 09:24:00', 'pending'),
(19, 1, 'Municipality of Prieto Diaz', '2026-08-12 09:31:00', '626899', 'Rural Health Unit Supplies', 19593.96, 'Nineteen thousand five hundred ninety three and 96/100 pesos', 'fuel', 'Cancelled', 'Seeder', NULL, '2026-08-12 09:31:00', '2026-08-12 09:31:00', 'pending'),
(20, 1, 'Municipality of Prieto Diaz', '2026-08-15 09:38:00', '626900', 'Ferrer Catering Services', 30952.12, 'Thirty thousand nine hundred fifty two and 12/100 pesos', 'remittance', 'Issued', 'Seeder', NULL, '2026-08-15 09:38:00', '2026-08-15 09:38:00', 'pending'),
(21, 1, 'Municipality of Prieto Diaz', '2026-08-18 09:45:00', '626901', 'CEE / Municipal Treasurer', 76864.77, 'Seventy six thousand eight hundred sixty four and 77/100 pesos', 'purchase', 'Issued', 'Seeder', NULL, '2026-08-18 09:45:00', '2026-08-18 09:45:00', 'pending'),
(22, 1, 'Municipality of Prieto Diaz', '2026-08-21 09:52:00', '626902', 'DPWH Materials Supply', 24708.69, 'Twenty four thousand seven hundred eight and 69/100 pesos', 'withdrawal', 'Issued', 'Seeder', NULL, '2026-08-21 09:52:00', '2026-08-21 09:52:00', 'pending'),
(23, 1, 'Municipality of Prieto Diaz', '2026-08-24 09:59:00', '626903', 'PhilHealth Remittance', 46157.49, 'Forty six thousand one hundred fifty seven and 49/100 pesos', 'salary', 'Issued', 'Seeder', NULL, '2026-08-24 09:59:00', '2026-08-24 09:59:00', 'pending'),
(24, 1, 'Municipality of Prieto Diaz', '2026-08-27 09:06:00', '626904', 'GSIS Remittance', 50308.91, 'Fifty thousand three hundred eight and 91/100 pesos', 'supplies', 'Issued', 'Seeder', NULL, '2026-08-27 09:06:00', '2026-08-27 09:06:00', 'pending'),
(25, 1, 'Municipality of Prieto Diaz', '2026-08-02 09:13:00', '626905', 'BIR - Withholding Tax', 58945.8, 'Fifty eight thousand nine hundred forty five and 80/100 pesos', 'services', 'Issued', 'Seeder', NULL, '2026-08-02 09:13:00', '2026-08-02 09:13:00', 'pending'),
(26, 1, 'Municipality of Prieto Diaz', '2026-08-05 09:20:00', '626906', 'LGU Payroll - Casual', 89139.11, 'Eighty nine thousand one hundred thirty nine and 11/100 pesos', 'fuel', 'Issued', 'Seeder', NULL, '2026-08-05 09:20:00', '2026-08-05 09:20:00', 'pending'),
(27, 1, 'Municipality of Prieto Diaz', '2026-08-08 09:27:00', '626907', 'JRE Agrivet Supply', 61769.13, 'Sixty one thousand seven hundred sixty nine and 13/100 pesos', 'remittance', 'Issued', 'Seeder', NULL, '2026-08-08 09:27:00', '2026-08-08 09:27:00', 'pending'),
(28, 1, 'Municipality of Prieto Diaz', '2026-08-11 09:34:00', '626908', 'B. Esperida Trading', 25938.95, 'Twenty five thousand nine hundred thirty eight and 95/100 pesos', 'purchase', 'Issued', 'Seeder', NULL, '2026-08-11 09:34:00', '2026-08-11 09:34:00', 'pending'),
(29, 1, 'Municipality of Prieto Diaz', '2026-08-14 09:41:00', '626909', 'Sirit Const. & Supply', 81445.83, 'Eighty one thousand four hundred forty five and 83/100 pesos', 'withdrawal', 'Issued', 'Seeder', NULL, '2026-08-14 09:41:00', '2026-08-14 09:41:00', 'pending'),
(30, 1, 'Municipality of Prieto Diaz', '2026-08-17 09:48:00', '626910', 'Electroworld Inc.', 69084.5, 'Sixty nine thousand eighty four and 50/100 pesos', 'salary', 'Issued', 'Seeder', NULL, '2026-08-17 09:48:00', '2026-08-17 09:48:00', 'pending'),
(31, 1, 'Municipality of Prieto Diaz', '2026-08-20 09:55:00', '626911', 'Sorsogon Electric Coop II', 71698.09, 'Seventy one thousand six hundred ninety eight and 09/100 pesos', 'supplies', 'Issued', 'Seeder', NULL, '2026-08-20 09:55:00', '2026-08-20 09:55:00', 'pending'),
(32, 1, 'Municipality of Prieto Diaz', '2026-08-23 09:02:00', '626912', 'Prieto Diaz Waterworks', 34733.13, 'Thirty four thousand seven hundred thirty three and 13/100 pesos', 'services', 'Cancelled', 'Seeder', NULL, '2026-08-23 09:02:00', '2026-08-23 09:02:00', 'pending'),
(33, 1, 'Municipality of Prieto Diaz', '2026-08-26 09:09:00', '626913', 'Grand Imperial Hardware', 91802.91, 'Ninety one thousand eight hundred two and 91/100 pesos', 'fuel', 'Issued', 'Seeder', NULL, '2026-08-26 09:09:00', '2026-08-26 09:09:00', 'pending'),
(34, 1, 'Municipality of Prieto Diaz', '2026-08-01 09:16:00', '626914', 'Petron - Prieto Diaz', 49251.72, 'Forty nine thousand two hundred fifty one and 72/100 pesos', 'remittance', 'Issued', 'Seeder', NULL, '2026-08-01 09:16:00', '2026-08-01 09:16:00', 'pending'),
(35, 1, 'Municipality of Prieto Diaz', '2026-08-04 09:23:00', '626915', 'MDF Office Supplies', 44104.68, 'Forty four thousand one hundred four and 68/100 pesos', 'purchase', 'Issued', 'Seeder', NULL, '2026-08-04 09:23:00', '2026-08-04 09:23:00', 'pending'),
(36, 1, 'Municipality of Prieto Diaz', '2026-08-07 09:30:00', '626916', 'Bicol Medical Supplies', 42895.7, 'Forty two thousand eight hundred ninety five and 70/100 pesos', 'withdrawal', 'Issued', 'Seeder', NULL, '2026-08-07 09:30:00', '2026-08-07 09:30:00', 'pending'),
(37, 1, 'Municipality of Prieto Diaz', '2026-08-10 09:37:00', '626917', 'Sorsogon Printing Press', 10007.65, 'Ten thousand seven and 65/100 pesos', 'salary', 'Issued', 'Seeder', NULL, '2026-08-10 09:37:00', '2026-08-10 09:37:00', 'pending'),
(38, 1, 'Municipality of Prieto Diaz', '2026-08-13 09:44:00', '626918', 'Ace Hardware Sorsogon', 10222.6, 'Ten thousand two hundred twenty two and 60/100 pesos', 'supplies', 'Issued', 'Seeder', NULL, '2026-08-13 09:44:00', '2026-08-13 09:44:00', 'pending'),
(39, 1, 'Municipality of Prieto Diaz', '2026-08-16 09:51:00', '626919', 'Rural Health Unit Supplies', 9656.5, 'Nine thousand six hundred fifty six and 50/100 pesos', 'services', 'Issued', 'Seeder', NULL, '2026-08-16 09:51:00', '2026-08-16 09:51:00', 'pending'),
(40, 1, 'Municipality of Prieto Diaz', '2026-08-19 09:58:00', '626920', 'Ferrer Catering Services', 69862.55, 'Sixty nine thousand eight hundred sixty two and 55/100 pesos', 'fuel', 'Issued', 'Seeder', NULL, '2026-08-19 09:58:00', '2026-08-19 09:58:00', 'pending'),
(41, 1, 'Municipality of Prieto Diaz', '2026-08-22 09:05:00', '626921', 'CEE / Municipal Treasurer', 86422.97, 'Eighty six thousand four hundred twenty two and 97/100 pesos', 'remittance', 'Issued', 'Seeder', NULL, '2026-08-22 09:05:00', '2026-08-22 09:05:00', 'pending'),
(42, 1, 'Municipality of Prieto Diaz', '2026-08-25 09:12:00', '626922', 'DPWH Materials Supply', 4204.61, 'Four thousand two hundred four and 61/100 pesos', 'purchase', 'Issued', 'Seeder', NULL, '2026-08-25 09:12:00', '2026-08-25 09:12:00', 'pending'),
(43, 1, 'Municipality of Prieto Diaz', '2026-08-28 09:19:00', '626923', 'PhilHealth Remittance', 11247.67, 'Eleven thousand two hundred forty seven and 67/100 pesos', 'withdrawal', 'Cancelled', 'Seeder', NULL, '2026-08-28 09:19:00', '2026-08-24 05:02:53', 'pending'),
(44, 1, 'Municipality of Prieto Diaz', '2026-08-03 09:26:00', '626924', 'GSIS Remittance', 63451.45, 'Sixty three thousand four hundred fifty one and 45/100 pesos', 'salary', 'Issued', 'Seeder', NULL, '2026-08-03 09:26:00', '2026-08-03 09:26:00', 'pending'),
(45, 1, 'Municipality of Prieto Diaz', '2026-08-06 09:33:00', '626925', 'BIR - Withholding Tax', 94853.17, 'Ninety four thousand eight hundred fifty three and 17/100 pesos', 'supplies', 'Cancelled', 'Seeder', NULL, '2026-08-06 09:33:00', '2026-08-06 09:33:00', 'pending'),
(46, 1, 'Municipality of Prieto Diaz', '2026-08-09 09:40:00', '626926', 'LGU Payroll - Casual', 43313.86, 'Forty three thousand three hundred thirteen and 86/100 pesos', 'services', 'Issued', 'Seeder', NULL, '2026-08-09 09:40:00', '2026-08-09 09:40:00', 'pending'),
(47, 1, 'Municipality of Prieto Diaz', '2026-08-12 09:47:00', '626927', 'JRE Agrivet Supply', 60962.31, 'Sixty thousand nine hundred sixty two and 31/100 pesos', 'fuel', 'Issued', 'Seeder', NULL, '2026-08-12 09:47:00', '2026-08-12 09:47:00', 'pending'),
(48, 1, 'Municipality of Prieto Diaz', '2026-08-15 09:54:00', '626928', 'B. Esperida Trading', 5470.25, 'Five thousand four hundred seventy and 25/100 pesos', 'remittance', 'Issued', 'Seeder', NULL, '2026-08-15 09:54:00', '2026-08-15 09:54:00', 'pending'),
(49, 1, 'Municipality of Prieto Diaz', '2026-08-18 09:01:00', '626929', 'Sirit Const. & Supply', 4823.61, 'Four thousand eight hundred twenty three and 61/100 pesos', 'purchase', 'Issued', 'Seeder', NULL, '2026-08-18 09:01:00', '2026-08-18 09:01:00', 'pending'),
(50, 1, 'Municipality of Prieto Diaz', '2026-08-21 09:08:00', '626930', 'Electroworld Inc.', 80082.26, 'Eighty thousand eighty two and 26/100 pesos', 'withdrawal', 'Issued', 'Seeder', NULL, '2026-08-21 09:08:00', '2026-08-21 09:08:00', 'pending'),
(51, 1, 'Municipality of Prieto Diaz', '2026-08-24 09:15:00', '626931', 'Sorsogon Electric Coop II', 22301.75, 'Twenty two thousand three hundred one and 75/100 pesos', 'salary', 'Issued', 'Seeder', NULL, '2026-08-24 09:15:00', '2026-08-24 09:15:00', 'pending'),
(52, 1, 'Municipality of Prieto Diaz', '2026-08-27 09:22:00', '626932', 'Prieto Diaz Waterworks', 30679.57, 'Thirty thousand six hundred seventy nine and 57/100 pesos', 'supplies', 'Issued', 'Seeder', NULL, '2026-08-27 09:22:00', '2026-08-24 14:18:44', 'failed'),
(53, 1, 'Municipality of Prieto Diaz', '2026-08-02 09:29:00', '626933', 'Grand Imperial Hardware', 14710.83, 'Fourteen thousand seven hundred ten and 83/100 pesos', 'services', 'Issued', 'Seeder', NULL, '2026-08-02 09:29:00', '2026-08-02 09:29:00', 'pending'),
(54, 1, 'Municipality of Prieto Diaz', '2026-08-05 09:36:00', '626934', 'Petron - Prieto Diaz', 2847.11, 'Two thousand eight hundred forty seven and 11/100 pesos', 'fuel', 'Issued', 'Seeder', NULL, '2026-08-05 09:36:00', '2026-08-05 09:36:00', 'pending'),
(55, 1, 'Municipality of Prieto Diaz', '2026-08-08 09:43:00', '626935', 'MDF Office Supplies', 2269.88, 'Two thousand two hundred sixty nine and 88/100 pesos', 'remittance', 'Issued', 'Seeder', NULL, '2026-08-08 09:43:00', '2026-08-08 09:43:00', 'pending'),
(56, 1, 'Municipality of Prieto Diaz', '2026-08-11 09:50:00', '626936', 'Bicol Medical Supplies', 36263.56, 'Thirty six thousand two hundred sixty three and 56/100 pesos', 'purchase', 'Issued', 'Seeder', NULL, '2026-08-11 09:50:00', '2026-08-11 09:50:00', 'pending');

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
(6, 2, '202600001', 2026, 'Prieto-Diaz, Sorsogon City, Sorsogon', '2026-07-06 00:00:00', 'SOLEM IT & Digital Solutions Corporation', '123456789000101', '2026-07-06 00:00:00', 'Brgy. Bibincahan, Sorsogon City, Sorsogon', 'Corporation', 'Information Technology', 5, 10, 35, 10, 35, 75, 20, 95, 'Ninety-five pesos only', 'Gemma D. Ferrer', '2026-07-06 00:59:54', '2026-07-06 00:59:54', 'CCC'),
(7, 2, '202600002', 2026, 'Sorsogon City', '2026-07-06 00:00:00', 'CodinGroundz Computer Shop & Services', '123456789001201', '2026-07-06 00:00:00', 'Bariis, Cabid-an', 'Partnership', 'Information Technology', 123, 0, 123, 0, 123, 369, 0, 369, 'Three hundred sixty-nine pesos only', 'Gemma D. Ferrer', '2026-07-06 02:56:01', '2026-07-06 02:56:01', 'CCC'),
(8, 2, '202600003', 2026, 'Prieto-Diaz, Sorsogon City, Sorsogon', '2026-08-24 00:00:00', 'CodinGroundz Computer Shop & Services', '123456789001011', '2026-08-24 00:00:00', NULL, 'Corporation', NULL, 5, 0, 75, 25, 75, 155, 25, 180, 'One hundred eighty pesos only', 'Gemma D. Ferrer', '2026-08-24 00:55:19', '2026-08-24 00:55:19', 'CCC');

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
(15, 1, '202600001', 2026, 'Prieto-Diaz, Sorsogon City, Sorsogon', '2026-07-06 00:00:00', '2026-07-06 00:00:00', 'Bernal', 'Armbel', 'Besalo', '123456789000101', NULL, 'Filipino', NULL, 'Tramo, Pasig', '161', 'on', '75', '1991-05-12 00:00:00', NULL, 75, 1, 35, 1, 35, 2, 35, 180, 4, 184, 'One hundred eighty-four pesos only', 'Gemma D. Ferrer', '2026-07-06 02:54:26', '2026-07-06 02:54:26', 'CCI');

DROP TABLE IF EXISTS `deposits`;
CREATE TABLE `deposits` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `deposit_date` DATE NOT NULL,
  `bank_account_id` BIGINT NOT NULL,
  `slip_number` VARCHAR(255) NULL,
  `prepared_by` VARCHAR(255) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `deposits` (`id`, `deposit_date`, `bank_account_id`, `slip_number`, `prepared_by`, `created_at`, `updated_at`) VALUES
(1, '2026-08-24 00:00:00', 2, NULL, 'Armbel Bernal', '2026-08-24 00:57:43', '2026-08-24 00:57:43');

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
(28, 2, '2026-07-05 23:10:39', '2026-07-05 23:10:39', '202600001', '202600005', 'Armbel Bernal', '2026-07-05 23:10:39', '2026-07-05 23:10:39', NULL),
(29, 1, '2026-07-05 23:30:01', '2026-07-05 23:30:01', '202600001', '202600005', 'Armbel Bernal', '2026-07-05 23:30:01', '2026-07-05 23:30:01', NULL),
(30, 5, '2026-07-06 03:02:56', '2026-07-06 03:02:56', '8104351', '8104360', 'Armbel Bernal', '2026-07-06 03:02:56', '2026-07-06 03:02:56', NULL),
(31, 7, '2026-07-06 04:39:14', '2026-07-06 04:39:14', '4218271', '4218280', 'Armbel Bernal', '2026-07-06 04:39:14', '2026-07-06 04:39:14', NULL),
(32, 8, '2026-07-06 04:50:23', '2026-07-06 04:50:23', '6551381', '6551382', 'Armbel Bernal', '2026-07-06 04:50:23', '2026-08-24 04:59:59', 'Ramon Torres'),
(33, 6, '2026-08-23 17:11:35', '2026-08-23 17:11:35', '2027021', '2027023', 'Armbel Bernal', '2026-08-23 17:11:35', '2026-08-23 17:11:56', 'Armbel Bernal'),
(34, 8, '2026-08-24 04:59:42', '2026-08-24 04:59:42', '6551383', '6551384', 'Armbel Bernal', '2026-08-24 04:59:42', '2026-08-24 05:00:14', 'Armbel Bernal'),
(35, 8, '2026-08-24 14:13:59', '2026-08-24 14:13:59', '6551385', '6551386', 'Armbel Bernal', '2026-08-24 14:13:59', '2026-08-24 14:16:38', 'Jose Ramirez');

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
(1, 4, 'Individual Cedula', 'BIR0016', '2026-07-05 23:30:01', 'Marlaw Sol Emata', '2026-06-13 07:27:13', '2026-07-06 02:54:26', '23:30:01'),
(2, 2, 'Corporation Cedula', 'BIR0017', '2026-07-05 23:10:39', 'Marlaw Sol Emata', '2026-06-13 07:27:13', '2026-08-24 00:55:19', '23:10:39'),
(3, 0, 'Certificate of Ownership of Large Cattle', 'Form 53', '2022-03-01 00:00:00', 'Marlaw Sol Emata', '2026-06-13 07:27:13', '2026-06-14 14:50:02', '11:00:00'),
(4, 0, 'Certificate of Transfer of Large Cattle', 'Form 28A', '2022-07-04 00:00:00', 'Marlaw Sol Emata', '2026-06-13 07:27:13', '2026-06-14 14:50:02', '12:30:00'),
(5, 8, 'Marriage License', 'Form 10', '2026-07-06 03:02:56', 'Marlaw Sol Emata', '2026-06-13 07:27:13', '2026-08-23 18:19:46', '03:02:56'),
(6, 2, 'Official Receipt', 'Form 5IC', '2026-08-23 17:11:35', 'Marlaw Sol Emata', '2026-06-13 07:27:13', '2026-08-23 19:48:23', '17:11:35'),
(7, 9, 'OR RPT', 'Form 56', '2026-07-06 04:39:14', 'Marlaw Sol Emata', '2026-06-13 07:27:13', '2026-08-23 19:46:12', '04:39:14'),
(8, 2, 'Burial', 'Form 58', '2026-08-24 14:13:59', 'Marlaw Sol Emata', '2026-06-13 07:27:13', '2026-08-24 14:13:59', '14:13:59');

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
  `fee_amount` DECIMAL(15,2) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `marriage_certificate_transactions` (`id`, `form_stock_id`, `certificate_number`, `husband_name`, `husband_age_years`, `husband_age_months`, `husband_address`, `wife_name`, `wife_age_years`, `wife_age_months`, `wife_address`, `witness_day`, `witness_month`, `witness_year`, `instructions_day`, `instructions_month`, `instructions_year`, `registry_number`, `local_civil_registrar_of`, `email`, `message`, `created_at`, `updated_at`, `fee_amount`) VALUES
(20, 5, '8104351', 'Armbel Besalo Bernal', 35, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', 'Cleofe Dioneda Villanueva', 30, 1, 'Winterwood Street, Greensborough Subdivision, Sabang, Dasmariñas, Cavite', '16', 'June', '26', '16', 'June', '26', '123456-7123-11-2', 'Dasmariñas, Cavite, 4115', NULL, NULL, '2026-07-06 03:04:59', '2026-07-06 03:04:59', NULL),
(21, 5, '8104352', 'Armbel Besalo Bernal', NULL, NULL, NULL, 'Cleofe Dioneda Villanueva', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-23 18:19:46', '2026-08-23 18:19:46', 2);

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
(22, '2026_06_19_224250_add_assigned_to_to_form_batches_table', 15),
(23, '2026_06_20_100000_create_rpt_properties_table', 16),
(24, '2026_06_20_100100_create_or_rpt_transaction_entries_table', 16),
(25, '2026_07_06_100000_create_burial_permit_transactions_table', 16),
(26, '2026_07_06_120000_create_cheque_management_tables', 17),
(27, '2026_07_07_100000_add_payment_fields_to_transaction_logs_table', 18),
(28, '2026_07_07_100100_backfill_transaction_log_payments', 19),
(29, '2026_07_08_100000_create_deposits_and_recon_columns', 20),
(30, '2026_08_27_100000_add_opening_balance_to_bank_accounts', 21);

DROP TABLE IF EXISTS `or_rpt_transaction_entries`;
CREATE TABLE `or_rpt_transaction_entries` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `or_rpt_transaction_id` BIGINT NOT NULL,
  `rpt_property_id` BIGINT NOT NULL,
  `payment_scheme` VARCHAR(255) NOT NULL,
  `installment_quarter` BIGINT NULL,
  `tax_due` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `discount` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `penalty_percent` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `penalty_amount` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `amount` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `or_rpt_transaction_entries` (`id`, `or_rpt_transaction_id`, `rpt_property_id`, `payment_scheme`, `installment_quarter`, `tax_due`, `discount`, `penalty_percent`, `penalty_amount`, `amount`, `created_at`, `updated_at`) VALUES
(1, 9, 1, 'full', NULL, 300, 0, 0, 0, 300, '2026-08-23 19:46:12', '2026-08-23 19:46:12');

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
(9, 7, '4218271', '211133456-2026', 'May 12', '2026', 'Prieto-Diaz, Sorsogon', 'Sorsogon', '2026-08-23 00:00:00', 'Armbel Bernal', 'Three hundred and 00/100 pesos', 300, 'Gemma D. Ferrer', 0, 0, '2026-08-23 19:46:12', '2026-08-23 19:46:12');

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
(6, 6, '0000001', '2026-08-23 00:00:00', 'SOLEM IT & Digital Solutions', '75000', 'Marlaw Emata', '[{"description":"Business Permit","account_code":"BP01","amount":"75000"},{"description":null,"account_code":null,"amount":0},{"description":null,"account_code":null,"amount":0},{"description":null,"account_code":null,"amount":0},{"description":null,"account_code":null,"amount":0},{"description":null,"account_code":null,"amount":0},{"description":null,"account_code":null,"amount":0},{"description":null,"account_code":null,"amount":0}]', 75000, 'Seventy-five thousand pesos only', 'cash', NULL, NULL, NULL, '2026-08-23 19:48:23', '2026-08-23 19:48:23');

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

DROP TABLE IF EXISTS `rpt_properties`;
CREATE TABLE `rpt_properties` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `tax_declaration_number` VARCHAR(255) NOT NULL,
  `declared_owner` VARCHAR(255) NULL,
  `location` VARCHAR(255) NULL,
  `lot_block_number` VARCHAR(255) NULL,
  `municipality_province` VARCHAR(255) NULL,
  `city` VARCHAR(255) NULL,
  `assessed_value_land` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `assessed_value_improvement` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `assessed_value_total` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `annual_tax_due` DECIMAL(15,2) NOT NULL DEFAULT '0',
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rpt_properties_tax_declaration_number_unique` (`tax_declaration_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `rpt_properties` (`id`, `tax_declaration_number`, `declared_owner`, `location`, `lot_block_number`, `municipality_province`, `city`, `assessed_value_land`, `assessed_value_improvement`, `assessed_value_total`, `annual_tax_due`, `created_at`, `updated_at`) VALUES
(1, '1-234-56789-0', 'Armbel Bernal', 'Winterwood Street, Brgy. Sabang', NULL, 'Prieto-Diaz, Sorsogon', 'Sorsogon', 10000, 5000, 15000, 300, '2026-08-23 19:46:12', '2026-08-23 19:46:12');

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
  `amount` DECIMAL(15,2) NULL,
  `payment_method` VARCHAR(255) NOT NULL DEFAULT 'cash',
  `payment_channel` VARCHAR(255) NULL,
  `payer_bank_name` VARCHAR(255) NULL,
  `payment_reference` VARCHAR(255) NULL,
  `payment_reference_date` DATE NULL,
  `recon_status` VARCHAR(255) NOT NULL DEFAULT 'pending',
  `deposit_id` BIGINT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_logs_transaction_type_transaction_id_index` (`transaction_type`, `transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `transaction_logs` (`id`, `serial_number`, `payee`, `transacted_at`, `form_type`, `status`, `created_at`, `updated_at`, `transaction_type`, `transaction_id`, `archived_at`, `amount`, `payment_method`, `payment_channel`, `payer_bank_name`, `payment_reference`, `payment_reference_date`, `recon_status`, `deposit_id`) VALUES
(121, 'CCC 202600001', 'SOLEM IT & Digital Solutions Corporation', '2026-07-06 00:59:54', 'BIR0017', 'Completed', '2026-07-06 00:59:54', '2026-08-23 17:07:23', 'App\\Models\\CtcCorporationTransaction', 6, NULL, 95, 'cash', NULL, NULL, NULL, NULL, 'pending', NULL),
(122, 'CCI 202600001', 'Bernal, Armbel Besalo', '2026-07-06 02:54:26', 'BIR0016', 'Completed', '2026-07-06 02:54:26', '2026-08-23 17:07:23', 'App\\Models\\CtcIndividualTransaction', 15, NULL, 184, 'cash', NULL, NULL, NULL, NULL, 'pending', NULL),
(123, 'CCC 202600002', 'CodinGroundz Computer Shop & Services', '2026-07-06 02:56:01', 'BIR0017', 'Completed', '2026-07-06 02:56:01', '2026-08-24 00:57:43', 'App\\Models\\CtcCorporationTransaction', 7, NULL, 369, 'cash', NULL, NULL, NULL, NULL, 'completed', 1),
(124, 'No. 8104351', 'Armbel Besalo Bernal & Cleofe Dioneda Villanueva', '2026-07-06 03:04:59', 'Form 10', 'Cancelled', '2026-07-06 03:04:59', '2026-08-24 00:30:43', 'App\\Models\\MarriageCertificateTransaction', 20, NULL, 0, 'cash', NULL, NULL, NULL, NULL, 'pending', NULL),
(125, 'No. 6551381 C', 'Armbel Bernal', '2026-07-06 15:38:18', 'Form 58', 'Cancelled', '2026-07-06 15:38:18', '2026-08-24 00:30:31', 'App\\Models\\BurialPermitTransaction', 1, NULL, 25000, 'cash', NULL, NULL, NULL, NULL, 'pending', NULL),
(126, 'No. 6551381 C', 'Armbel Bernal', '2026-08-23 16:22:43', 'Form 58', 'Completed', '2026-08-23 16:22:43', '2026-08-24 00:57:43', 'App\\Models\\BurialPermitTransaction', 2, NULL, 500, 'cash', NULL, NULL, NULL, NULL, 'completed', 1),
(127, 'No. 6551381 C', 'Armbel Bernal', '2026-08-23 17:24:09', 'Form 58', 'Completed', '2026-08-23 17:24:09', '2026-08-24 00:57:49', 'App\\Models\\BurialPermitTransaction', 3, NULL, 500, 'online', 'Maya', NULL, '123456123', '2026-08-23 00:00:00', 'completed', NULL),
(128, 'No. 8104352', 'Armbel Besalo Bernal & Cleofe Dioneda Villanueva', '2026-08-23 18:19:46', 'Form 10', 'Completed', '2026-08-23 18:19:46', '2026-08-24 00:57:43', 'App\\Models\\MarriageCertificateTransaction', 21, NULL, 2, 'cash', NULL, NULL, NULL, NULL, 'completed', 1),
(129, '4218271', 'Armbel Bernal', '2026-08-23 19:46:12', 'Form 56', 'Completed', '2026-08-23 19:46:12', '2026-08-24 00:57:48', 'App\\Models\\OrRptTransaction', 9, NULL, 300, 'online', 'Maya', NULL, '1234566123', '2026-08-23 00:00:00', 'completed', NULL),
(130, 'No. 0000001 U', 'Marlaw Emata', '2026-08-23 19:48:23', 'Form 5IC', 'Cancelled', '2026-08-23 19:48:23', '2026-08-24 14:19:03', 'App\\Models\\OrTransaction', 6, NULL, 75000, 'cash', NULL, NULL, NULL, NULL, 'completed', 1),
(131, 'No. 6551381 C', 'Armbel Bernal', '2026-08-24 00:43:42', 'Form 58', 'Completed', '2026-08-24 00:43:42', '2026-08-24 00:57:47', 'App\\Models\\BurialPermitTransaction', 4, NULL, 5000, 'online', 'Maya', NULL, '1234561235', '2026-08-24 00:00:00', 'completed', NULL),
(132, 'CCC 202600003', 'CodinGroundz Computer Shop & Services', '2026-08-24 00:55:19', 'BIR0017', 'Completed', '2026-08-24 00:55:19', '2026-08-24 00:57:46', 'App\\Models\\CtcCorporationTransaction', 8, NULL, 180, 'online', 'GCash', NULL, '123412516', '2026-08-24 00:00:00', 'completed', NULL),
(133, 'No. 6551382 C', 'Armbel Bernal', '2026-08-24 04:59:12', 'Form 58', 'Completed', '2026-08-24 04:59:12', '2026-08-24 14:18:25', 'App\\Models\\BurialPermitTransaction', 5, NULL, 5000, 'online', 'Maya', NULL, '12345125616', '2026-08-24 00:00:00', 'completed', NULL),
(134, 'No. 6551383 C', 'Armbel Bernal', '2026-08-24 13:03:06', 'Form 58', 'Completed', '2026-08-24 13:03:06', '2026-08-24 13:03:06', 'App\\Models\\BurialPermitTransaction', 6, NULL, 500, 'cash', NULL, NULL, NULL, NULL, 'pending', NULL),
(135, 'No. 6551384 C', 'Armbel Bernal', '2026-08-24 13:03:25', 'Form 58', 'Completed', '2026-08-24 13:03:26', '2026-08-24 13:03:26', 'App\\Models\\BurialPermitTransaction', 7, NULL, 500, 'cash', NULL, NULL, NULL, NULL, 'pending', NULL);

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
(1, 'Marlaw Sol Emata', 'memata@solemitsolutions.com', '2026-06-13 05:54:19', '$2y$12$6c4yaGdXcN5LAFOgvwFW8uZGZoB.9bsMjDKbcYvN.wfCAsH3BKUL2', 'RURqWdHVZ4c2NF8JBB1Zyi6ZHY4fHuabfdOJhYY988Js6nn4jzIjVRN7Jx7e', '2023-01-14 09:00:00', '2026-08-24 02:09:03', '+63 912 345 6780', 'activated', 'System', 'memata', NULL),
(2, 'Juan Dela Cruz', 'jdelacruz@solemitsolutions.com', NULL, '$2y$12$EI66X0mmtIia6zkdEWPyWeCeCkCQcXSc2H9F9ie4F9ZPckz2tnesK', NULL, '2023-03-02 10:30:00', '2026-06-16 06:59:36', '+63 912 345 6781', 'disabled', 'Marlaw Sol Emata', 'jdelacruz', NULL),
(3, 'Maria Santos', 'msantos@solemitsolutions.com', NULL, '$2y$12$syll0tLjS57QF4QeqPeyc.YZBz4N1PZ1WHt14bdr5/LlzloKdpzTi', NULL, '2023-08-19 11:15:00', '2026-06-16 06:59:36', '+63 912 345 6782', 'disabled', 'Marlaw Sol Emata', 'msantos', NULL),
(4, 'Pedro Reyes', 'preyes@solemitsolutions.com', NULL, '$2y$12$rjihwkOx1cTixyvaF.XrG.wyqFwoKH7/9xDxHuB.rlWAqMxV5ZdRe', NULL, '2024-02-05 14:20:00', '2026-06-16 06:59:36', '+63 912 345 6783', 'disabled', 'Marlaw Sol Emata', 'preyes', NULL),
(5, 'Ana Garcia', 'agarcia@solemitsolutions.com', NULL, '$2y$12$LCRLRvrbsip.JnvkPVMQWuuDpTPA37Sr/gNB/lAVeYQLdZxNRUyre', NULL, '2024-06-17 09:45:00', '2026-06-20 19:01:42', '+63 912 345 6784', 'disabled', 'Marlaw Sol Emata', 'agarcia', NULL),
(6, 'Jose Ramirez', 'jramirez@solemitsolutions.com', NULL, '$2y$12$/lDdmXPM4xeOjf4LrdNPTO3lNIA3hFSkMrcKH4X1OYV9OBSq0xPb.', NULL, '2024-11-30 13:10:00', '2026-06-20 18:59:06', '+63 912 345 6785', 'disabled', 'Marlaw Sol Emata', 'jramirez', NULL),
(7, 'Carmen Lopez', 'clopez@solemitsolutions.com', NULL, '$2y$12$S3MxAIH70FdEfSpPCBwQpeOAkbrDzyWup0C1hvq/iYB7J8f5efhri', NULL, '2025-04-12 08:30:00', '2026-06-20 19:07:07', '+63 912 345 6786', 'activated', 'Marlaw Sol Emata', 'clopez', NULL),
(8, 'Ramon Torres', 'rtorres@solemitsolutions.com', NULL, '$2y$12$iYAmGpU0TU6d9GD6.jC3EOu.tPKpau1flQzUDKXQUoM19fnwNMMVm', 'ZvEv9j1GmPmhuZrAhYSUf0vMk9TEKaRRLLcVIAlwOhvcDU5e6dwUc3sXDopl', '2025-09-25 16:00:00', '2026-08-24 12:41:06', '+63 912 345 6787', 'activated', 'Marlaw Sol Emata', 'rtorres', NULL),
(9, 'Test User One', 'testuser1@example.com', NULL, '$2y$12$Ld4OMRII523iOEfL.OZxpOhYxLgzcjNmh44cN1MzY1ccORnU3p782', NULL, '2026-06-15 23:48:29', '2026-06-16 00:06:39', '09179998888', 'archived', 'Marlaw Sol Emata', 'testuser1', '2026-06-16 00:06:39'),
(10, 'Armbel Bernal', 'bernalarmbelb@outlook.com', NULL, '$2y$12$KTvGK/3WuwY/UJF4kBSt5OUo7IyHrKbD34vDHimel/CNEWxEZZHb6', 'SRZYL1rusCrI86HXwfZZsmG15Euvho26PwZmjAslUyp7Poykhn8UwDOHFg7s', '2026-06-16 00:46:58', '2026-07-05 22:33:12', '639475113910', 'activated', 'Marlaw Sol Emata', 'rootAdmin', NULL),
(12, 'Cleofe Villanueva', 'cvillanueva@gmail.com', NULL, '$2y$12$bmCXUdypciomaPJbmIwr8eHVcXEdX6eC4n8uGx25yQ5YBDm62TV9C', NULL, '2026-06-16 12:32:24', '2026-08-23 17:29:06', '639475113910', 'activated', 'Marlaw Sol Emata', 'admin', NULL);

-- Foreign keys
ALTER TABLE `activity_logs` ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
ALTER TABLE `burial_permit_transactions` ADD CONSTRAINT `burial_permit_transactions_form_stock_id_foreign` FOREIGN KEY (`form_stock_id`) REFERENCES `form_stocks` (`id`) ON DELETE NO ACTION;
ALTER TABLE `cancel_requests` ADD CONSTRAINT `cancel_requests_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
ALTER TABLE `cancel_requests` ADD CONSTRAINT `cancel_requests_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
ALTER TABLE `cancel_requests` ADD CONSTRAINT `cancel_requests_transaction_log_id_foreign` FOREIGN KEY (`transaction_log_id`) REFERENCES `transaction_logs` (`id`) ON DELETE CASCADE;
ALTER TABLE `cheques` ADD CONSTRAINT `cheques_bank_account_id_foreign` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts` (`id`) ON DELETE CASCADE;
ALTER TABLE `ctc_corporation_transactions` ADD CONSTRAINT `ctc_corporation_transactions_form_stock_id_foreign` FOREIGN KEY (`form_stock_id`) REFERENCES `form_stocks` (`id`) ON DELETE CASCADE;
ALTER TABLE `ctc_individual_transactions` ADD CONSTRAINT `ctc_individual_transactions_form_stock_id_foreign` FOREIGN KEY (`form_stock_id`) REFERENCES `form_stocks` (`id`) ON DELETE CASCADE;
ALTER TABLE `deposits` ADD CONSTRAINT `deposits_bank_account_id_foreign` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts` (`id`) ON DELETE CASCADE;
ALTER TABLE `form_batches` ADD CONSTRAINT `form_batches_form_stock_id_foreign` FOREIGN KEY (`form_stock_id`) REFERENCES `form_stocks` (`id`) ON DELETE CASCADE;
ALTER TABLE `marriage_certificate_transactions` ADD CONSTRAINT `marriage_certificate_transactions_form_stock_id_foreign` FOREIGN KEY (`form_stock_id`) REFERENCES `form_stocks` (`id`) ON DELETE NO ACTION;
ALTER TABLE `or_rpt_transaction_entries` ADD CONSTRAINT `or_rpt_transaction_entries_rpt_property_id_foreign` FOREIGN KEY (`rpt_property_id`) REFERENCES `rpt_properties` (`id`) ON DELETE CASCADE;
ALTER TABLE `or_rpt_transaction_entries` ADD CONSTRAINT `or_rpt_transaction_entries_or_rpt_transaction_id_foreign` FOREIGN KEY (`or_rpt_transaction_id`) REFERENCES `or_rpt_transactions` (`id`) ON DELETE CASCADE;
ALTER TABLE `or_rpt_transactions` ADD CONSTRAINT `or_rpt_transactions_form_stock_id_foreign` FOREIGN KEY (`form_stock_id`) REFERENCES `form_stocks` (`id`) ON DELETE CASCADE;
ALTER TABLE `or_transactions` ADD CONSTRAINT `or_transactions_form_stock_id_foreign` FOREIGN KEY (`form_stock_id`) REFERENCES `form_stocks` (`id`) ON DELETE CASCADE;
ALTER TABLE `role_module_permissions` ADD CONSTRAINT `role_module_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
ALTER TABLE `role_user` ADD CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `role_user` ADD CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
ALTER TABLE `transaction_logs` ADD CONSTRAINT `transaction_logs_deposit_id_foreign` FOREIGN KEY (`deposit_id`) REFERENCES `deposits` (`id`) ON DELETE SET NULL;

SET FOREIGN_KEY_CHECKS = 1;
