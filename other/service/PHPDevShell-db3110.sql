/*[1060]*/ALTER TABLE `pds_core_menu_items` ADD COLUMN `params` varchar(1024) DEFAULT NULL AFTER `layout`;
REPLACE INTO `pds_core_plugin_activation` VALUES ('RedBeanORM', 'install', '1000', '0');
REPLACE INTO `pds_core_plugin_activation` VALUES ('CRUD', 'install', '1000', '0');
INSERT INTO `pds_core_plugin_classes` VALUES ('', 'orm', 'PHPDS_orm', 'RedBeanORM', '1', '1');
INSERT INTO `pds_core_plugin_classes` VALUES ('', 'crud', 'PHPDS_crud', 'CRUD', '1', '1');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_setting_support_email', 'default:System Support Query,default:General Query', 'Allows you to have multiple option for a email query.');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_menu_behaviour', 'dynamic', 'How the menu system should behave when navigating');
INSERT INTO `pds_core_logs` VALUES ('', '3', '<strong>You just upgraded, you are not completely done yet, please goto the Plugin Manager and next to PHPDevShell click on Reinstall Menus to gain access to added UI functionality.</strong>', '585886089', '1', 'Root User', '585886089', 'plugin-admin/plugin-activation.php', 'System Upgrade', '127.0.0.1');