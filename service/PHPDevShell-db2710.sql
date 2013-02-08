REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_allowed_ext', 'jpg,jpeg,png,gif,zip,tar,doc,xls,pdf');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_cmod', '0777');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_crop_thumb_dimension', '0,0,100,50');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_crop_thumb_fromcenter', '150');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_do_create_resize_image', '1');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_do_create_thumb', '1');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_do_thumb_reflect', '0');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_email_charset', 'UTF-8');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_email_encoding', '8bit');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_email_fromname', 'PHPDevShell');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_image_quality', '80');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_massmail_limit', '100');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_max_filesize', '2000000');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_max_filesize_show', '1.91 Mb');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_max_imagesize', '2000000');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_max_imagesize_show', '1.91 Mb');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_resize_image_dimension', '500,500');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_resize_thumb_dimension', '250,150');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_resize_thumb_percent', '50');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_smtp_helo', '');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_thumbnail_type', 'resize');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_thumb_reflect_settings', '40,40,80,true,#a4a4a4');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_log_uploads', '1');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_default_upload_directory', 'write/upload/');
REPLACE INTO `pds_core_settings` VALUES ('PHPDevShell_printable_template', 'cloud-printable');
ALTER TABLE `pds_core_users` MODIFY COLUMN `date_registered` int(10) unsigned NULL DEFAULT NULL AFTER `user_role`;
ALTER TABLE `pds_core_menu_access_logs` MODIFY COLUMN `timestamp` int(10) unsigned NULL DEFAULT NULL AFTER `user_id`;
ALTER TABLE `pds_core_logs` MODIFY COLUMN `log_time` int(10) unsigned NULL DEFAULT NULL AFTER `log_description`;

CREATE TABLE `pds_core_upload_logs` (
	`file_id` int(20) unsigned NOT NULL auto_increment,
	`sub_id` int(20) default NULL,
	`menu_id` int(20) default NULL,
	`alias` varchar(255) default NULL,
	`original_filename` varchar(255) default NULL,
	`new_filename` varchar(255) default NULL,
	`relative_path` text,
	`thumbnail` text,
	`resized` text,
	`extention` varchar(5) default NULL,
	`mime_type` varchar(255) default NULL,
	`file_desc` varchar(255) default NULL,
	`group_id` int(20) default NULL,
	`user_id` int(20) default NULL,
	`date_stored` int(10) unsigned default NULL,
	`file_size` int(14) default NULL,
	PRIMARY KEY  (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;