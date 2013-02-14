-- Create filters for search.;
CREATE TABLE `pds_core_filter` (
	`search_id` int(255) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(20) DEFAULT NULL,
	`menu_id` varchar(64) NOT NULL,
	`filter_search` varchar(255) DEFAULT NULL,
	`filter_order` varchar(5) DEFAULT NULL,
	`filter_by` varchar(255) DEFAULT NULL,
	`exact_match` varchar(2) DEFAULT NULL,
	PRIMARY KEY (`search_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Create logs table for watchdog.;
CREATE TABLE `pds_core_logs` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`log_type` int(2) DEFAULT NULL,
	`log_description` text,
	`log_time` int(10) DEFAULT NULL,
	`user_id` int(30) DEFAULT NULL,
	`user_display_name` varchar(255) DEFAULT NULL,
	`menu_id` varchar(64) NOT NULL,
	`file_name` varchar(255) DEFAULT NULL,
	`menu_name` varchar(255) DEFAULT NULL,
	`user_ip` varchar(30) DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- Create table for default menu items.;
CREATE TABLE `pds_core_menu_items` (
	`menu_id` varchar(64) NOT NULL,
	`parent_menu_id` varchar(64) DEFAULT NULL,
	`menu_name` varchar(255) DEFAULT NULL,
	`menu_link` varchar(255) DEFAULT NULL,
	`plugin` varchar(255) DEFAULT NULL,
	`menu_type` int(1) DEFAULT NULL,
	`extend` varchar(255) DEFAULT NULL,
	`new_window` int(1) DEFAULT NULL,
	`rank` int(100) DEFAULT NULL,
	`hide` int(1) DEFAULT NULL,
	`template_id` varchar(64) unsigned DEFAULT NULL,
	`alias` varchar(255) DEFAULT NULL,
	`layout` varchar(255) DEFAULT NULL,
	`params` varchar(1024) DEFAULT NULL,
	PRIMARY KEY (`menu_id`),
	KEY `index` (`parent_menu_id`,`menu_link`,`plugin`,`alias`),
	KEY `params` (`params`(255)) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insert default menu items.;
INSERT INTO `pds_core_menu_items` VALUES ('readme', '0', 'Readme', 'readme.php', 'About', '1', null, '0', '1', '0', 'default', 'readme', null, null);

INSERT INTO `pds_core_menu_items` VALUES ('admin', '0', 'Admin', 'user/admin.system-admin', 'AdminTools', '2', 'system-settings', '0', '2', '0', 'default', 'admin', null, null);
INSERT INTO `pds_core_menu_items` VALUES ('system-settings', 'admin', 'Settings', 'system-admin/general-settings.php', 'AdminTools', '1', null, '0', '1', '0', 'default', 'system-settings', null, null);
INSERT INTO `pds_core_menu_items` VALUES ('config-manager', 'admin', 'Config', 'system-admin/config-manager.php', 'AdminTools', '1', null, '0', '2', '0', 'default', 'config-manager', null, null);

INSERT INTO `pds_core_menu_items` VALUES ('user-admin-list', 'admin', 'Users', 'user-admin/user-admin-list.php', 'AdminTools', '1', null, '0', '3', '0', 'default', 'user-admin-list', null, null);
INSERT INTO `pds_core_menu_items` VALUES ('user-admin', 'admin', 'User', 'user-admin/user-admin.php', 'AdminTools', '1', null, '0', '4', '3', 'default', 'user-admin', null, null);
INSERT INTO `pds_core_menu_items` VALUES ('group-admin-list', 'admin', 'Groups', 'user-admin/user-group-admin-list.php', 'AdminTools', '1', null, '0', '5', '0', 'default', 'group-admin-list', null, null);
INSERT INTO `pds_core_menu_items` VALUES ('group-admin', 'admin', 'Group', 'user-admin/user-group-admin.php', 'AdminTools', '1', null, '0', '6', '3', 'default', 'group-admin', null, null);
INSERT INTO `pds_core_menu_items` VALUES ('roles-admin-list', 'admin', 'Roles', 'user-admin/user-role-admin-list.php', 'AdminTools', '1', null, '0', '7', '0', 'default', 'roles-admin-list', null, null);
INSERT INTO `pds_core_menu_items` VALUES ('role-admin', 'admin', 'Role', 'user-admin/user-role-admin.php', 'AdminTools', '1', null, '0', '8', '3', 'default', 'role-admin', null, null);
INSERT INTO `pds_core_menu_items` VALUES ('menu-admin-list', 'admin', 'Menus', 'menu-admin/menu-item-admin-list.php', 'AdminTools', '1', null, '0', '9', '0', 'default', 'menu-admin-list', null, null);
INSERT INTO `pds_core_menu_items` VALUES ('menu-admin', 'admin', 'Menu', 'menu-admin/menu-item-admin.php', 'AdminTools', '1', null, '0', '10', '3', 'default', 'menu-admin', null, null);
INSERT INTO `pds_core_menu_items` VALUES ('tags-admin', 'admin', 'Tags', 'tagger-admin/tagger-admin.php', 'AdminTools', '1', null, '0', '11', '0', 'default', 'tags-admin', null, null);
INSERT INTO `pds_core_menu_items` VALUES ('themes-admin', 'admin', 'Themes', 'template-admin/template-admin-list.php', 'AdminTools', '1', null, '0', '12', '0', 'default', 'theme-admin', null, null);

INSERT INTO `pds_core_menu_items` VALUES ('file-logs', 'admin', 'File Logs', 'logs-admin/file-log-viewer.php', 'AdminTools', '1', null, '0', '13', '0', 'default', 'file-logs', null, null);
INSERT INTO `pds_core_menu_items` VALUES ('sys-logs', 'admin', 'Sys Logs', 'logs-admin/system-logs.php', 'AdminTools', '1', null, '0', '14', '0', 'default', 'sys-logs', null, null);

INSERT INTO `pds_core_menu_items` VALUES ('plugin-admin', 'admin', 'Plugins', 'plugin-activation.php', 'PluginManager', '1', null, '0', '15', '0', 'default', 'plugins-admin', null, null);
INSERT INTO `pds_core_menu_items` VALUES ('class-registry', 'admin', 'Registry', 'class-registry.php', 'PluginManager', '1', null, '0', '16', '0', 'default', 'class-registry', null, null);

-- Create menu tree structure.;
CREATE TABLE `pds_core_menu_structure` (
	`id` int(50) unsigned NOT NULL AUTO_INCREMENT,
	`menu_id` varchar(64) NOT NULL,
	`is_parent` int(1) DEFAULT NULL,
	`type` int(1) DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `index` (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insert menu tree structure.;
INSERT INTO `pds_core_menu_structure` (menu_id, is_parent, type) VALUES ('readme', '0', '2');
INSERT INTO `pds_core_menu_structure` (menu_id, is_parent, type) VALUES ('admin', '1', '1');
INSERT INTO `pds_core_menu_structure` (menu_id, is_parent, type) VALUES ('system-settings', '0', '4');
INSERT INTO `pds_core_menu_structure` (menu_id, is_parent, type) VALUES ('config-manager', '0', '4');
INSERT INTO `pds_core_menu_structure` (menu_id, is_parent, type) VALUES ('user-admin-list', '0', '4');
INSERT INTO `pds_core_menu_structure` (menu_id, is_parent, type) VALUES ('user-admin', '0', '4');
INSERT INTO `pds_core_menu_structure` (menu_id, is_parent, type) VALUES ('group-admin-list', '0', '4');
INSERT INTO `pds_core_menu_structure` (menu_id, is_parent, type) VALUES ('group-admin', '0', '4');
INSERT INTO `pds_core_menu_structure` (menu_id, is_parent, type) VALUES ('roles-admin-list', '0', '4');
INSERT INTO `pds_core_menu_structure` (menu_id, is_parent, type) VALUES ('roles-admin', '0', '4');
INSERT INTO `pds_core_menu_structure` (menu_id, is_parent, type) VALUES ('menu-admin-list', '0', '4');
INSERT INTO `pds_core_menu_structure` (menu_id, is_parent, type) VALUES ('menu-admin', '0', '4');
INSERT INTO `pds_core_menu_structure` (menu_id, is_parent, type) VALUES ('tags-admin', '0', '4');
INSERT INTO `pds_core_menu_structure` (menu_id, is_parent, type) VALUES ('themes-admin', '0', '4');
INSERT INTO `pds_core_menu_structure` (menu_id, is_parent, type) VALUES ('file-logs', '0', '4');
INSERT INTO `pds_core_menu_structure` (menu_id, is_parent, type) VALUES ('sys-logs', '0', '4');
INSERT INTO `pds_core_menu_structure` (menu_id, is_parent, type) VALUES ('plugin-admin', '0', '4');
INSERT INTO `pds_core_menu_structure` (menu_id, is_parent, type) VALUES ('class-registry', '0', '4');


-- Create plugins table.;
CREATE TABLE `pds_core_plugin_activation` (
	`plugin_folder` varchar(255) NOT NULL DEFAULT '0',
	`status` varchar(255) DEFAULT NULL,
	`version` int(16) NOT NULL,
	`use_logo` int(2) DEFAULT NULL,
	PRIMARY KEY (`plugin_folder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insert available default plugins.;
INSERT INTO `pds_core_plugin_activation` VALUES ('Pagination', 'install', '1000', '0');
INSERT INTO `pds_core_plugin_activation` VALUES ('Smarty', 'install', '1000', '0');
INSERT INTO `pds_core_plugin_activation` VALUES ('UserActions', 'install', '1000', '0');
INSERT INTO `pds_core_plugin_activation` VALUES ('StandardLogin', 'install', '1000', '0');
INSERT INTO `pds_core_plugin_activation` VALUES ('CRUD', 'install', '1000', '0');

-- Create classes available from default plugins.;
CREATE TABLE `pds_core_plugin_classes` (
	`class_id` int(10) NOT NULL AUTO_INCREMENT,
	`class_name` varchar(155) DEFAULT NULL,
	`alias` varchar(155) DEFAULT NULL,
	`plugin_folder` varchar(255) DEFAULT NULL,
	`enable` int(1) DEFAULT NULL,
	`rank` int(4) DEFAULT NULL,
	PRIMARY KEY (`class_id`),
	KEY `index` (`class_name`,`alias`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insert classes available from default plugins.;
INSERT INTO `pds_core_plugin_classes` (class_name, alias, plugin_folder, enable, rank) VALUES ('pagination', 'PHPDS_pagination', 'Pagination', '1', '1');
INSERT INTO `pds_core_plugin_classes` (class_name, alias, plugin_folder, enable, rank) VALUES ('views', 'PHPDS_views', 'Smarty', '1', '1');
INSERT INTO `pds_core_plugin_classes` (class_name, alias, plugin_folder, enable, rank) VALUES ('iana', 'PHPDS_iana', 'AdminTools', '1', '1');
INSERT INTO `pds_core_plugin_classes` (class_name, alias, plugin_folder, enable, rank) VALUES ('menuArray', 'PHPDS_menu_array', 'AdminTools', '1', '1');
INSERT INTO `pds_core_plugin_classes` (class_name, alias, plugin_folder, enable, rank) VALUES ('menuStructure', 'PHPDS_menu_structure', 'AdminTools', '1', '1');
INSERT INTO `pds_core_plugin_classes` (class_name, alias, plugin_folder, enable, rank) VALUES ('pluginManager', 'PHPDS_pluginmanager', 'PluginManager', '1', '1');
INSERT INTO `pds_core_plugin_classes` (class_name, alias, plugin_folder, enable, rank) VALUES ('timeZone', 'PHPDS_timezone', 'AdminTools', '1', '1');
INSERT INTO `pds_core_plugin_classes` (class_name, alias, plugin_folder, enable, rank) VALUES ('userActions', 'PHPDS_userAction', 'UserActions', '1', '1');
INSERT INTO `pds_core_plugin_classes` (class_name, alias, plugin_folder, enable, rank) VALUES ('StandardLogin', 'PHPDS_login', 'StandardLogin', '1', '1');
INSERT INTO `pds_core_plugin_classes` (class_name, alias, plugin_folder, enable, rank) VALUES ('crud', 'PHPDS_crud', 'CRUD', '1', '1');

-- Create session table.;
CREATE TABLE `pds_core_session` (
	`cookie_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(20) unsigned NOT NULL,
	`id_crypt` char(6) NOT NULL,
	`pass_crypt` char(32) NOT NULL,
	`timestamp` int(10) NOT NULL,
	PRIMARY KEY (`cookie_id`),
	KEY `index` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Create settings table.;
CREATE TABLE `pds_core_settings` (
	`setting_description` varchar(100) NOT NULL DEFAULT '',
	`setting_value` text,
	`note` text,
	PRIMARY KEY (`setting_description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insert default settings to make system work.;
INSERT INTO `pds_core_settings` VALUES ('AdminTools_allow_remember', '1', 'Should users be allowed to login with remember.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_banned_role', '6', 'The banned role. No access allowed.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_charset', 'UTF-8', 'Site wide charset.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_charset_format', '.{charset}', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_cmod', '0777', 'Writable forlder permissions');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_crypt_key', 'eDucDjodz8ZiMqFe8zeJ', 'General crypt key to protect system.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_custom_logo', '', 'Default system logo.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_date_format', 'F j, Y, g:i a O', 'Date format according to DateTime function of PHP.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_date_format_short', 'Y-m-d', 'Shorter date format.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_date_format_show', 'September 17, 2010, 12:59 pm +0000', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_date_format_show_short', '2010-09-17', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_debug_language', '', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_default_template', 'default', 'Default theme for all nodes.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_default_template_id', 'default', 'Default template id.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_default_upload_directory', 'write/upload/', 'Writable upload directory.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_demo_mode', '0', 'Should system be set into demo mode, no transactions will occur.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_spam_assassin', '1', 'Should system attempt to protect public forms from spam bots?');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_email_charset', 'UTF-8', 'Default email charset.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_email_critical', '1', 'Should critical errors be emailed to admin.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_email_encoding', '8bit', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_email_fromname', 'PHPDevShell', 'From which name should emails come.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_email_hostname', '', 'Email host name.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_email_new_registrations', '1', 'Should new registrations be mailed.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_email_option', 'smtp', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_footer_notes', 'PHPDevShell.org (c) 2011 GNU/GPL License.', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_from_email', 'no-reply@phphdevshell.org', 'From Email address.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_front_page_id', 'readme', 'The page to show when site is access.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_front_page_id_in', 'cp', 'The page to show when logged in and home or page is accessed.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_ftp_enable', '1', 'Should ftp be enabled.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_ftp_host', 'localhost', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_ftp_password', '', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_ftp_port', '21', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_ftp_root', '', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_ftp_ssl', '0', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_ftp_timeout', '90', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_ftp_username', 'usernameFTP', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_guest_group', '3', 'The systems guest group.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_guest_role', '5', 'The systems guest role.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_language', 'en', 'Default language.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_languages_available', 'en', 'List of language codes available');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_locale_format', '{lang}_{region}{charset}', 'Complete locale format.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_loginandout', 'login', 'The page to use to log-in and log-out.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_login_message', '', 'a Default message to welcome users loging in.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_meta_description', 'Administrative user interface based on AdminTools and other modern technologies.', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_meta_keywords', 'administrative, administrator, AdminTools, interface, ui, user', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_printable_template', 'default', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_queries_count', '1', 'Should queries be counted and info show.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_redirect_login', 'login', 'When a user logs in, where should he be redirected to?');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_region', 'US', 'Region settings.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_regions_available', 'US', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_resize_adaptive_dimension', '250,150', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_resize_image_dimension', '500,500', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_resize_thumb_dimension', '250,150', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_resize_thumb_percent', '50', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_root_group', '1', 'Root Group.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_root_id', '1', 'Root User.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_root_role', '1', 'Root Role.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_save', 'save', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_scripts_name_version', 'Powered by PHPDevShell', 'Footer message.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_sef_url', '0', 'Should SEF urls be enabled, not rename to .htaccess in root.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_sendmail_path', '/usr/sbin/sendmail', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_setting_admin_email', 'admin@phpdevshell.org', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_setting_support_email', 'default:System Support Query,default:General Query', 'Allows you to have multiple option for a email query.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_smtp_helo', '', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_smtp_host', 'smtp.gmail.com', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_smtp_password', '', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_smtp_port', '465', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_smtp_secure', 'ssl', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_smtp_timeout', '10', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_smtp_username', 'admin@phpdevshell.org', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_split_results', '30', 'When viewing paged results, how many results should be shown.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_system_down', '0', 'Is system currently down for development.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_system_down_message', '%s is currently down for maintenance. Some important features are being updated. Please return soon.', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_system_logging', '1', 'Should logs be written to database.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_system_timezone', 'UTC', 'Timezone.');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_test_email', 'test_email', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_trim_logs', '1000000', '');
INSERT INTO `pds_core_settings` VALUES ('AdminTools_url_append', '.html', 'The url extension in the end.');

-- Create tags table for tagging data.;
CREATE TABLE `pds_core_tags` (
	`tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`tag_object` varchar(45) DEFAULT NULL,
	`tag_name` varchar(45) DEFAULT NULL,
	`tag_target` varchar(45) DEFAULT NULL,
	`tag_value` text,
	PRIMARY KEY (`tag_id`),
	UNIQUE KEY `UNIQUE` (`tag_object`,`tag_name`,`tag_target`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create themes table to store installed themes.;
CREATE TABLE `pds_core_templates` (
	`template_id` varchar(64) unsigned NOT NULL,
	`template_folder` varchar(255) DEFAULT NULL,
	PRIMARY KEY (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insert default themes.;
INSERT INTO `pds_core_templates` VALUES ('default', 'default');

-- Create important user table to store all users.;
CREATE TABLE `pds_core_users` (
	`user_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
	`user_display_name` varchar(255) DEFAULT NULL,
	`user_name` varchar(255) DEFAULT NULL,
	`user_password` varchar(100) DEFAULT NULL,
	`user_email` varchar(100) DEFAULT NULL,
	`user_group` int(10) DEFAULT NULL,
	`user_role` int(10) DEFAULT NULL,
	`date_registered` int(10) DEFAULT NULL,
	`language` varchar(10) DEFAULT NULL,
	`timezone` varchar(255) DEFAULT NULL,
	`region` varchar(10) DEFAULT NULL,
	PRIMARY KEY (`user_id`),
	UNIQUE KEY `index_user` (`user_name`,`user_email`),
	KEY `index_general` (`user_display_name`,`user_group`,`user_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create primary groups table a user can belong to.;
CREATE TABLE `pds_core_user_groups` (
	`user_group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_group_name` varchar(255) DEFAULT NULL,
	`user_group_note` tinytext,
	PRIMARY KEY (`user_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insert primary groups table a user can belong to.;
INSERT INTO `pds_core_user_groups` VALUES ('1', 'Super', null);
INSERT INTO `pds_core_user_groups` VALUES ('2', 'Registered', null);
INSERT INTO `pds_core_user_groups` VALUES ('3', 'Guest', null);
INSERT INTO `pds_core_user_groups` VALUES ('4', 'Limited Admin', null);
INSERT INTO `pds_core_user_groups` VALUES ('5', 'Demo', null);

-- Create primary roles table a user can belong to.;
CREATE TABLE `pds_core_user_roles` (
	`user_role_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_role_name` varchar(255) DEFAULT NULL,
	`user_role_note` tinytext,
	PRIMARY KEY (`user_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insert primary roles table a user can belong to.;
INSERT INTO `pds_core_user_roles` VALUES ('1', 'Super', null);
INSERT INTO `pds_core_user_roles` VALUES ('2', 'Registered', null);
INSERT INTO `pds_core_user_roles` VALUES ('4', 'Awaiting Confirmation', null);
INSERT INTO `pds_core_user_roles` VALUES ('5', 'Guest', null);
INSERT INTO `pds_core_user_roles` VALUES ('6', 'Disabled', null);
INSERT INTO `pds_core_user_roles` VALUES ('7', 'Limited Admin', null);
INSERT INTO `pds_core_user_roles` VALUES ('8', 'Branch Admin', null);
INSERT INTO `pds_core_user_roles` VALUES ('9', 'Demo', null);

-- Create security role permissions table.;
CREATE TABLE `pds_core_user_role_permissions` (
  `user_role_id` int(10) NOT NULL DEFAULT '0',
  `menu_id` varchar(64) NOT NULL,
  PRIMARY KEY (`user_role_id`,`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insert default user permissions.;
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'edit-cronjob');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'pending-users');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'manage-users');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'system-settings');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'edit-role');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'policy-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'new-user');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'system-logs');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'theme-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'new-group');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'group-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'email-token');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'upload-logs');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'file-log-viewer');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'config-manager');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'token-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'edit-token');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'readme');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'edit-group');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'new-role');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'manage-tokens');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'access-logs');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'system-management');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'repair-database');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'role-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'user-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'user-preferences');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'trim-logs');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'manage-menus');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'manage-tags');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'access-control');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'manage-groups');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'edit-menu');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'import-users');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'manage-roles');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'system-status');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'login');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'contact-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'menu-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'manage-cronjobs');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'access-management');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'new-token');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'plugins-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'system-cronjob');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'system-info');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'edit-user');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'cronjob-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'cp');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'new-menu');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'optimize-database');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'system-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'new-password');
INSERT INTO pds_core_user_role_permissions VALUES ('1', 'class-registry');
INSERT INTO pds_core_user_role_permissions VALUES ('2', 'readme');
INSERT INTO pds_core_user_role_permissions VALUES ('2', 'user-preferences');
INSERT INTO pds_core_user_role_permissions VALUES ('2', 'login');
INSERT INTO pds_core_user_role_permissions VALUES ('2', 'contact-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('2', 'new-password');
INSERT INTO pds_core_user_role_permissions VALUES ('2', 'cp');
INSERT INTO pds_core_user_role_permissions VALUES ('4', 'finish-registration');
INSERT INTO pds_core_user_role_permissions VALUES ('4', 'readme');
INSERT INTO pds_core_user_role_permissions VALUES ('4', 'user-preferences');
INSERT INTO pds_core_user_role_permissions VALUES ('4', 'login');
INSERT INTO pds_core_user_role_permissions VALUES ('4', 'contact-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('4', 'new-password');
INSERT INTO pds_core_user_role_permissions VALUES ('4', 'cp');
INSERT INTO pds_core_user_role_permissions VALUES ('5', 'lost-password');
INSERT INTO pds_core_user_role_permissions VALUES ('5', 'finish-registration');
INSERT INTO pds_core_user_role_permissions VALUES ('5', 'readme');
INSERT INTO pds_core_user_role_permissions VALUES ('5', 'repair-database');
INSERT INTO pds_core_user_role_permissions VALUES ('5', 'trim-logs');
INSERT INTO pds_core_user_role_permissions VALUES ('5', 'login');
INSERT INTO pds_core_user_role_permissions VALUES ('5', 'register-account');
INSERT INTO pds_core_user_role_permissions VALUES ('5', 'contact-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('5', 'new-password');
INSERT INTO pds_core_user_role_permissions VALUES ('5', 'system-cronjob');
INSERT INTO pds_core_user_role_permissions VALUES ('5', 'cp');
INSERT INTO pds_core_user_role_permissions VALUES ('5', 'optimize-database');
INSERT INTO pds_core_user_role_permissions VALUES ('6', 'login');
INSERT INTO pds_core_user_role_permissions VALUES ('6', 'cp');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'pending-users');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'manage-users');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'system-settings');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'policy-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'new-user');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'system-logs');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'new-group');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'group-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'email-token');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'upload-logs');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'file-log-viewer');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'config-manager');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'token-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'edit-token');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'readme');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'edit-group');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'manage-tokens');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'access-logs');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'system-management');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'user-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'user-preferences');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'manage-groups');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'import-users');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'system-status');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'login');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'contact-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'new-password');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'new-token');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'system-info');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'edit-user');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'cp');
INSERT INTO pds_core_user_role_permissions VALUES ('7', 'system-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('8', 'pending-users');
INSERT INTO pds_core_user_role_permissions VALUES ('8', 'manage-users');
INSERT INTO pds_core_user_role_permissions VALUES ('8', 'policy-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('8', 'new-user');
INSERT INTO pds_core_user_role_permissions VALUES ('8', 'new-group');
INSERT INTO pds_core_user_role_permissions VALUES ('8', 'group-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('8', 'email-token');
INSERT INTO pds_core_user_role_permissions VALUES ('8', 'token-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('8', 'edit-token');
INSERT INTO pds_core_user_role_permissions VALUES ('8', 'readme');
INSERT INTO pds_core_user_role_permissions VALUES ('8', 'edit-group');
INSERT INTO pds_core_user_role_permissions VALUES ('8', 'manage-tokens');
INSERT INTO pds_core_user_role_permissions VALUES ('8', 'system-management');
INSERT INTO pds_core_user_role_permissions VALUES ('8', 'user-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('8', 'user-preferences');
INSERT INTO pds_core_user_role_permissions VALUES ('8', 'manage-groups');
INSERT INTO pds_core_user_role_permissions VALUES ('8', 'import-users');
INSERT INTO pds_core_user_role_permissions VALUES ('8', 'login');
INSERT INTO pds_core_user_role_permissions VALUES ('8', 'contact-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('8', 'new-password');
INSERT INTO pds_core_user_role_permissions VALUES ('8', 'new-token');
INSERT INTO pds_core_user_role_permissions VALUES ('8', 'edit-user');
INSERT INTO pds_core_user_role_permissions VALUES ('8', 'cp');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'edit-cronjob');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'pending-users');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'manage-users');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'system-settings');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'edit-role');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'policy-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'new-user');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'system-logs');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'theme-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'new-group');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'group-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'email-token');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'lost-password');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'upload-logs');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'file-log-viewer');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'config-manager');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'token-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'edit-token');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'readme');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'edit-group');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'new-role');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'manage-tokens');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'access-logs');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'system-management');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'repair-database');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'role-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'user-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'user-preferences');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'trim-logs');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'manage-menus');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'manage-tags');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'access-control');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'manage-groups');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'edit-menu');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'import-users');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'manage-roles');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'system-status');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'login');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'register-account');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'contact-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'menu-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'manage-cronjobs');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'access-management');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'new-password');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'new-token');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'plugins-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'system-cronjob');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'system-info');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'edit-user');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'cronjob-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'cp');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'new-menu');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'optimize-database');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'system-admin');
INSERT INTO pds_core_user_role_permissions VALUES ('9', 'class-registry');