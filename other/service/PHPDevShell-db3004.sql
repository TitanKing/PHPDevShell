ALTER TABLE `pds_core_upload_logs` ADD COLUMN `file_explained` text DEFAULT NULL AFTER `file_size`;
ALTER TABLE `pds_core_settings` ADD COLUMN `note` text DEFAULT NULL AFTER `setting_value`;
REPLACE INTO `pds_core_plugin_classes` VALUES ('', 'userActions', 'PHPDS_userAction', 'userActions', '1', '1');
REPLACE INTO `pds_core_plugin_classes` VALUES ('', 'StandardLogin', 'PHPDS_login', 'StandardLogin', '1', '1');
