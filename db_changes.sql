-- ALTER TABLE `enquiries` ADD `source_mode` ENUM('inhouse ','self') NULL DEFAULT NULL AFTER `enquiry_source_id`;

-- INSERT INTO `enquiry_statuses` (`id`, `status_key`, `label`, `bg`, `filter_color`, `list_color`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, 'ongoing_discussion', 'Ongoing Discussion ', '#212121', '#000', '#fff', '9', '1', '2025-05-05 13:49:08', '2025-05-05 14:12:23');
-- INSERT INTO `enquiry_statuses` (`id`, `status_key`, `label`, `bg`, `filter_color`, `list_color`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, 'preparing_scope', 'Preparing Scope of Work ', '#212121', '#000', '#fff', '9', '1', '2025-05-05 13:49:08', '2025-05-05 14:12:23');

-- UPDATE `enquiry_statuses` SET `sort_order` = '3' WHERE `enquiry_statuses`.`id` = 10;
-- UPDATE `enquiry_statuses` SET `sort_order` = '4' WHERE `enquiry_statuses`.`id` = 11;
-- UPDATE `enquiry_statuses` SET `sort_order` = '5' WHERE `enquiry_statuses`.`id` = 3;
-- UPDATE `enquiry_statuses` SET `sort_order` = '6' WHERE `enquiry_statuses`.`id` = 4;
-- UPDATE `enquiry_statuses` SET `sort_order` = '7' WHERE `enquiry_statuses`.`id` = 5;
-- UPDATE `enquiry_statuses` SET `sort_order` = '8' WHERE `enquiry_statuses`.`id` = 6;
-- UPDATE `enquiry_statuses` SET `sort_order` = '9' WHERE `enquiry_statuses`.`id` = 7;
-- UPDATE `enquiry_statuses` SET `sort_order` = '10' WHERE `enquiry_statuses`.`id` = 8;
-- UPDATE `enquiry_statuses` SET `sort_order` = '11' WHERE `enquiry_statuses`.`id` = 9;

-- ALTER TABLE `enquiries` CHANGE `status` `status` ENUM('new_enquiry','started_discussion','proposal_submitted','project_approved','project_rejected','not_interested','not_responding','invalid_spam','signed_payment_pending','ongoing_discussion','preparing_scope') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'new_enquiry' COMMENT 'New Enquiry, Started Discussion, Proposal Submitted, Project Approved, Project Rejected, Not Interested, Not Responding, Invalid/Spam';

-- ALTER TABLE `enquiries` CHANGE `status` `status` ENUM('new_enquiry','started_discussion','proposal_submitted','project_approved','project_rejected','not_interested','not_responding','invalid_spam','signed_payment_pending','ongoing_discussion','preparing_scope') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'new_enquiry' COMMENT 'New Enquiry, Started Discussion,\r\nOngoing Discussion,\r\nPreparing Scope of Work, Proposal Submitted, Project Approved, Project Rejected, Not Interested, Not Responding, Invalid/Spam';

-- UPDATE `enquiry_statuses` SET `bg` = '#f2d1ff' WHERE `enquiry_statuses`.`id` = 10;
-- UPDATE `enquiry_statuses` SET `list_color` = '#000' WHERE `enquiry_statuses`.`id` = 10;
-- UPDATE `enquiry_statuses` SET `bg` = '#fff8b7' WHERE `enquiry_statuses`.`id` = 11;
-- UPDATE `enquiry_statuses` SET `list_color` = '#000' WHERE `enquiry_statuses`.`id` = 11;
-- UPDATE `enquiry_statuses` SET `bg` = '#94d1ff' WHERE `enquiry_statuses`.`id` = 2;
-- UPDATE `enquiry_statuses` SET `bg` = '#b1e8ff' WHERE `enquiry_statuses`.`id` = 1;
-- UPDATE `enquiry_statuses` SET `bg` = '#fff069' WHERE `enquiry_statuses`.`id` = 3;
-- UPDATE `enquiry_statuses` SET `bg` = '#b9ffc2' WHERE `enquiry_statuses`.`id` = 4;
-- UPDATE `enquiry_statuses` SET `bg` = '#10e327ad' WHERE `enquiry_statuses`.`id` = 5;
-- UPDATE `enquiry_statuses` SET `list_color` = '#000' WHERE `enquiry_statuses`.`id` = 5;
-- UPDATE `enquiry_statuses` SET `bg` = '#f15146ba' WHERE `enquiry_statuses`.`id` = 6;
-- UPDATE `enquiry_statuses` SET `list_color` = '#000' WHERE `enquiry_statuses`.`id` = 6;
-- UPDATE `enquiry_statuses` SET `bg` = '#c6c6c6' WHERE `enquiry_statuses`.`id` = 7;
-- UPDATE `enquiry_statuses` SET `bg` = '#ffc56f' WHERE `enquiry_statuses`.`id` = 8;
-- UPDATE `enquiry_statuses` SET `bg` = '#767676' WHERE `enquiry_statuses`.`id` = 9;

-- ALTER TABLE `enquiry_status_histories` CHANGE `status` `status` ENUM('new_enquiry','started_discussion','proposal_submitted','project_approved','project_rejected','not_interested','not_responding','invalid_spam','signed_payment_pending','ongoing_discussion','preparing_scope') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'New Enquiry, Started Discussion,\\r\\nOngoing Discussion,\\r\\nPreparing Scope of Work, Proposal Submitted, Project Approved, Project Rejected, Not Interested, Not Responding, Invalid/Spam';

-- CREATE TABLE IF NOT EXISTS `enquiry_scopes_of_work` (
--   `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
--   `enquiry_id` bigint UNSIGNED DEFAULT NULL,
--   `scope_content` longtext,
--   `status` enum('open','responded','closed') NOT NULL DEFAULT 'open',
--   `created_by` int UNSIGNED DEFAULT NULL,
--   `updated_by` int UNSIGNED DEFAULT NULL,
--   `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`),
--   KEY `enquiry_id` (`enquiry_id`),
--   KEY `created_by` (`created_by`),
--   KEY `updated_by` (`updated_by`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ALTER TABLE `enquiry_scopes_of_work` ADD FOREIGN KEY (`enquiry_id`) REFERENCES `enquiries`(`id`) ON DELETE CASCADE ON UPDATE NO ACTION; ALTER TABLE `enquiry_scopes_of_work` ADD FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE NO ACTION ON UPDATE NO ACTION; ALTER TABLE `enquiry_scopes_of_work` ADD FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

-- CREATE TABLE IF NOT EXISTS `enquiry_scope_histories` (
--   `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
--   `scope_of_work_id` bigint UNSIGNED DEFAULT NULL,
--   `scope_content` longtext,
--   `edited_by` int UNSIGNED DEFAULT NULL,
--   `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`),
--   KEY `scope_of_work_id` (`scope_of_work_id`),
--   KEY `edited_by` (`edited_by`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ALTER TABLE `enquiry_scope_histories` ADD FOREIGN KEY (`scope_of_work_id`) REFERENCES `enquiry_scopes_of_work`(`id`) ON DELETE CASCADE ON UPDATE NO ACTION; ALTER TABLE `enquiry_scope_histories` ADD FOREIGN KEY (`edited_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

-- CREATE TABLE IF NOT EXISTS `enquiry_scope_comments` (
--   `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
--   `scope_of_work_id` bigint UNSIGNED DEFAULT NULL,
--   `comment` text,
--   `commented_by` int UNSIGNED DEFAULT NULL,
--   `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`),
--   KEY `scope_of_work_id` (`scope_of_work_id`),
--   KEY `commented_by` (`commented_by`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ALTER TABLE `enquiry_scope_comments` ADD FOREIGN KEY (`scope_of_work_id`) REFERENCES `enquiry_scopes_of_work`(`id`) ON DELETE CASCADE ON UPDATE NO ACTION; ALTER TABLE `enquiry_scope_comments` ADD FOREIGN KEY (`commented_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

-- ALTER TABLE `enquiry_scopes_of_work` ADD `title` VARCHAR(255) NULL DEFAULT NULL AFTER `enquiry_id`;

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'manage_enquiry_scope_work', 'Manage Enquiry Scope Of Work', 'web', '1', NULL, NULL);

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '72', 'view_enquiry_scope_work', 'View Enquiry Scope Of Work', 'web', '1', NULL, NULL);

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '72', 'edit_enquiry_scope_work', 'Edit Enquiry Scope Of Work', 'web', '1', NULL, NULL);

-- ALTER TABLE `enquiry_scopes_of_work` ADD `sales_comment` TEXT NULL DEFAULT NULL AFTER `scope_content`;
-- ALTER TABLE `enquiry_scopes_of_work` ADD `scope_comment` TEXT NULL DEFAULT NULL AFTER `sales_comment`;

-- ALTER TABLE `enquiry_scope_comments` ADD `is_sales_team` TINYINT(1) NOT NULL DEFAULT '0' AFTER `comment`;

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '55', 'change_customer_status', 'Change Customer Active Status', 'web', '1', NULL, NULL);

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '60', 'view_project_amounts', 'View Project Amounts', 'web', '1', NULL, NULL);

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'manage_dashboard', 'Manage Dashboard', 'web', '1', NULL, NULL);

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '77', 'view_total_counts', 'View Total Counts', 'web', '1', NULL, NULL);

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '77', 'view_enquiries_by_current_status', 'View Enquiries by Current Status', 'web', '1', NULL, NULL);

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '77', 'view_enquiries_by_source', 'View Enquiries by Source', 'web', '1', NULL, NULL);

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '77', 'view_enquiries_total', 'View Enquiries-Total, Pending & Contacted', 'web', '1', NULL, NULL);

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '77', 'view_enquiries_by_project_type', 'View Enquiries by Project Type', 'web', '1', NULL, NULL);

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '77', 'view_enquiries_by_milestone', 'View Enquiries By Milestone', 'web', '1', NULL, NULL);

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '77', 'view_all_users_filter', 'View All User Filter', 'web', '1', NULL, NULL);

-- ALTER TABLE `users` ADD `followup_mail_status` TINYINT(1) NOT NULL DEFAULT '0' AFTER `banned`, ADD `followup_cc` JSON NULL DEFAULT NULL AFTER `followup_mail_status`;

-- ALTER TABLE `enquiries` ADD `project_title` VARCHAR(255) NULL DEFAULT NULL AFTER `source_mode`;

-- INSERT INTO `enquiry_statuses` (`id`, `status_key`, `label`, `bg`, `filter_color`, `list_color`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, 'pipeline', 'Pipeline', '#fff8b7', '#000', '#000', '6', '1', '2025-05-05 13:49:08', '2026-01-06 15:14:00');

-- UPDATE `enquiry_statuses` SET `sort_order` = '7' WHERE `enquiry_statuses`.`id` = 4;
-- UPDATE `enquiry_statuses` SET `sort_order` = '8' WHERE `enquiry_statuses`.`id` = 5;
-- UPDATE `enquiry_statuses` SET `sort_order` = '9' WHERE `enquiry_statuses`.`id` = 6;
-- UPDATE `enquiry_statuses` SET `sort_order` = '10' WHERE `enquiry_statuses`.`id` = 7;
-- UPDATE `enquiry_statuses` SET `sort_order` = '11' WHERE `enquiry_statuses`.`id` = 8;
-- UPDATE `enquiry_statuses` SET `sort_order` = '12' WHERE `enquiry_statuses`.`id` = 9;
-- UPDATE `enquiry_statuses` SET `bg` = '#be85ff' WHERE `enquiry_statuses`.`id` = 12;

-- ALTER TABLE `enquiries` CHANGE `status` `status` ENUM('new_enquiry','started_discussion','proposal_submitted','project_approved','project_rejected','not_interested','not_responding','invalid_spam','signed_payment_pending','ongoing_discussion','preparing_scope','pipeline') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'new_enquiry' COMMENT 'New Enquiry, Started Discussion,\r\nOngoing Discussion,\r\nPreparing Scope of Work, Proposal Submitted, \r\nPipeline,\r\nProject Approved, Project Rejected, Not Interested, Not Responding, Invalid/Spam';

-- ALTER TABLE `enquiry_status_histories` CHANGE `status` `status` ENUM('new_enquiry','started_discussion','proposal_submitted','project_approved','project_rejected','not_interested','not_responding','invalid_spam','signed_payment_pending','ongoing_discussion','preparing_scope','pipeline') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'New Enquiry, Started Discussion,\\r\\nOngoing Discussion,\\r\\nPreparing Scope of Work, Proposal Submitted, Pipeline, Project Approved, Project Rejected, Not Interested, Not Responding, Invalid/Spam';



-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, NULL, 'manage_data', 'Manage Data', 'web', '1', NULL, NULL);

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '85', 'view_data', 'View Data', 'web', '1', NULL, NULL);

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '85', 'add_data', 'Add Data', 'web', '1', NULL, NULL);

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '85', 'edit_data', 'Edit Data', 'web', '1', NULL, NULL);


-- CREATE TABLE IF NOT EXISTS `datas` (
--   `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
--   `data_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
--   `sales_person` int UNSIGNED DEFAULT NULL,
--   `company_name` varchar(255) DEFAULT NULL,
--   `company_email` varchar(100) DEFAULT NULL,
--   `industry_id` int DEFAULT NULL,
--   `company_address` text,
--   `company_country` int DEFAULT NULL,
--   `emirate` int DEFAULT NULL,
--   `website_link` varchar(255) DEFAULT NULL,
--   `google_location` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
--   `is_active` tinyint(1) NOT NULL DEFAULT '1',
--   `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`),
--   KEY `industry_id` (`industry_id`),
--   KEY `company_country` (`company_country`),
--   KEY `emirate` (`emirate`),
--   KEY `sales_person` (`sales_person`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- ALTER TABLE `datas`
--   ADD CONSTRAINT `datas_ibfk_1` FOREIGN KEY (`industry_id`) REFERENCES `industries` (`id`) ON DELETE CASCADE,
--   ADD CONSTRAINT `datas_ibfk_2` FOREIGN KEY (`company_country`) REFERENCES `countries` (`id`) ON DELETE CASCADE,
--   ADD CONSTRAINT `datas_ibfk_3` FOREIGN KEY (`emirate`) REFERENCES `emirates` (`id`) ON DELETE CASCADE,
--   ADD CONSTRAINT `datas_ibfk_4` FOREIGN KEY (`sales_person`) REFERENCES `users` (`id`) ON DELETE CASCADE;

  -- ALTER TABLE `datas` DROP FOREIGN KEY `datas_ibfk_1`; ALTER TABLE `datas` ADD CONSTRAINT `datas_ibfk_1` FOREIGN KEY (`industry_id`) REFERENCES `industries`(`id`) ON DELETE SET NULL ON UPDATE NO ACTION; ALTER TABLE `datas` DROP FOREIGN KEY `datas_ibfk_2`; ALTER TABLE `datas` ADD CONSTRAINT `datas_ibfk_2` FOREIGN KEY (`company_country`) REFERENCES `countries`(`id`) ON DELETE SET NULL ON UPDATE NO ACTION; ALTER TABLE `datas` DROP FOREIGN KEY `datas_ibfk_3`; ALTER TABLE `datas` ADD CONSTRAINT `datas_ibfk_3` FOREIGN KEY (`emirate`) REFERENCES `emirates`(`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

  -- ALTER TABLE `customers` DROP FOREIGN KEY `customers_ibfk_1`; ALTER TABLE `customers` ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`industry_id`) REFERENCES `industries`(`id`) ON DELETE SET NULL ON UPDATE NO ACTION; ALTER TABLE `customers` DROP FOREIGN KEY `customers_ibfk_2`; ALTER TABLE `customers` ADD CONSTRAINT `customers_ibfk_2` FOREIGN KEY (`company_country`) REFERENCES `countries`(`id`) ON DELETE SET NULL ON UPDATE NO ACTION; ALTER TABLE `customers` DROP FOREIGN KEY `customers_ibfk_3`; ALTER TABLE `customers` ADD CONSTRAINT `customers_ibfk_3` FOREIGN KEY (`emirate`) REFERENCES `emirates`(`id`) ON DELETE SET NULL ON UPDATE NO ACTION; ALTER TABLE `customers` DROP FOREIGN KEY `customers_ibfk_4`; ALTER TABLE `customers` ADD CONSTRAINT `customers_ibfk_4` FOREIGN KEY (`sales_person`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE NO ACTION;


--   CREATE TABLE IF NOT EXISTS `data_contacts` (
--   `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
--   `data_id` bigint UNSIGNED NOT NULL,
--   `name` varchar(255) DEFAULT NULL,
--   `email` varchar(255) DEFAULT NULL,
--   `landline_number` varchar(50) DEFAULT NULL,
--   `mobile_number` varchar(50) DEFAULT NULL,
--   `whatsapp_number` varchar(50) DEFAULT NULL,
--   `designation` varchar(100) DEFAULT NULL,
--   `is_primary` tinyint(1) NOT NULL DEFAULT '0',
--   `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`),
--   KEY `data_id` (`data_id`)
-- ) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ALTER TABLE `data_contacts` ADD CONSTRAINT `data_contacts_ibfk_1` FOREIGN KEY (`data_id`) REFERENCES `datas` (`id`) ON DELETE CASCADE;

-- ALTER TABLE `datas` ADD `entry_date` DATE NULL DEFAULT NULL AFTER `data_code`, ADD `status` ENUM('to_be_contacted','contacted','follow_up','not_interested','not_responding','invalid_spam','convert_to_enquiry') NOT NULL DEFAULT 'to_be_contacted' COMMENT 'To Be Contacted,\r\nContacted,\r\nFollow Up,\r\nNot Interested,\r\nNot Responding,\r\nInvalid/Spam,\r\nConvert To Enquiry' AFTER `entry_date`, ADD `requirement` LONGTEXT NULL DEFAULT NULL AFTER `status`;

-- ALTER TABLE `datas` ADD `last_updated` DATE NULL DEFAULT NULL AFTER `is_active`, ADD `next_followup` DATE NULL DEFAULT NULL AFTER `last_updated`;

-- ALTER TABLE `datas` ADD `source_id` INT NULL DEFAULT NULL AFTER `status`;

-- ALTER TABLE `datas` ADD FOREIGN KEY (`source_id`) REFERENCES `enquiry_sources`(`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

-- ALTER TABLE `datas` ADD `last_comment` TEXT NULL DEFAULT NULL AFTER `next_followup`;

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '85', 'import_data', 'Import Data', 'web', '1', NULL, NULL);

-- CREATE TABLE IF NOT EXISTS `data_statuses` (
--   `id` int NOT NULL AUTO_INCREMENT,
--   `status_key` varchar(50) DEFAULT NULL,
--   `label` varchar(50) DEFAULT NULL,
--   `bg` varchar(10) DEFAULT NULL,
--   `filter_color` varchar(10) DEFAULT NULL,
--   `sort_order` int NOT NULL DEFAULT '0',
--   `is_active` tinyint(1) NOT NULL DEFAULT '1',
--   `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --
-- -- Dumping data for table `data_statuses`
-- --

-- INSERT INTO `data_statuses` (`id`, `status_key`, `label`, `bg`, `filter_color`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
-- (1, 'to_be_contacted', 'To Be Contacted', '#d4d4f8', NULL, 1, 1, '2026-01-17 06:39:40', '2026-01-19 11:45:22'),
-- (2, 'contacted', 'Contacted', '#5e8aff', NULL, 2, 1, '2026-01-17 06:42:42', '2026-01-19 11:51:15'),
-- (3, 'follow_up', 'Follow Up', '#0eced4', NULL, 3, 1, '2026-01-17 06:44:44', '2026-01-19 11:50:35'),
-- (4, 'not_interested', 'Not Interested', '#da0000', NULL, 4, 1, '2026-01-17 06:46:34', '2026-01-19 11:48:04'),
-- (5, 'not_responding', 'Not Responding', '#f18b8b', NULL, 5, 1, '2026-01-17 06:50:12', '2026-01-19 11:48:33'),
-- (6, 'invalid_spam', 'Invalid/Spam', '#848181', NULL, 6, 1, '2026-01-17 07:07:24', '2026-01-19 11:49:01'),
-- (7, 'convert_to_enquiry', 'Convert To Enquiry', '#008600', NULL, 7, 1, '2026-01-17 07:07:24', '2026-01-19 11:49:37');

-- CREATE TABLE IF NOT EXISTS `data_status_histories` (
--   `id` bigint NOT NULL AUTO_INCREMENT,
--   `data_id` bigint UNSIGNED DEFAULT NULL,
--   `status` enum('to_be_contacted','contacted','follow_up','not_interested','not_responding','invalid_spam','convert_to_enquiry') NOT NULL DEFAULT 'to_be_contacted' COMMENT 'To Be Contacted,\r\nContacted,\r\nFollow Up,\r\nNot Interested,\r\nNot Responding,\r\nInvalid/Spam,\r\nConvert To Enquiry',
--   `status_date` date DEFAULT NULL,
--   `comment` text,
--   `followup_date` date DEFAULT NULL,
--   `changed_by` int UNSIGNED DEFAULT NULL,
--   `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`),
--   KEY `date_id` (`data_id`),
--   KEY `changed_by` (`changed_by`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- ALTER TABLE `data_status_histories`
--   ADD CONSTRAINT `data_status_histories_ibfk_1` FOREIGN KEY (`data_id`) REFERENCES `datas` (`id`) ON DELETE CASCADE,
--   ADD CONSTRAINT `data_status_histories_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;


-- UPDATE `data_statuses` SET `label` = 'Ongoing Discussion' WHERE `data_statuses`.`id` = 3;

-- UPDATE `data_statuses` SET `status_key` = 'ongoing_discussion' WHERE `data_statuses`.`id` = 3;

-- ALTER TABLE `datas` CHANGE `status` `status` ENUM('to_be_contacted','contacted','ongoing_discussion','not_interested','not_responding','invalid_spam','convert_to_enquiry') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'to_be_contacted' COMMENT 'To Be Contacted,\r\nContacted,\r\nOngoing Discussion,\r\nNot Interested,\r\nNot Responding,\r\nInvalid/Spam,\r\nConvert To Enquiry';

-- ALTER TABLE `data_status_histories` CHANGE `status` `status` ENUM('to_be_contacted','contacted','ongoing_discussion','not_interested','not_responding','invalid_spam','convert_to_enquiry') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'to_be_contacted' COMMENT 'To Be Contacted,\r\nContacted,\r\nOngoing Discussion,\r\nNot Interested,\r\nNot Responding,\r\nInvalid/Spam,\r\nConvert To Enquiry';


-- 
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '55', 'export_customer', 'Export Customers Data to Excel', 'web', '1', NULL, NULL);

-- ALTER TABLE `enquiry_followups` CHANGE `enquiry_status` `enquiry_status` ENUM('new_enquiry','started_discussion','proposal_submitted','project_approved','project_rejected','not_interested','not_responding','invalid_spam','signed_payment_pending','ongoing_discussion','preparing_scope','pipeline') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'New Enquiry, Started Discussion, Proposal Submitted, Project Approved, Project Rejected, Not Interested, Not Responding, Invalid/Spam';