REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_sef_url', '0');
ALTER TABLE `pds_core_menu_items` ADD COLUMN `alias` varchar(255) NULL DEFAULT NULL AFTER `template_id`;