/*[1061]*/CREATE UNIQUE INDEX `index` USING BTREE ON `pds_core_menu_structure`(`menu_id`) ;
/*[1091]*/ALTER TABLE `pds_core_user_extra_groups` DROP COLUMN `id`;
/*[1068]*/ALTER TABLE `pds_core_user_extra_groups` ADD PRIMARY KEY (`user_id`, `user_group_id`);
/*[1091]*/ALTER TABLE `pds_core_user_extra_roles` DROP COLUMN `id`;
/*[1068]*/ALTER TABLE `pds_core_user_extra_roles` ADD PRIMARY KEY (`user_id`, `user_role_id`);
/*[1091]*/ALTER TABLE `pds_core_user_role_permissions` DROP COLUMN `id`;
ALTER TABLE `pds_core_user_role_permissions` DROP PRIMARY KEY;
/*[1062,1065,1068]*/ALTER TABLE `pds_core_user_role_permissions` ADD PRIMARY KEY (`user_role_id`,`menu_id`);
INSERT INTO `pds_core_logs` VALUES ('', '3', '<strong>You just upgraded, you are not completely done yet, please goto the Plugin Manager and next to PHPDevShell click on Reinstall Menus to gain access to added UI functionality.</strong>', '585886089', '1', 'Root User', '585886089', 'plugin-admin/plugin-activation.php', 'System Upgrade', '127.0.0.1');