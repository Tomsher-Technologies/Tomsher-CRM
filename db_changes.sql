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

ALTER TABLE `users` ADD `followup_mail_status` TINYINT(1) NOT NULL DEFAULT '0' AFTER `banned`, ADD `followup_cc` JSON NULL DEFAULT NULL AFTER `followup_mail_status`;