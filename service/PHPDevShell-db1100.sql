ALTER TABLE `pds_core_users` DROP COLUMN `registration_type`;
ALTER TABLE `pds_core_users` MODIFY COLUMN `user_id` int(20) unsigned NOT NULL auto_increment;

CREATE TABLE `pds_core_registration_tokens` (
	`token_id` int(10) NOT NULL auto_increment,
	`token_name` varchar(255) default NULL,
	`user_role_id` int(10) default NULL,
	`user_group_id` int(10) default NULL,
	`token_key` varchar(42) default NULL,
	`registration_option` int(1) default NULL,
	`available_tokens` int(25) default NULL,
	PRIMARY KEY  (`token_id`),
	KEY `index` (`token_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `pds_core_registration_queue` (
	`user_id` int(20) unsigned NOT NULL default '0',
	`registration_type` int(1) default NULL,
	`token_id` int(20) default NULL,
	PRIMARY KEY  (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;