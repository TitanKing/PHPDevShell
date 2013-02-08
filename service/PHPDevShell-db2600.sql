UPDATE `pds_core_users` SET `timezone` = 'UT';
ALTER TABLE `pds_core_users` MODIFY COLUMN `timezone` varchar(255) NULL DEFAULT NULL AFTER `language`;
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_system_timezone', 'UTC');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_date_format', 'F j, Y, g:i a O');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_date_format_short', 'Y-m-d');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_date_format_show', 'August 7, 2009, 11:43 am +0000');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_date_format_show_short', '2009-08-07');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_loginandout', '3682403894');