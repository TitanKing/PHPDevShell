ALTER TABLE `pds_core_cron` MODIFY COLUMN `menu_id`  varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL FIRST ;
ALTER TABLE `pds_core_filter` MODIFY COLUMN `menu_id`  varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `user_id`;
ALTER TABLE `pds_core_hooks` MODIFY COLUMN `menu_id`  varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `id`;
ALTER TABLE `pds_core_logs` MODIFY COLUMN `menu_id`  varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `user_display_name`;
ALTER TABLE `pds_core_menu_access_logs` MODIFY COLUMN `menu_id`  varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `log_id`;
ALTER TABLE `pds_core_menu_access_logs` ROW_FORMAT=Dynamic;
ALTER TABLE `pds_core_menu_items` MODIFY COLUMN `menu_id`  varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL FIRST ;
CREATE INDEX `index`  ON `pds_core_menu_items`(`parent_menu_id`, `menu_link`, `plugin`, `alias`);
ALTER TABLE `pds_core_menu_structure` MODIFY COLUMN `menu_id`  varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `id`;

CREATE TABLE IF NOT EXISTS `pds_core_plugin_classes` (
`class_id`  int(10) NOT NULL AUTO_INCREMENT ,
`class_name`  varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`alias`  varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`plugin_folder`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`enable`  int(1) NULL DEFAULT NULL ,
`rank`  int(4) NULL DEFAULT NULL ,
PRIMARY KEY (`class_id`),
INDEX `index`  (`class_name`, `alias`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact;

CREATE TABLE IF NOT EXISTS `pds_core_session` (
`cookie_id`  int(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
`user_id`  int(20) UNSIGNED NOT NULL ,
`id_crypt`  char(6) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`pass_crypt`  char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`timestamp`  int(10) NOT NULL ,
PRIMARY KEY (`cookie_id`),
INDEX `index`  (`user_id`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
CHECKSUM=0
ROW_FORMAT=Fixed
DELAY_KEY_WRITE=0;

CREATE TABLE IF NOT EXISTS `pds_core_tags` (
`tagID`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`tagObject`  varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`tagName`  varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`tagTarget`  varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`tagValue`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`tagID`),
UNIQUE INDEX `UNIQUE`  (`tagObject`, `tagName`, `tagTarget`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Compact;

ALTER TABLE `pds_core_upload_logs` MODIFY COLUMN `menu_id`  varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `sub_id`;

CREATE UNIQUE INDEX `index_user`  ON `pds_core_users`(`user_name`, `user_email`) ;

CREATE INDEX `index_general`  ON `pds_core_users`(`user_display_name`, `user_group`, `user_role`) ;

ALTER TABLE `pds_core_user_role_permissions` MODIFY COLUMN `menu_id`  varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `user_role_id`;

CREATE INDEX `index`  ON `pds_core_user_role_permissions`(`user_role_id`, `menu_id`) ;

# Remember.;
INSERT INTO pds_core_settings (setting_description, setting_value) VALUES ('PHPDevShell_allow_remember','1');

# SEF URL improvements.;
REPLACE INTO pds_core_settings (setting_description, setting_value) VALUES ('PHPDevShell_url_append','.html');

# Skin selector.;
REPLACE INTO pds_core_settings (setting_description, setting_value) VALUES ('PHPDevShell_skin','flick');

# Meta values.;
REPLACE INTO pds_core_settings (setting_description, setting_value) VALUES ('PHPDevShell_meta_keywords','administrative, administrator, phpdevshell, interface, ui, user');
REPLACE INTO pds_core_settings (setting_description, setting_value) VALUES ('PHPDevShell_meta_description','Administrative user interface based on PHPDevShell and other modern technologies.');

# 17 September 2010 - Directories changed.;
REPLACE INTO `pds_core_menu_items` VALUES ('48580716', '2190226087', 'New Token', 'registration-token-admin/registration-token-admin.php', 'PHPDevShell', '1', '', '0', '2', '0', '844895956', 'new-token', '');
REPLACE INTO `pds_core_menu_items` VALUES ('131201277', '294626826', 'Manage Users', 'user-admin/user-admin-list.php', 'PHPDevShell', '1', '', '0', '1', '0', '844895956', 'manage-users', '');
REPLACE INTO `pds_core_menu_items` VALUES ('294626826', '1411278578', 'User Admin', 'user-admin/user-admin-list.php.link', 'PHPDevShell', '2', '131201277', '0', '2', '2', '844895956', 'user-admin', '');
REPLACE INTO `pds_core_menu_items` VALUES ('585886089', '982913650', 'Plugins Admin', 'plugin-admin/plugin-activation.php', 'PHPDevShell', '1', '', '0', '3', '0', '844895956', 'plugins-admin', '');
REPLACE INTO `pds_core_menu_items` VALUES ('742061208', '930839394', 'System Cronjob', 'cron-admin/run-cron.php', 'PHPDevShell', '1', '', '0', '3', '1', '844895956', 'system-cronjob', '');
REPLACE INTO `pds_core_menu_items` VALUES ('863779375', '3669783681', 'System Info', 'system-admin/admin.php', 'PHPDevShell', '1', '', '0', '1', '0', '844895956', 'system-info', '');
REPLACE INTO `pds_core_menu_items` VALUES ('885145814', '294626826', 'Edit User', 'user-admin/user-admin.link', 'PHPDevShell', '2', '1440418834', '0', '3', '4', '844895956', 'edit-user', '');
REPLACE INTO `pds_core_menu_items` VALUES ('930839394', '2509699192', 'Cronjob Admin', 'cron-admin/cronjob-admin-list.php.link', 'PHPDevShell', '2', '4134883375', '0', '3', '2', '844895956', 'cronjob-admin', '');
REPLACE INTO `pds_core_menu_items` VALUES ('940041356', '0', 'Dashboard', 'user/control-panel.php', 'PHPDevShell', '1', '', '0', '8', '1', '844895956', 'cp', '');
REPLACE INTO `pds_core_menu_items` VALUES ('967550350', '3968968736', 'New Menu', 'menu-admin/menu-item-admin.php', 'PHPDevShell', '1', '', '0', '2', '0', '844895956', 'new-menu', '');
REPLACE INTO `pds_core_menu_items` VALUES ('971937357', '930839394', 'Optimize Database', 'cron/optimize-database.php', 'PHPDevShell', '8', '', '0', '6', '1', '844895956', 'optimize-database', '');
REPLACE INTO `pds_core_menu_items` VALUES ('982913650', '2509699192', 'System Admin', 'system-admin/general-settings.php.link', 'PHPDevShell', '2', '1363712008', '0', '2', '2', '844895956', 'system-admin', '');
REPLACE INTO `pds_core_menu_items` VALUES ('1016054546', '930839394', 'Edit Cronjob', 'cron-admin/cronjob-admin.php', 'PHPDevShell', '1', '', '0', '2', '4', '844895956', 'edit-cronjob', '');
REPLACE INTO `pds_core_menu_items` VALUES ('1210756465', '294626826', 'Pending Users', 'user-admin/user-admin-pending.php', 'PHPDevShell', '1', '', '0', '4', '0', '844895956', 'pending-users', '');
REPLACE INTO `pds_core_menu_items` VALUES ('1363712008', '982913650', 'System Settings', 'system-admin/general-settings.php', 'PHPDevShell', '1', '', '0', '1', '0', '844895956', 'system-settings', '');
REPLACE INTO `pds_core_menu_items` VALUES ('1405303115', '2751748213', 'Edit Role', 'user-admin/user-role-admin.link', 'PHPDevShell', '2', '2313706889', '0', '3', '4', '844895956', 'edit-role', '');
REPLACE INTO `pds_core_menu_items` VALUES ('1411278578', '2509699192', 'Policy Admin', 'user/control-panel.user-control', 'PHPDevShell', '2', '940041356', '0', '6', '0', '844895956', 'policy-admin', '');
REPLACE INTO `pds_core_menu_items` VALUES ('1440418834', '294626826', 'New User', 'user-admin/user-admin.php', 'PHPDevShell', '1', '', '0', '2', '0', '844895956', 'new-user', '');
REPLACE INTO `pds_core_menu_items` VALUES ('1648130103', '3669783681', 'System Logs', 'logs-admin/system-logs.php', 'PHPDevShell', '1', '', '0', '2', '0', '844895956', 'system-logs', '');
REPLACE INTO `pds_core_menu_items` VALUES ('1669337107', '982913650', 'Template Admin', 'template-admin/template-admin-list.php', 'PHPDevShell', '1', '', '0', '4', '0', '844895956', 'template-admin', '');
REPLACE INTO `pds_core_menu_items` VALUES ('1772410402', '1814972020', 'New Group', 'user-admin/user-group-admin.php', 'PHPDevShell', '1', '', '0', '2', '0', '844895956', 'new-group', '');
REPLACE INTO `pds_core_menu_items` VALUES ('1814972020', '1411278578', 'Group Admin', 'user-admin/user-group-admin-list.php.link', 'PHPDevShell', '2', '3276230420', '0', '4', '2', '844895956', 'group-admin', '');
REPLACE INTO `pds_core_menu_items` VALUES ('1886139891', '2190226087', 'Email Token', 'registration-token-admin/email-token.php', 'PHPDevShell', '1', '', '0', '4', '4', '844895956', 'email-token', '');
REPLACE INTO `pds_core_menu_items` VALUES ('1901799184', '0', 'Lost Password', 'user/lost-password.php', 'PHPDevShell', '1', '', '0', '3', '0', '844895956', 'lost-password', '');
REPLACE INTO `pds_core_menu_items` VALUES ('2021208659', '3669783681', 'Upload Logs', 'logs-admin/fileupload-logs.php', 'PHPDevShell', '1', '', '0', '4', '0', '844895956', 'upload-logs', '');
REPLACE INTO `pds_core_menu_items` VALUES ('2074704070', '982913650', 'Config Manager', 'system-admin/config-manager.php', 'PHPDevShell', '1', '', '0', '2', '0', '844895956', 'config-manager', '');
REPLACE INTO `pds_core_menu_items` VALUES ('2143500606', '0', 'Finish Registration', 'user/register-finalize.php', 'PHPDevShell', '1', '', '0', '9', '1', '844895956', 'finish-registration', '');
REPLACE INTO `pds_core_menu_items` VALUES ('2190226087', '1411278578', 'Token Admin', 'registration-token-admin/registration-token-admin-list.php.link', 'PHPDevShell', '2', '2387241520', '0', '5', '2', '844895956', 'token-admin', '');
REPLACE INTO `pds_core_menu_items` VALUES ('2200445609', '2190226087', 'Edit Token', 'registration-token-admin/registration-token-admin.link', 'PHPDevShell', '2', '48580716', '0', '3', '4', '844895956', 'edit-token', '');
REPLACE INTO `pds_core_menu_items` VALUES ('2266433229', '0', 'Readme', 'user/readme.php', 'PHPDevShell', '1', '', '0', '1', '0', '844895956', 'readme', '');
REPLACE INTO `pds_core_menu_items` VALUES ('2273945344', '1814972020', 'Edit Group', 'user-admin/user-group-admin.link', 'PHPDevShell', '2', '1772410402', '0', '3', '4', '844895956', 'edit-group', '');
REPLACE INTO `pds_core_menu_items` VALUES ('2313706889', '2751748213', 'New Role', 'user-admin/user-role-admin.php', 'PHPDevShell', '1', '', '0', '2', '0', '844895956', 'new-role', '');
REPLACE INTO `pds_core_menu_items` VALUES ('2387241520', '2190226087', 'Manage Tokens', 'registration-token-admin/registration-token-admin-list.php', 'PHPDevShell', '1', '', '0', '1', '0', '844895956', 'manage-tokens', '');
REPLACE INTO `pds_core_menu_items` VALUES ('2390350678', '3669783681', 'Access Logs', 'logs-admin/menu-access-logs.php', 'PHPDevShell', '1', '', '0', '3', '0', '844895956', 'access-logs', '');
REPLACE INTO `pds_core_menu_items` VALUES ('2509699192', '0', 'System Management', 'user/control-panel.system-admin', 'PHPDevShell', '2', '940041356', '0', '10', '0', '844895956', 'system-management', '');
REPLACE INTO `pds_core_menu_items` VALUES ('2749758364', '930839394', 'Repair Database', 'cron/repair-database.php', 'PHPDevShell', '8', '', '0', '5', '1', '844895956', 'repair-database', '');
REPLACE INTO `pds_core_menu_items` VALUES ('2751748213', '1411278578', 'Role Admin', 'user-admin/user-role-admin-list.php.link', 'PHPDevShell', '2', '3642120161', '0', '3', '2', '844895956', 'role-admin', '');
REPLACE INTO `pds_core_menu_items` VALUES ('2946674795', '0', 'User Preferences', 'user/edit-preferences.php', 'PHPDevShell', '1', '', '0', '7', '0', '844895956', 'user-preferences', '');
REPLACE INTO `pds_core_menu_items` VALUES ('2953441878', '930839394', 'Trim Logs', 'cron/trim-logs.php', 'PHPDevShell', '8', '', '0', '4', '1', '844895956', 'trim-logs', '');
REPLACE INTO `pds_core_menu_items` VALUES ('3204262040', '3968968736', 'Manage Menus', 'menu-admin/menu-item-admin-list.php', 'PHPDevShell', '1', '', '0', '1', '0', '844895956', 'manage-menus', '');
REPLACE INTO `pds_core_menu_items` VALUES ('3247623521', '1411278578', 'Access Control', 'menu-admin/menu-item-admin-permissions.php', 'PHPDevShell', '1', '', '0', '1', '0', '844895956', 'access-control', '');
REPLACE INTO `pds_core_menu_items` VALUES ('3276230420', '1814972020', 'Manage Groups', 'user-admin/user-group-admin-list.php', 'PHPDevShell', '1', '', '0', '1', '0', '844895956', 'manage-groups', '');
REPLACE INTO `pds_core_menu_items` VALUES ('3440897808', '3968968736', 'Edit Menu', 'menu-admin/menu-item-admin.link', 'PHPDevShell', '2', '967550350', '0', '3', '4', '844895956', 'edit-menu', '');
REPLACE INTO `pds_core_menu_items` VALUES ('3467402321', '294626826', 'Import Users', 'user-admin/user-admin-import.php', 'PHPDevShell', '1', '', '0', '5', '0', '844895956', 'import-users', '');
REPLACE INTO `pds_core_menu_items` VALUES ('3642120161', '2751748213', 'Manage Roles', 'user-admin/user-role-admin-list.php', 'PHPDevShell', '1', '', '0', '1', '0', '844895956', 'manage-roles', '');
REPLACE INTO `pds_core_menu_items` VALUES ('3669783681', '2509699192', 'System Status', 'system-admin/admin.php.link', 'PHPDevShell', '2', '863779375', '0', '1', '2', '844895956', 'system-status', '');
REPLACE INTO `pds_core_menu_items` VALUES ('3682403894', '0', 'Log In|Out', 'user/login-page.php', 'PHPDevShell', '1', '', '0', '5', '0', '844895956', 'login', '');
REPLACE INTO `pds_core_menu_items` VALUES ('3727066128', '0', 'Register Account', 'user/register.php', 'PHPDevShell', '1', '', '0', '2', '0', '844895956', 'register-account', '');
REPLACE INTO `pds_core_menu_items` VALUES ('3776270042', '0', 'Contact Admin', 'user/email-admin.php', 'PHPDevShell', '1', '', '0', '6', '0', '844895956', 'contact-admin', '');
REPLACE INTO `pds_core_menu_items` VALUES ('3968968736', '2509699192', 'Menu Admin', 'menu-admin/menu-item-admin-list.php.link', 'PHPDevShell', '2', '3204262040', '0', '5', '2', '844895956', 'menu-admin', '');
REPLACE INTO `pds_core_menu_items` VALUES ('4134883375', '930839394', 'Manage Cronjobs', 'cron-admin/cronjob-admin-list.php', 'PHPDevShell', '1', '', '0', '1', '0', '844895956', 'manage-cronjobs', '');
REPLACE INTO `pds_core_menu_items` VALUES ('4250544529', '3968968736', 'Access Control', 'menu-admin/menu-item-admin-permissions.link', 'PHPDevShell', '2', '3247623521', '0', '4', '0', '844895956', 'access-control', '');
REPLACE INTO `pds_core_menu_items` VALUES ('4283172353', '0', 'New Password', 'user/new-password.php', 'PHPDevShell', '1', '', '0', '4', '1', '844895956', 'new-password', '');

INSERT INTO `pds_core_plugin_classes` VALUES ('', 'mailer', 'PHPDS_mailer', 'PHPMailer', '1', '1');
INSERT INTO `pds_core_plugin_classes` VALUES ('', 'wysiwygEditor', 'PHPDS_wysiwyg', 'TinyMCE', '1', '1');
INSERT INTO `pds_core_plugin_classes` VALUES ('', 'imaging', 'PHPDS_imaging', 'PHPThumbs', '1', '1');
INSERT INTO `pds_core_plugin_classes` VALUES ('', 'pagination', 'PHPDS_pagination', 'Pagination', '1', '1');
INSERT INTO `pds_core_plugin_classes` VALUES ('', 'views', 'PHPDS_views', 'Smarty', '1', '1');
INSERT INTO `pds_core_plugin_classes` VALUES ('', 'fileManager', 'PHPDS_fileManager', 'FileMan', '1', '1');
INSERT INTO `pds_core_plugin_classes` VALUES ('', 'group_tree', 'PHPDS_groups_tree', 'PHPDevShell', '1', '1');
INSERT INTO `pds_core_plugin_classes` VALUES ('', 'iana', 'PHPDS_iana', 'PHPDevShell', '1', '1');
INSERT INTO `pds_core_plugin_classes` VALUES ('', 'menu_array', 'PHPDS_menu_array', 'PHPDevShell', '1', '1');
INSERT INTO `pds_core_plugin_classes` VALUES ('', 'menu_structure', 'PHPDS_menu_structure', 'PHPDevShell', '1', '1');
INSERT INTO `pds_core_plugin_classes` VALUES ('', 'pluginmanager', 'PHPDS_pluginmanager', 'PHPDevShell', '1', '1');
INSERT INTO `pds_core_plugin_classes` VALUES ('', 'timezone', 'PHPDS_timezone', 'PHPDevShell', '1', '1');
INSERT INTO `pds_core_plugin_classes` VALUES ('', 'user_pending', 'PHPDS_user_pending', 'PHPDevShell', '1', '1');
INSERT INTO `pds_core_plugin_classes` VALUES ('', 'forms', 'PHPDS_forms', 'PHPFormBuilder', '1', '1');
INSERT INTO `pds_core_plugin_classes` VALUES ('', 'controlPanel', 'PHPDS_controlPanel', 'ControlPanel', '1', '1');
INSERT INTO `pds_core_plugin_classes` VALUES ('', 'fileManager', 'PHPDS_fileManager', 'FileMan', '1', '1');
INSERT INTO `pds_core_plugin_classes` VALUES ('', 'imaging', 'PHPDS_imaging', 'PHPThumbs', '1', '1');
