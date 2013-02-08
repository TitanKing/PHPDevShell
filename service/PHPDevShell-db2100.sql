ALTER TABLE `pds_core_user_groups` ADD INDEX `index` (`alias`);
ALTER TABLE `pds_core_user_groups` ADD COLUMN `alias` varchar(255) NULL DEFAULT NULL AFTER `parent_group_id`;
ALTER TABLE `pds_core_user_groups` ADD COLUMN `parent_group_id` int(10) NULL DEFAULT NULL AFTER `user_group_note`;