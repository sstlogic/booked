SET foreign_key_checks = 0;

CREATE TABLE `announcements` (
 `announcementid` mediumint(8) unsigned NOT NULL auto_increment,
 `announcement_text` text NOT NULL,
 `priority` mediumint(8),
 `start_date` datetime,
 `end_date` datetime,
 PRIMARY KEY (`announcementid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `layouts` (
 `layout_id` mediumint(8) unsigned NOT NULL auto_increment,
 `timezone` varchar(50) NOT NULL,
 PRIMARY KEY (`layout_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `time_blocks` (
 `block_id` mediumint(8) unsigned NOT NULL auto_increment,
 `label` varchar(85),
 `end_label` varchar(85),
 `availability_code` tinyint(2) unsigned NOT NULL,
 `layout_id` mediumint(8) unsigned NOT NULL,
 `start_time` time NOT NULL,
 `end_time` time NOT NULL,
 PRIMARY KEY (`block_id`),
 INDEX (`layout_id`),
 FOREIGN KEY (`layout_id`) 
	REFERENCES `layouts`(`layout_id`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `schedules` (
 `schedule_id` smallint(5) unsigned NOT NULL auto_increment,
 `name` varchar(85) NOT NULL,
 `isdefault` tinyint(1) unsigned NOT NULL,
 `weekdaystart` tinyint(2) unsigned NOT NULL,
 `daysvisible` tinyint(2) unsigned NOT NULL default '7',
 `layout_id` mediumint(8) unsigned NOT NULL,
 `legacyid` char(16),
 PRIMARY KEY (`schedule_id`),
 INDEX (`layout_id`),
 FOREIGN KEY (`layout_id`)
	REFERENCES `layouts`(`layout_id`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `groups` (
 `group_id` smallint(5) unsigned NOT NULL auto_increment,
 `name` varchar(85) NOT NULL,
 `admin_group_id` smallint(5) unsigned,
 `legacyid` char(16),
 PRIMARY KEY (`group_id`),
 FOREIGN KEY (`admin_group_id`)
	REFERENCES `groups`(`group_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `roles` (
 `role_id` tinyint(2) unsigned NOT NULL,
 `name` varchar(85),
 `role_level` tinyint(2) unsigned,
 PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `group_roles` (
 `group_id` smallint(8) unsigned NOT NULL,
 `role_id` tinyint(2) unsigned NOT NULL,
 PRIMARY KEY (`group_id`, `role_id`),
 INDEX (`group_id`),
 INDEX (`role_id`),
 FOREIGN KEY (`group_id`)
	REFERENCES `groups`(`group_id`)
	ON UPDATE CASCADE ON DELETE CASCADE,
 FOREIGN KEY (`role_id`)
	REFERENCES `roles`(`role_id`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `user_statuses` (
 `status_id` tinyint(2) unsigned NOT NULL,
 `description` varchar(85),
 PRIMARY KEY (`status_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `users` (
 `user_id` mediumint(8) unsigned NOT NULL auto_increment,
 `fname` varchar(85),
 `lname` varchar(85),
 `username` varchar(85),
 `email` varchar(85) NOT NULL,
 `password` varchar(85) NOT NULL,
 `salt` varchar(85) NOT NULL,
 `organization` varchar(85),
 `position` varchar(85),
 `phone` varchar(85),
 `timezone` varchar(85) NOT NULL,
 `language` VARCHAR(10) NOT NULL,
 `homepageid` tinyint(2) unsigned NOT NULL default '1',
 `date_created` datetime NOT NULL,
 `last_modified` timestamp,
 `lastlogin` datetime,
 `status_id` tinyint(2) unsigned NOT NULL,
 `legacyid` char(16),
 `legacypassword` varchar(32),
 PRIMARY KEY (`user_id`),
 INDEX (`status_id`),
 FOREIGN KEY (`status_id`) 
	REFERENCES `user_statuses`(`status_id`)
	ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `user_groups` (
 `user_id` mediumint(8) unsigned NOT NULL,
 `group_id` smallint(5) unsigned NOT NULL,
 PRIMARY KEY (`group_id`, `user_id`),
 INDEX (`user_id`),
 INDEX (`group_id`),
 FOREIGN KEY (`user_id`) 
	REFERENCES users(`user_id`)
	ON UPDATE CASCADE ON DELETE CASCADE,
 FOREIGN KEY (`group_id`) 
	REFERENCES `groups`(`group_id`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `resources` (
 `resource_id` smallint(5) unsigned NOT NULL auto_increment,
 `name` varchar(85) NOT NULL,
 `location` varchar(85),
 `contact_info` varchar(85),
 `description` text,
 `notes` text,
 `isactive` tinyint(1) unsigned NOT NULL default '1',
 `min_duration` int,
 `min_increment` int,
 `max_duration` int,
 `unit_cost` dec(7,2),
 `autoassign` tinyint(1) unsigned NOT NULL default '1',
 `requires_approval` tinyint(1) unsigned NOT NULL,
 `allow_multiday_reservations` tinyint(1) unsigned NOT NULL default '1',
 `max_participants` mediumint(8) unsigned,
 `min_notice_time` int,
 `max_notice_time` int,
 `image_name` varchar(50),
 `schedule_id` smallint(5) unsigned NOT NULL,
 `legacyid` char(16),
 PRIMARY KEY (`resource_id`),
 INDEX (`schedule_id`),
 FOREIGN KEY (`schedule_id`)
	REFERENCES `schedules`(`schedule_id`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `user_resource_permissions` (
 `user_id` mediumint(8) unsigned NOT NULL,
 `resource_id` smallint(5) unsigned NOT NULL,
 `permission_id` tinyint(2) unsigned NOT NULL default '1',
 PRIMARY KEY (`user_id`, `resource_id`),
 INDEX (`user_id`),
 INDEX (`resource_id`),
 FOREIGN KEY (`user_id`) 
	REFERENCES users(`user_id`)
	ON UPDATE CASCADE ON DELETE CASCADE,
 FOREIGN KEY (`resource_id`) 
	REFERENCES `resources`(`resource_id`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `group_resource_permissions` (
 `group_id` smallint(5) unsigned NOT NULL,
 `resource_id` smallint(5) unsigned NOT NULL,
 PRIMARY KEY (`group_id`, `resource_id`),
 INDEX (`group_id`),
 INDEX (`resource_id`),
 FOREIGN KEY (`group_id`) 
	REFERENCES `groups`(`group_id`) 
	ON UPDATE CASCADE ON DELETE CASCADE,
 FOREIGN KEY (`resource_id`) 
	REFERENCES `resources`(`resource_id`) 
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `reservation_types` (
 `type_id` tinyint(2) unsigned NOT NULL,
 `label` varchar(85) NOT NULL,
 PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `reservation_statuses` (
 `status_id` tinyint(2) unsigned NOT NULL,
 `label` varchar(85) NOT NULL,
 PRIMARY KEY (`status_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE  `reservation_series` (
  `series_id` int unsigned NOT NULL auto_increment,
  `date_created` datetime NOT NULL,
  `last_modified` datetime,
  `title` varchar(85) NOT NULL,
  `description` text,
  `allow_participation` tinyint(1) unsigned NOT NULL,
  `allow_anon_participation` tinyint(1) unsigned NOT NULL,
  `type_id` tinyint(2) unsigned NOT NULL,
  `status_id` tinyint(2) unsigned NOT NULL,
  `repeat_type` varchar(10) default NULL,
  `repeat_options` varchar(255) default NULL,
  `owner_id` mediumint(8) unsigned NOT NULL,
  `legacyid` char(16),
  PRIMARY KEY  (`series_id`),
  KEY `type_id` (`type_id`),
  KEY `status_id` (`status_id`),
  CONSTRAINT `reservations_type` FOREIGN KEY (`type_id`) REFERENCES `reservation_types` (`type_id`) ON UPDATE CASCADE,
  CONSTRAINT `reservations_status` FOREIGN KEY (`status_id`) REFERENCES `reservation_statuses` (`status_id`) ON UPDATE CASCADE,
  CONSTRAINT `reservations_owner` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`)  ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=UTF8MB4;

CREATE TABLE  `reservation_instances` (
  `reservation_instance_id` int unsigned NOT NULL auto_increment,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `reference_number` varchar(50) NOT NULL,
  `series_id` int unsigned NOT NULL,
  PRIMARY KEY  (`reservation_instance_id`),
  KEY `start_date` (`start_date`),
  KEY `end_date` (`end_date`),
  KEY `reference_number` (`reference_number`),
  KEY `series_id` (`series_id`),
  CONSTRAINT `reservations_series` FOREIGN KEY (`series_id`) REFERENCES `reservation_series` (`series_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=UTF8MB4;

CREATE TABLE `reservation_users` (
  `reservation_instance_id` int unsigned NOT NULL,
  `user_id` mediumint(8) unsigned NOT NULL,
  `reservation_user_level` tinyint(2) unsigned NOT NULL,
  PRIMARY KEY  (`reservation_instance_id`,`user_id`),
  KEY `reservation_instance_id` (`reservation_instance_id`),
  KEY `user_id` (`user_id`),
  KEY `reservation_user_level` (`reservation_user_level`),
  FOREIGN KEY (`reservation_instance_id`) REFERENCES `reservation_instances` (`reservation_instance_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=UTF8MB4;

CREATE TABLE `reservation_resources` (
 `series_id` int unsigned NOT NULL,
 `resource_id` smallint(5) unsigned NOT NULL,
 `resource_level_id` tinyint(2) unsigned NOT NULL,
 PRIMARY KEY (`series_id`, `resource_id`),
 INDEX (`resource_id`),
 INDEX (`series_id`),
 FOREIGN KEY (`resource_id`) 
	REFERENCES resources(`resource_id`)
	ON UPDATE CASCADE ON DELETE CASCADE,
 FOREIGN KEY (`series_id`) 
	REFERENCES `reservation_series`(`series_id`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE  `blackout_series` (
  `blackout_series_id` int unsigned NOT NULL auto_increment,
  `date_created` datetime NOT NULL,
  `last_modified` datetime,
  `title` varchar(85) NOT NULL,
  `description` text,
  `owner_id` mediumint(8) unsigned NOT NULL,
  `resource_id` mediumint(8) unsigned NOT NULL,
  `legacyid` char(16),
  PRIMARY KEY  (`blackout_series_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=UTF8MB4;

CREATE TABLE  `blackout_instances` (
  `blackout_instance_id` int unsigned NOT NULL auto_increment,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `blackout_series_id` int unsigned NOT NULL,
  PRIMARY KEY  (`blackout_instance_id`),
  INDEX `start_date` (`start_date`),
  INDEX `end_date` (`end_date`),
  INDEX `blackout_series_id` (`blackout_series_id`),
  FOREIGN KEY (`blackout_series_id`)
  	REFERENCES `blackout_series` (`blackout_series_id`)
  	ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=UTF8MB4;

CREATE TABLE `user_email_preferences` (
  `user_id` mediumint(8) unsigned NOT NULL,
  `event_category` varchar(45) NOT NULL,
  `event_type` varchar(45) NOT NULL,
 PRIMARY KEY (`user_id`, `event_category`, `event_type`),
 FOREIGN KEY (`user_id`)
	REFERENCES `users`(`user_id`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `quotas` (
 `quota_id` mediumint(8) unsigned NOT NULL auto_increment,
 `quota_limit` decimal(7,2) unsigned NOT NULL,
 `unit` varchar(25) NOT NULL,
 `duration` varchar(25) NOT NULL,
 `resource_id` smallint(5) unsigned,
 `group_id` smallint(5) unsigned,
 `schedule_id` smallint(5) unsigned,
 PRIMARY KEY (`quota_id`),
 FOREIGN KEY (`resource_id`)
	REFERENCES `resources`(`resource_id`)
	ON UPDATE CASCADE ON DELETE CASCADE,
 FOREIGN KEY (`group_id`)
	REFERENCES `groups`(`group_id`)
	ON UPDATE CASCADE ON DELETE CASCADE,
 FOREIGN KEY (`schedule_id`)
	REFERENCES `schedules`(`schedule_id`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `accessories` (
 `accessory_id` smallint(5) unsigned NOT NULL auto_increment,
 `accessory_name` varchar(85) NOT NULL,
 `accessory_quantity` tinyint(2) unsigned,
 `legacyid` char(16),
 PRIMARY KEY (`accessory_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `reservation_accessories` (
 `series_id` int unsigned NOT NULL,
 `accessory_id` smallint(5) unsigned NOT NULL,
 `quantity` tinyint(2) unsigned NOT NULL,
 PRIMARY KEY (`series_id`, `accessory_id`),
 INDEX (`accessory_id`),
 INDEX (`series_id`),
 FOREIGN KEY (`accessory_id`)
	REFERENCES accessories(`accessory_id`)
	ON UPDATE CASCADE ON DELETE CASCADE,
 FOREIGN KEY (`series_id`)
	REFERENCES `reservation_series`(`series_id`)
	ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

SET foreign_key_checks = 1;

-- UPGRADE TO VERSION 2.1



CREATE TABLE `dbversion` (
 `version_number` DOUBLE unsigned NOT NULL DEFAULT 0,
 `version_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

ALTER TABLE `resources` ADD COLUMN `admin_group_id` SMALLINT(5) unsigned;
ALTER TABLE `resources` ADD CONSTRAINT `admin_group_id` FOREIGN KEY (`admin_group_id`) REFERENCES `groups`(`group_id`) ON DELETE SET NULL;

ALTER TABLE `users` ADD COLUMN `public_id` VARCHAR(20);
CREATE UNIQUE INDEX `public_id` ON `users` (`public_id`);

ALTER TABLE `resources` ADD COLUMN `public_id` VARCHAR(20);
CREATE UNIQUE INDEX `public_id` ON `resources` (`public_id`);

ALTER TABLE `schedules` ADD COLUMN `public_id` VARCHAR(20);
CREATE UNIQUE INDEX `public_id` ON `schedules` (`public_id`);

ALTER TABLE `users` ADD COLUMN `allow_calendar_subscription` TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE `resources` ADD COLUMN `allow_calendar_subscription` TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE `schedules` ADD COLUMN `allow_calendar_subscription` TINYINT(1) NOT NULL DEFAULT 0;

-- UPGRADE TO VERSION 2.2




CREATE TABLE `custom_attributes` (
 `custom_attribute_id` mediumint(8) unsigned NOT NULL auto_increment,
 `display_label` varchar(50) NOT NULL,
 `display_type` tinyint(2) unsigned NOT NULL,
 `attribute_category` tinyint(2) unsigned NOT NULL,
 `validation_regex` varchar(50),
 `is_required` tinyint(1) unsigned NOT NULL,
 `possible_values` text,
 `sort_order` tinyint(2) unsigned,
  PRIMARY KEY (`custom_attribute_id`),
  INDEX (`attribute_category`),
  INDEX (`display_label`)
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `custom_attribute_values` (
 `custom_attribute_value_id` mediumint(8) unsigned NOT NULL auto_increment,
 `custom_attribute_id` mediumint(8) unsigned NOT NULL,
 `attribute_value` text NOT NULL,
 `entity_id` mediumint(8) unsigned NOT NULL,
 `attribute_category`  tinyint(2) unsigned NOT NULL,
  PRIMARY KEY (`custom_attribute_value_id`),
  INDEX (`custom_attribute_id`),
  INDEX `entity_category` (`entity_id`, `attribute_category`),
  INDEX `entity_attribute` (`entity_id`, `custom_attribute_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `account_activation` (
 `account_activation_id` mediumint(8) unsigned NOT NULL auto_increment,
 `user_id` mediumint(8) unsigned NOT NULL,
 `activation_code` varchar(30) NOT NULL,
 `date_created` datetime NOT NULL,
  PRIMARY KEY (`account_activation_id`),
  INDEX (`activation_code`),
  UNIQUE KEY (`activation_code`),
  FOREIGN KEY (`user_id`)
	REFERENCES `users`(`user_id`)
	ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE IF NOT EXISTS `reservation_files` (
  `file_id` int unsigned NOT NULL auto_increment,
  `series_id` int unsigned NOT NULL,
  `file_name` varchar(250) NOT NULL,
  `file_type` varchar(15) NOT NULL,
  `file_size` varchar(45) NOT NULL,
  `file_extension` varchar(10) NOT NULL,
  PRIMARY KEY  (`file_id`),
  FOREIGN KEY (`series_id`)
  	REFERENCES `reservation_series`(`series_id`)
  	ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

-- UPGRADE TO VERSION 2.3



ALTER TABLE `schedules` ADD COLUMN `admin_group_id` SMALLINT(5) unsigned;
ALTER TABLE `schedules` ADD CONSTRAINT `schedules_groups_admin_group_id` FOREIGN KEY (`admin_group_id`) REFERENCES `groups`(`group_id`) ON DELETE SET NULL;

CREATE TABLE `saved_reports` (
 `saved_report_id` mediumint(8) unsigned NOT NULL auto_increment,
 `report_name` varchar(50),
 `user_id` mediumint(8) unsigned NOT NULL,
 `date_created` datetime NOT NULL,
 `report_details` varchar(500) NOT NULL,
  PRIMARY KEY (`saved_report_id`),
  FOREIGN KEY (`user_id`)
	REFERENCES `users`(`user_id`)
	ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

ALTER TABLE `resources` ADD COLUMN `sort_order` TINYINT(2) unsigned;

-- UPGRADE TO VERSION 2.4



CREATE TABLE `user_session` (
 `user_session_id` mediumint(8) unsigned NOT NULL auto_increment,
 `user_id` mediumint(8) unsigned NOT NULL,
 `last_modified` datetime NOT NULL,
 `session_token` varchar(50) NOT NULL,
 `user_session_value` text NOT NULL,
  PRIMARY KEY (`user_session_id`),
  INDEX `user_session_user_id` (`user_id`),
  INDEX `user_session_session_token` (`session_token`),
  FOREIGN KEY (`user_id`)
	REFERENCES `users`(`user_id`)
	ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

ALTER TABLE `time_blocks` ADD COLUMN `day_of_week` SMALLINT(5) unsigned;

CREATE TABLE `reminders` (
 `reminder_id` int(11) unsigned NOT NULL auto_increment,
 `user_id` mediumint(8) unsigned NOT NULL,
 `address` text NOT NULL,
 `message` text NOT NULL,
 `sendtime` datetime NOT NULL,
 `refnumber` text NOT NULL,
 PRIMARY KEY (`reminder_id`),
 INDEX `reminders_user_id` (`user_id`),
 FOREIGN KEY (`user_id`)
 	REFERENCES `users`(`user_id`)
 	ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `reservation_reminders` (
 `reminder_id` int(11) unsigned NOT NULL auto_increment,
 `series_id` int unsigned NOT NULL,
 `minutes_prior` int unsigned NOT NULL,
 `reminder_type` tinyint(2) unsigned NOT NULL,
 PRIMARY KEY (`reminder_id`),
 FOREIGN KEY (`series_id`)
  	REFERENCES `reservation_series`(`series_id`)
  	ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

ALTER TABLE `users` ADD COLUMN `default_schedule_id` smallint(5) unsigned;

-- UPGRADE TO VERSION 2.5



ALTER TABLE `custom_attributes` ADD COLUMN `entity_id` mediumint(8) unsigned;

ALTER TABLE `resources` ADD COLUMN `resource_type_id` mediumint(8) unsigned;

DROP TABLE IF EXISTS `resource_group_assignment`;

CREATE TABLE `resource_groups` (
 `resource_group_id` mediumint(8) unsigned NOT NULL auto_increment,
 `resource_group_name` VARCHAR(75),
 `parent_id` mediumint(8) unsigned,
  PRIMARY KEY (`resource_group_id`),
  INDEX `resource_groups_parent_id` (`parent_id`),
  FOREIGN KEY (`parent_id`)
	REFERENCES `resource_groups`(`resource_group_id`)
	ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `resource_types` (
 `resource_type_id` mediumint(8) unsigned NOT NULL auto_increment,
 `resource_type_name` VARCHAR(75),
 `resource_type_description` TEXT,
  PRIMARY KEY (`resource_type_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

ALTER TABLE `resources` ADD FOREIGN KEY (`resource_type_id`) REFERENCES `resource_types`(`resource_type_id`) ON DELETE SET NULL;

CREATE TABLE `resource_group_assignment` (
 `resource_group_id` mediumint(8) unsigned NOT NULL,
 `resource_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`resource_group_id`, `resource_id`),
  INDEX `resource_group_assignment_resource_id` (`resource_id`),
  INDEX `resource_group_assignment_resource_group_id` (`resource_group_id`),
  FOREIGN KEY (`resource_group_id`)
		REFERENCES resource_groups(`resource_group_id`)
		ON DELETE CASCADE,
	FOREIGN KEY (`resource_id`)
		REFERENCES `resources`(`resource_id`)
	ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `blackout_series_resources` (
 `blackout_series_id` int unsigned NOT NULL,
 `resource_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`blackout_series_id`, `resource_id`),
	FOREIGN KEY (`blackout_series_id`)
		REFERENCES `blackout_series`(`blackout_series_id`)
		ON DELETE CASCADE,
	FOREIGN KEY (`resource_id`)
		REFERENCES `resources`(`resource_id`)
	ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

DELETE `blackout_series`
FROM `blackout_series`
LEFT JOIN `resources` ON `blackout_series`.`resource_id` = `resources`.`resource_id`
WHERE `resources`.`resource_id` IS NULL;

INSERT INTO `blackout_series_resources` SELECT `blackout_series_id`, `resource_id` FROM `blackout_series`;

ALTER TABLE `blackout_series` DROP COLUMN `resource_id`;
ALTER TABLE `blackout_series` ADD COLUMN `repeat_type` varchar(10) default NULL;
ALTER TABLE `blackout_series` ADD COLUMN `repeat_options` varchar(255) default NULL;

CREATE TABLE `user_preferences` (
 `user_preferences_id` int unsigned NOT NULL auto_increment,
 `user_id` mediumint(8) unsigned NOT NULL,
 `name` varchar(100) NOT NULL,
 `value` varchar(100),
 PRIMARY KEY (`user_preferences_id`),
 INDEX (`user_id`),
 FOREIGN KEY (`user_id`)
    REFERENCES `users`(`user_id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

ALTER TABLE `accessories` MODIFY COLUMN `accessory_quantity` smallint(5) unsigned;
ALTER TABLE `reservation_accessories` MODIFY COLUMN `quantity` smallint(5) unsigned;

CREATE TABLE `resource_status_reasons` (
 `resource_status_reason_id` smallint(5) unsigned NOT NULL auto_increment,
 `status_id` tinyint unsigned NOT NULL,
 `description` varchar(100),
 PRIMARY KEY (`resource_status_reason_id`),
 INDEX (`status_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8MB4;

ALTER TABLE `resources` ADD COLUMN `status_id` tinyint unsigned NOT NULL DEFAULT 1;
ALTER TABLE `resources` ADD COLUMN `resource_status_reason_id` smallint(5) unsigned;
ALTER TABLE `resources` ADD FOREIGN KEY (`resource_status_reason_id`) REFERENCES `resource_status_reasons`(`resource_status_reason_id`) ON DELETE SET NULL;
UPDATE `resources` SET `status_id` = `isactive`;
ALTER TABLE `resources` DROP COLUMN `isactive`;
ALTER TABLE `resources` ADD COLUMN `buffer_time` int unsigned;

-- UPGRADE TO VERSION 2.6



# noinspection SqlNoDataSourceInspectionForFile
ALTER TABLE `custom_attributes`
  ADD COLUMN `admin_only` TINYINT(1) UNSIGNED;

ALTER TABLE `user_preferences`
  CHANGE COLUMN `value` `value` TEXT;

ALTER TABLE `reservation_files`
  CHANGE COLUMN `file_type` `file_type` VARCHAR(75);

CREATE TABLE `reservation_color_rules` (
		`reservation_color_rule_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		`custom_attribute_id`       MEDIUMINT(8) UNSIGNED NOT NULL,
		`attribute_type`            SMALLINT UNSIGNED,
		`required_value`            TEXT,
		`comparison_type`           SMALLINT UNSIGNED,
		`color`                     VARCHAR(50),
  PRIMARY KEY (`reservation_color_rule_id`),
  FOREIGN KEY (`custom_attribute_id`)
  REFERENCES `custom_attributes` (`custom_attribute_id`)
    ON DELETE CASCADE
)
		ENGINE = InnoDB
		DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `resource_accessories` (
		`resource_accessory_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		`resource_id`           SMALLINT(5) UNSIGNED  NOT NULL,
		`accessory_id`          SMALLINT(5) UNSIGNED  NOT NULL,
		`minimum_quantity`      SMALLINT              NULL,
		`maximum_quantity`      SMALLINT              NULL,
		PRIMARY KEY (`resource_accessory_id`),
		FOREIGN KEY (`resource_id`)
		REFERENCES `resources` (`resource_id`)
				ON DELETE CASCADE,
		FOREIGN KEY (`accessory_id`)
		REFERENCES `accessories` (`accessory_id`)
				ON DELETE CASCADE
)
		ENGINE = InnoDB
		DEFAULT CHARACTER SET UTF8MB4;


ALTER TABLE `custom_attributes` ADD COLUMN `secondary_category` TINYINT(2) UNSIGNED;
ALTER TABLE `custom_attributes` ADD COLUMN `secondary_entity_ids` VARCHAR(2000);
ALTER TABLE `custom_attributes` ADD COLUMN `is_private` TINYINT(1) UNSIGNED;

ALTER TABLE `resource_groups`
  ADD COLUMN `public_id` VARCHAR(20);

ALTER TABLE `resources`
  MODIFY COLUMN `contact_info` VARCHAR(255);
ALTER TABLE `resources`
  MODIFY COLUMN `location` VARCHAR(255);

CREATE TABLE `resource_type_assignment` (
		`resource_id`      SMALLINT(5) UNSIGNED  NOT NULL,
		`resource_type_id` MEDIUMINT(8) UNSIGNED NOT NULL,
		PRIMARY KEY (`resource_id`, `resource_type_id`),
		FOREIGN KEY (`resource_id`)
		REFERENCES `resources` (`resource_id`)
				ON DELETE CASCADE,
		FOREIGN KEY (`resource_type_id`)
		REFERENCES `resource_types` (`resource_type_id`)
				ON DELETE CASCADE
)
		ENGINE = InnoDB
		DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `custom_attribute_entities` (
		`custom_attribute_id` MEDIUMINT(8) UNSIGNED NOT NULL,
		`entity_id`           MEDIUMINT(8) UNSIGNED NOT NULL,
		PRIMARY KEY (`custom_attribute_id`, `entity_id`),
		FOREIGN KEY (`custom_attribute_id`)
		REFERENCES `custom_attributes` (`custom_attribute_id`)
				ON DELETE CASCADE
)
		ENGINE = InnoDB
		DEFAULT CHARACTER SET UTF8MB4;

INSERT INTO `custom_attribute_entities` (`custom_attribute_id`, `entity_id`) (SELECT
        `custom_attribute_id`,
        `entity_id`
FROM `custom_attributes`
WHERE `entity_id` IS NOT NULL AND `entity_id` <> 0);

ALTER TABLE `custom_attributes`
  DROP COLUMN `entity_id`;

ALTER TABLE `quotas`
  ADD COLUMN `enforced_days` VARCHAR(15);
ALTER TABLE `quotas`
  ADD COLUMN `enforced_time_start` TIME;
ALTER TABLE `quotas`
  ADD COLUMN `enforced_time_end` TIME;
ALTER TABLE `quotas`
  ADD COLUMN `scope` VARCHAR(25);

ALTER TABLE `resources`
  ADD COLUMN `enable_check_in` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE `resources`
  ADD COLUMN `auto_release_minutes` SMALLINT UNSIGNED;
ALTER TABLE `resources` ADD INDEX( `auto_release_minutes`);
ALTER TABLE `resources`
  ADD COLUMN `color` VARCHAR(10);
ALTER TABLE `resources`
  ADD COLUMN `allow_display` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `reservation_instances`
  ADD COLUMN `checkin_date` DATETIME;
ALTER TABLE `reservation_instances` ADD INDEX( `checkin_date`);
ALTER TABLE `reservation_instances`
  ADD COLUMN `checkout_date` DATETIME;
ALTER TABLE `reservation_instances`
  ADD COLUMN `previous_end_date` DATETIME;
ALTER TABLE `reservation_series`
  ADD COLUMN `last_action_by` MEDIUMINT(8) UNSIGNED;

CREATE TABLE `reservation_guests` (
		`reservation_instance_id` INT UNSIGNED        NOT NULL,
		`email`                   VARCHAR(255)        NOT NULL,
		`reservation_user_level`  TINYINT(2) UNSIGNED NOT NULL,
		PRIMARY KEY (`reservation_instance_id`, `email`),
		KEY `reservation_guests_reservation_instance_id` (`reservation_instance_id`),
		KEY `reservation_guests_email_address` (`email`),
		KEY `reservation_guests_reservation_user_level` (`reservation_user_level`),
		FOREIGN KEY (`reservation_instance_id`) REFERENCES `reservation_instances` (`reservation_instance_id`)
				ON DELETE CASCADE
				ON UPDATE CASCADE
)
		ENGINE = InnoDB
		DEFAULT CHARACTER SET UTF8MB4;

ALTER TABLE `users`
  ADD COLUMN `credit_count` DECIMAL(7, 2) UNSIGNED;
ALTER TABLE `resources`
  ADD COLUMN `credit_count` DECIMAL(7, 2) UNSIGNED;
ALTER TABLE `resources`
  ADD COLUMN `peak_credit_count` DECIMAL(7, 2) UNSIGNED;
ALTER TABLE `reservation_instances`
  ADD COLUMN `credit_count` DECIMAL(7, 2) UNSIGNED;

CREATE TABLE `peak_times` (
		`peak_times_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		`schedule_id`   SMALLINT(5) UNSIGNED  NOT NULL,
		`all_day`   TINYINT(1) UNSIGNED  NOT NULL,
		`start_time`   VARCHAR(10),
		`end_time`   VARCHAR(10),
		`every_day`   TINYINT(1) UNSIGNED  NOT NULL,
		`peak_days`   VARCHAR(13),
		`all_year`   TINYINT(1) UNSIGNED  NOT NULL,
		`begin_month`   TINYINT(1) UNSIGNED  NOT NULL,
		`begin_day`   TINYINT(1) UNSIGNED  NOT NULL,
		`end_month`   TINYINT(1) UNSIGNED  NOT NULL,
		`end_day`   TINYINT(1) UNSIGNED  NOT NULL,
		PRIMARY KEY (`peak_times_id`),
		FOREIGN KEY (`schedule_id`)
		REFERENCES `schedules` (`schedule_id`)
				ON DELETE CASCADE
)
		ENGINE = InnoDB
		DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `announcement_groups` (
		`announcementid` MEDIUMINT(8) UNSIGNED NOT NULL,
		`group_id` SMALLINT(5) UNSIGNED NOT NULL,
		PRIMARY KEY (`announcementid`, `group_id`),
		FOREIGN KEY (`announcementid`)
		REFERENCES `announcements` (`announcementid`)
				ON DELETE CASCADE,
    FOREIGN KEY (`group_id`)
		REFERENCES `groups` (`group_id`)
				ON DELETE CASCADE
		)
    ENGINE = InnoDB
		DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `announcement_resources` (
		`announcementid` MEDIUMINT(8) UNSIGNED NOT NULL,
		`resource_id` SMALLINT(5) UNSIGNED NOT NULL,
		PRIMARY KEY (`announcementid`, `resource_id`),
		FOREIGN KEY (`announcementid`)
		REFERENCES `announcements` (`announcementid`)
				ON DELETE CASCADE,
    FOREIGN KEY (`resource_id`)
		REFERENCES `resources` (`resource_id`)
				ON DELETE CASCADE
		)
    ENGINE = InnoDB
		DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `reservation_waitlist_requests` (
  `reservation_waitlist_request_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` MEDIUMINT(8) UNSIGNED NOT NULL,
  `resource_id` SMALLINT(5) UNSIGNED NOT NULL,
  `start_date` DATETIME,
  `end_date` DATETIME,
  PRIMARY KEY (`reservation_waitlist_request_id`),
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`user_id`)
    ON DELETE CASCADE,
  FOREIGN KEY (`resource_id`)
  REFERENCES `resources` (`resource_id`)
    ON DELETE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET UTF8MB4;

ALTER TABLE `custom_attribute_values`
  CHANGE `custom_attribute_value_id`  `custom_attribute_value_id` INT(8) UNSIGNED NOT NULL AUTO_INCREMENT;


-- UPGRADE TO VERSION 2.7



# noinspection SqlNoDataSourceInspectionForFile

ALTER TABLE `users` CHANGE `credit_count` `credit_count` DECIMAL(7,2) NULL DEFAULT '0';
UPDATE users SET credit_count = 0 WHERE credit_count IS NULL;

ALTER TABLE `resources`
  CHANGE COLUMN `sort_order` `sort_order` SMALLINT UNSIGNED;

CREATE TABLE `payment_configuration` (
  `payment_configuration_id` TINYINT UNSIGNED       NOT NULL AUTO_INCREMENT,
  `credit_cost`              DECIMAL(7, 2) UNSIGNED NOT NULL,
  `credit_currency`          VARCHAR(10)            NOT NULL,
  PRIMARY KEY (`payment_configuration_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `payment_gateway_settings` (
  `gateway_type`  VARCHAR(255)  NOT NULL,
  `setting_name`  VARCHAR(255)  NOT NULL,
  `setting_value` VARCHAR(1000) NOT NULL,
  PRIMARY KEY (`gateway_type`, `setting_name`)
)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `credit_log` (
  `credit_log_id`  INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`  MEDIUMINT(8) UNSIGNED NOT NULL,
  `original_credit_count`  DECIMAL(7, 2),
  `credit_count`  DECIMAL(7, 2),
  `credit_note` VARCHAR(1000),
  `date_created` DATETIME NOT NULL,
  PRIMARY KEY (`credit_log_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `payment_transaction_log` (
  `payment_transaction_log_id`  INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`  MEDIUMINT(8) UNSIGNED NOT NULL,
  `status` VARCHAR(255) NOT NULL,
  `invoice_number` VARCHAR(50),
  `transaction_id` VARCHAR(50) NOT NULL,
  `subtotal_amount`  DECIMAL(7, 2) NOT NULL,
  `tax_amount`  DECIMAL(7, 2) NOT NULL,
  `total_amount`  DECIMAL(7, 2) NOT NULL,
  `transaction_fee`  DECIMAL(7, 2),
  `currency` VARCHAR(3) NOT NULL,
  `transaction_href` VARCHAR(500),
  `refund_href` VARCHAR(500),
  `date_created` DATETIME NOT NULL,
  `gateway_name` VARCHAR(100) NOT NULL,
  `gateway_date_created` VARCHAR(25) NOT NULL,
  `payment_response` TEXT,
  PRIMARY KEY (`payment_transaction_log_id`),
  KEY `user_id` (`user_id`),
  KEY `invoice_number` (`invoice_number`)
)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `refund_transaction_log` (
  `refund_transaction_log_id`  INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `payment_transaction_log_id`  INT(10) UNSIGNED NOT NULL,
  `status` VARCHAR(255) NOT NULL,
  `transaction_id` VARCHAR(50),
  `total_refund_amount`  DECIMAL(7, 2) NOT NULL,
  `payment_refund_amount`  DECIMAL(7, 2),
  `fee_refund_amount`  DECIMAL(7, 2),
  `transaction_href` VARCHAR(500),
  `date_created` DATETIME NOT NULL,
  `gateway_date_created` VARCHAR(25) NOT NULL,
  `refund_response` TEXT,
  PRIMARY KEY (`refund_transaction_log_id`),
  FOREIGN KEY (`payment_transaction_log_id`)
  REFERENCES `payment_transaction_log` (`payment_transaction_log_id`)
  ON DELETE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET UTF8MB4;

ALTER TABLE `groups`
  ADD COLUMN `isdefault` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE `groups` ADD INDEX(`isdefault`);

CREATE TABLE `terms_of_service` (
  `terms_of_service_id`  INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `terms_text` TEXT,
  `terms_url` VARCHAR(255),
  `terms_file` VARCHAR(50),
  `applicability` VARCHAR(50),
  `date_created` DATETIME NOT NULL,
  PRIMARY KEY (`terms_of_service_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET UTF8MB4;

ALTER TABLE `reservation_series`
  ADD COLUMN `terms_date_accepted` DATETIME;

ALTER TABLE `users`
  ADD COLUMN `terms_date_accepted` DATETIME;

ALTER TABLE `announcements`
  ADD COLUMN `display_page` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1;
ALTER TABLE `announcements` ADD INDEX (`start_date`);
ALTER TABLE `announcements` ADD INDEX (`end_date`);
ALTER TABLE `announcements` ADD INDEX (`display_page`);

ALTER TABLE `resources` CHANGE COLUMN `min_notice_time` `min_notice_time_add` INT;

ALTER TABLE `resources`
  ADD COLUMN `min_notice_time_update` INT;

ALTER TABLE `resources`
  ADD COLUMN `min_notice_time_delete` INT;

UPDATE resources SET min_notice_time_update = min_notice_time_add, min_notice_time_delete = min_notice_time_add;

ALTER TABLE `schedules` ADD COLUMN `start_date` DATETIME;
ALTER TABLE `schedules` ADD COLUMN `end_date` DATETIME;
ALTER TABLE `schedules` ADD COLUMN `allow_concurrent_bookings` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0;


ALTER TABLE `reservation_series`
  CHANGE COLUMN `title` `title` VARCHAR(300);

CREATE TABLE `resource_images` (
  `resource_image_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `resource_id` SMALLINT UNSIGNED NOT NULL,
  `image_name` VARCHAR(50),
  PRIMARY KEY (`resource_image_id`),
  FOREIGN KEY (`resource_id`)
 	REFERENCES `resources` (`resource_id`)
 	ON UPDATE CASCADE ON DELETE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET UTF8MB4;

ALTER TABLE `group_resource_permissions` ADD COLUMN `permission_type` TINYINT UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE `group_resource_permissions` DROP PRIMARY KEY, ADD PRIMARY KEY(`group_id`, `resource_id`);
ALTER TABLE `group_resource_permissions` ADD INDEX(`group_id`);
ALTER TABLE `group_resource_permissions` ADD INDEX(`resource_id`);

ALTER TABLE `user_resource_permissions` ADD COLUMN `permission_type` TINYINT UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE `user_resource_permissions` DROP PRIMARY KEY, ADD PRIMARY KEY(`user_id`, `resource_id`);
ALTER TABLE `user_resource_permissions` ADD INDEX(`user_id`);
ALTER TABLE `user_resource_permissions` ADD INDEX(`resource_id`);

ALTER TABLE `resources` ADD COLUMN `date_created` DATETIME;
ALTER TABLE `resources` ADD COLUMN `last_modified` DATETIME;

ALTER TABLE `custom_attribute_values` DROP INDEX `entity_category`;
ALTER TABLE `custom_attribute_values` DROP INDEX `entity_attribute`;
ALTER TABLE `custom_attribute_values` ADD INDEX(`entity_id`);
ALTER TABLE `custom_attribute_values` ADD INDEX(`attribute_category`);
ALTER TABLE `reservation_reminders` ADD INDEX(`reminder_type`);

ALTER TABLE `layouts` ADD COLUMN `layout_type` TINYINT UNSIGNED NOT NULL DEFAULT 0;

CREATE TABLE `custom_time_blocks` (
  `custom_time_block_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `start_time` DATETIME NOT NULL,
  `end_time` DATETIME NOT NULL,
  `layout_id` MEDIUMINT UNSIGNED NOT NULL,
  PRIMARY KEY (`custom_time_block_id`),
  FOREIGN KEY (`layout_id`)
 	REFERENCES `layouts` (`layout_id`)
 	ON UPDATE CASCADE ON DELETE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET UTF8MB4;

ALTER TABLE `schedules` ADD COLUMN `default_layout` TINYINT NOT NULL DEFAULT 0;

-- UPGRADE TO VERSION 2.8



# noinspection SqlNoDataSourceInspectionForFile

ALTER TABLE `schedules`
  ADD COLUMN `total_concurrent_reservations` SMALLINT UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `schedules`
  ADD COLUMN `max_resources_per_reservation` SMALLINT UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `schedules`
  ADD COLUMN `additional_properties` TEXT;

ALTER TABLE `resources`
  ADD COLUMN `additional_properties` TEXT;

-- UPGRADE TO VERSION 2.9



# noinspection SqlNoDataSourceInspectionForFile

ALTER TABLE `users`
  ADD COLUMN `api_only` TINYINT UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `accessories`
  ADD COLUMN `credit_count` DECIMAL(7,2) UNSIGNED;

ALTER TABLE `accessories`
  ADD COLUMN `peak_credit_count` DECIMAL(7,2) UNSIGNED;

ALTER TABLE `accessories`
  ADD COLUMN `credit_applicability` TINYINT UNSIGNED NOT NULL DEFAULT 1;

ALTER TABLE `resources`
  ADD COLUMN `credit_applicability` TINYINT UNSIGNED NOT NULL DEFAULT 1;

ALTER TABLE `resources`
  ADD COLUMN `credits_charged_all_slots` TINYINT UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `accessories`
  ADD COLUMN `credits_charged_all_slots` TINYINT UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `schedules`
  ADD COLUMN `allow_blocked_slot_end` TINYINT UNSIGNED NOT NULL DEFAULT 0;

-- UPGRADE TO VERSION 2.95



# noinspection SqlNoDataSourceInspectionForFile
CREATE TABLE `resource_relationships`
(
    `resource_relationship_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    `resource_id`              SMALLINT UNSIGNED NOT NULL,
    `related_resource_id`      SMALLINT UNSIGNED NOT NULL,
    `relationship_type`        TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (`resource_relationship_id`),
    FOREIGN KEY (`resource_id`)
        REFERENCES `resources`(`resource_id`)
        ON DELETE CASCADE,
    FOREIGN KEY (`related_resource_id`)
        REFERENCES `resources`(`resource_id`)
        ON DELETE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARACTER SET UTF8MB4;

-- UPGRADE TO VERSION 3.0



# noinspection SqlNoDataSourceInspectionForFile
CREATE TABLE `monitor_views`
(
    `monitor_view_id`     INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    `monitor_view_name`   VARCHAR(255),
    `serialized_settings` TEXT,
    `public_id`           VARCHAR(255)     NOT NULL,
    `date_created`        DATETIME         NOT NULL,
    `last_modified`       DATETIME,
    PRIMARY KEY (`monitor_view_id`),
    UNIQUE KEY (`public_id`)
)
    ENGINE = InnoDB
    DEFAULT CHARACTER SET UTF8MB4;

ALTER TABLE `groups`
    DROP COLUMN `legacyid`;

ALTER TABLE `resources`
    DROP COLUMN `legacyid`;

ALTER TABLE `reservation_series`
    DROP COLUMN `legacyid`;

ALTER TABLE `blackout_series`
    DROP COLUMN `legacyid`;

ALTER TABLE `accessories`
    DROP COLUMN `legacyid`;

ALTER TABLE `groups`
    CHANGE `name` `name` VARCHAR(255) NOT NULL;

CREATE TABLE `group_credit_replenishment_rule`
(
    `group_credit_replenishment_rule_id` SMALLINT UNSIGNED      NOT NULL AUTO_INCREMENT,
    `group_id`                           SMALLINT UNSIGNED      NOT NULL,
    `type`                               TINYINT UNSIGNED       NOT NULL,
    `amount`                             DECIMAL(7, 2) UNSIGNED NOT NULL,
    `day_of_month`                       TINYINT UNSIGNED       NOT NULL DEFAULT 0,
    `interval`                           SMALLINT UNSIGNED      NOT NULL DEFAULT 0,
    `last_replenishment_date`            DATETIME,
    PRIMARY KEY (`group_credit_replenishment_rule_id`),
    FOREIGN KEY (`group_id`)
        REFERENCES `groups` (`group_id`)
        ON DELETE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARACTER
        SET UTF8MB4;

CREATE TABLE `scheduled_job_status`
(
    `job_name`      VARCHAR(255)     NOT NULL,
    `last_run_date` DATETIME,
    `status`        TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (`job_name`)
)
    ENGINE = InnoDB
    DEFAULT CHARACTER
        SET UTF8MB4;

ALTER TABLE `resources`
    ADD COLUMN `auto_extend_reservations` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `users`
    ADD COLUMN `password_hash_version` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `users`
    CHANGE `password` `password` VARCHAR(255) NOT NULL;

ALTER TABLE `users`
    CHANGE `salt` `salt` VARCHAR(85) DEFAULT NULL;

ALTER TABLE `users`
    ADD COLUMN `force_password_reset` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `users`
    ADD COLUMN `login_token` VARCHAR(255) DEFAULT NULL;

ALTER TABLE `users`
    ADD COLUMN `remember_me_token` VARCHAR(255) DEFAULT NULL;

ALTER TABLE `users`
    ADD COLUMN `mfa_key` VARCHAR(255) DEFAULT NULL;

ALTER TABLE `users`
    ADD COLUMN `mfa_generated` DATETIME DEFAULT NULL;


CREATE TABLE `reset_password_requests`
(
    `user_id`      MEDIUMINT UNSIGNED NOT NULL,
    `date_created` DATETIME,
    `reset_token`  VARCHAR(255)       NOT NULL,
    PRIMARY KEY (`user_id`),
    FOREIGN KEY (`user_id`)
        REFERENCES `users` (`user_id`)
        ON DELETE CASCADE,
    UNIQUE KEY (`reset_token`)
)
    ENGINE = InnoDB
    DEFAULT CHARACTER
        SET UTF8MB4;

CREATE TABLE `user_resource_favorites`
(
    `user_id`      MEDIUMINT UNSIGNED NOT NULL,
    `resource_id`  SMALLINT UNSIGNED  NOT NULL,
    `date_created` DATETIME,
    PRIMARY KEY (`user_id`, `resource_id`),
    FOREIGN KEY (`user_id`)
        REFERENCES `users` (`user_id`)
        ON DELETE CASCADE,
    FOREIGN KEY (`resource_id`)
        REFERENCES `resources` (`resource_id`)
        ON DELETE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARACTER
        SET UTF8MB4;

ALTER TABLE `accessories`
    ADD COLUMN `date_created` DATETIME;
ALTER TABLE `accessories`
    ADD COLUMN `last_modified` DATETIME;

ALTER TABLE `announcements`
    ADD COLUMN `date_created` DATETIME;
ALTER TABLE `announcements`
    ADD COLUMN `last_modified` DATETIME;

ALTER TABLE `custom_attributes`
    ADD COLUMN `date_created` DATETIME;
ALTER TABLE `custom_attributes`
    ADD COLUMN `last_modified` DATETIME;

ALTER TABLE `custom_attribute_values`
    ADD COLUMN `date_created` DATETIME;
ALTER TABLE `custom_attribute_values`
    ADD COLUMN `last_modified` DATETIME;

ALTER TABLE `groups`
    ADD COLUMN `date_created` DATETIME;
ALTER TABLE `groups`
    ADD COLUMN `last_modified` DATETIME;

ALTER TABLE `group_resource_permissions`
    ADD COLUMN `date_created` DATETIME;
ALTER TABLE `group_resource_permissions`
    ADD COLUMN `last_modified` DATETIME;

ALTER TABLE `group_roles`
    ADD COLUMN `date_created` DATETIME;
ALTER TABLE `group_roles`
    ADD COLUMN `last_modified` DATETIME;

ALTER TABLE `group_credit_replenishment_rule`
    ADD COLUMN `date_created` DATETIME;
ALTER TABLE `group_credit_replenishment_rule`
    ADD COLUMN `last_modified` DATETIME;

ALTER TABLE `layouts`
    ADD COLUMN `date_created` DATETIME;
ALTER TABLE `layouts`
    ADD COLUMN `last_modified` DATETIME;

ALTER TABLE `payment_configuration`
    ADD COLUMN `date_created` DATETIME;
ALTER TABLE `payment_configuration`
    ADD COLUMN `last_modified` DATETIME;

ALTER TABLE `payment_gateway_settings`
    ADD COLUMN `date_created` DATETIME;
ALTER TABLE `payment_gateway_settings`
    ADD COLUMN `last_modified` DATETIME;

ALTER TABLE `peak_times`
    ADD COLUMN `date_created` DATETIME;
ALTER TABLE `peak_times`
    ADD COLUMN `last_modified` DATETIME;

ALTER TABLE `quotas`
    ADD COLUMN `date_created` DATETIME;
ALTER TABLE `quotas`
    ADD COLUMN `last_modified` DATETIME;

ALTER TABLE `reservation_color_rules`
    ADD COLUMN `date_created` DATETIME;
ALTER TABLE `reservation_color_rules`
    ADD COLUMN `last_modified` DATETIME;

ALTER TABLE `reservation_waitlist_requests`
    ADD COLUMN `date_created` DATETIME;
ALTER TABLE `reservation_waitlist_requests`
    ADD COLUMN `last_modified` DATETIME;

ALTER TABLE `resource_accessories`
    ADD COLUMN `date_created` DATETIME;
ALTER TABLE `resource_accessories`
    ADD COLUMN `last_modified` DATETIME;

ALTER TABLE `resource_groups`
    ADD COLUMN `date_created` DATETIME;
ALTER TABLE `resource_groups`
    ADD COLUMN `last_modified` DATETIME;

ALTER TABLE `resource_group_assignment`
    ADD COLUMN `date_created` DATETIME;
ALTER TABLE `resource_group_assignment`
    ADD COLUMN `last_modified` DATETIME;

ALTER TABLE `resource_images`
    ADD COLUMN `date_created` DATETIME;
ALTER TABLE `resource_images`
    ADD COLUMN `last_modified` DATETIME;

ALTER TABLE `resource_types`
    ADD COLUMN `date_created` DATETIME;
ALTER TABLE `resource_types`
    ADD COLUMN `last_modified` DATETIME;

ALTER TABLE `resource_relationships`
    ADD COLUMN `date_created` DATETIME;
ALTER TABLE `resource_relationships`
    ADD COLUMN `last_modified` DATETIME;

ALTER TABLE `schedules`
    ADD COLUMN `date_created` DATETIME;
ALTER TABLE `schedules`
    ADD COLUMN `last_modified` DATETIME;

ALTER TABLE `user_groups`
    ADD COLUMN `date_created` DATETIME;
ALTER TABLE `user_groups`
    ADD COLUMN `last_modified` DATETIME;

ALTER TABLE `user_resource_permissions`
    ADD COLUMN `date_created` DATETIME;
ALTER TABLE `user_resource_permissions`
    ADD COLUMN `last_modified` DATETIME;

ALTER TABLE `resources`
    ADD COLUMN `checkin_limited_to_admins` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `dbversion`
    CHANGE `version_number` `version_number` VARCHAR(255) NOT NULL;

ALTER TABLE `accessories`
    ADD COLUMN `public_id` VARCHAR(20);

CREATE UNIQUE INDEX `accessories_public_id_unique`
    ON `accessories` (`public_id`);

-- UPGRADE TO VERSION 3.1



ALTER TABLE `resources`
    ADD COLUMN `min_participants` SMALLINT UNSIGNED;


-- UPGRADE TO VERSION 3.2



ALTER TABLE `groups`
    ADD COLUMN `limit_on_reservation` TINYINT UNSIGNED;

ALTER TABLE `quotas`
    ADD COLUMN `interval` SMALLINT UNSIGNED;


-- UPGRADE TO VERSION 3.3



ALTER TABLE `saved_reports`
    ADD COLUMN `report_schedule` TEXT;

ALTER TABLE `saved_reports`
    ADD COLUMN `report_last_sent_date` DATETIME;

ALTER TABLE `users`
    ADD COLUMN `phone_country_code` VARCHAR(10);

ALTER TABLE `users`
    ADD COLUMN `phone_last_updated` DATETIME;

ALTER TABLE `user_email_preferences`
    ADD COLUMN `notification_method` TINYINT UNSIGNED NOT NULL DEFAULT 1;

ALTER TABLE `user_email_preferences` DROP PRIMARY KEY, ADD PRIMARY KEY(`user_id`, `event_category`, `event_type`, `notification_method`);

CREATE TABLE `user_sms`
(
    `user_sms_id`       INTEGER UNSIGNED   NOT NULL AUTO_INCREMENT,
    `user_id`           MEDIUMINT UNSIGNED NOT NULL,
    `opt_in_date`       DATETIME,
    `opt_out_date`      DATETIME,
    `date_created`      DATETIME           NOT NULL,
    `last_modified`     DATETIME,
    `confirmation_code` VARCHAR(10),
    PRIMARY KEY (`user_sms_id`),
    FOREIGN KEY (`user_id`)
        REFERENCES `users` (`user_id`)
        ON DELETE CASCADE,
    INDEX (`date_created`)
)
    ENGINE = InnoDB
    DEFAULT CHARACTER SET UTF8MB4;


ALTER TABLE `accessories` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `account_activation` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `announcement_groups` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `announcement_resources` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `announcements` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `blackout_instances` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `blackout_series` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `blackout_series_resources` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `credit_log` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `custom_attribute_entities` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `custom_attribute_values` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `custom_attributes` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `custom_time_blocks` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `dbversion` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `group_credit_replenishment_rule` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `group_resource_permissions` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `group_roles` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `groups` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `layouts` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `monitor_views` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `payment_configuration` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `payment_gateway_settings` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `payment_transaction_log` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `peak_times` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `quotas` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `refund_transaction_log` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `reminders` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `reservation_accessories` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `reservation_color_rules` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `reservation_files` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `reservation_guests` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `reservation_instances` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `reservation_reminders` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `reservation_resources` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `reservation_series` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `reservation_statuses` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `reservation_types` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `reservation_users` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `reservation_waitlist_requests` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `reset_password_requests` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `resource_accessories` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `resource_group_assignment` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `resource_groups` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `resource_images` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `resource_relationships` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `resource_status_reasons` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `resource_type_assignment` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `resource_types` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `resources` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `roles` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `saved_reports` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `scheduled_job_status` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `schedules` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `terms_of_service` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `time_blocks` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `user_email_preferences` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `user_groups` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `user_preferences` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `user_resource_favorites` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `user_resource_permissions` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `user_session` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `user_sms` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `user_statuses` CONVERT TO CHARACTER SET UTF8MB4;
ALTER TABLE `users` CONVERT TO CHARACTER SET UTF8MB4;

-- UPGRADE TO VERSION 3.4



CREATE TABLE `reservation_meeting_links`
(
    `reservation_meeting_link_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    `series_id`                   INTEGER UNSIGNED NOT NULL,
    `meeting_link_type`           SMALLINT UNSIGNED NOT NULL,
    `meeting_link_url`            VARCHAR(1000) NOT NULL,
    `meeting_external_id`         VARCHAR(1000),
    `meeting_metadata`            TEXT,
    `date_created`                DATETIME      NOT NULL,
    `last_modified`               DATETIME,
    PRIMARY KEY (`reservation_meeting_link_id`),
    FOREIGN KEY (`series_id`)
        REFERENCES `reservation_series` (`series_id`)
        ON DELETE CASCADE
) ENGINE = InnoDB
    DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `user_oauth`
(
    `user_oauth_id`  INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`        MEDIUMINT UNSIGNED NOT NULL,
    `access_token`   VARCHAR(1000) NOT NULL,
    `refresh_token`  VARCHAR(1000) NOT NULL,
    `expires_at`     DATETIME      NOT NULL,
    `provider_id`    SMALLINT UNSIGNED NOT NULL,
    `oauth_metadata` TEXT,
    `date_created`   DATETIME      NOT NULL,
    `last_modified`  DATETIME,
    PRIMARY KEY (`user_oauth_id`),
    FOREIGN KEY (`user_id`)
        REFERENCES `users` (`user_id`)
        ON DELETE CASCADE
) ENGINE = InnoDB
    DEFAULT CHARACTER SET UTF8MB4;


-- UPGRADE TO VERSION 3.5



ALTER TABLE `user_oauth`
    MODIFY `access_token` VARCHAR (5000) NOT NULL;

ALTER TABLE `user_oauth`
    MODIFY `refresh_token` VARCHAR (5000) NOT NULL;

ALTER TABLE `users`
    MODIFY `organization` VARCHAR (300);

ALTER TABLE `users`
    ADD COLUMN `date_format` TINYINT UNSIGNED;

ALTER TABLE `users`
    ADD COLUMN `time_format` TINYINT UNSIGNED;

ALTER TABLE `quotas`
    ADD COLUMN `stop_enforcement_minutes_prior` INTEGER UNSIGNED;

-- UPGRADE TO VERSION 3.6



CREATE TABLE `resource_maps`
(
    `resource_map_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`            VARCHAR(1000),
    `public_id`       VARCHAR(25)       NOT NULL,
    `status_id`       SMALLINT UNSIGNED NOT NULL,
    `file_type`       VARCHAR(75)       NOT NULL,
    `file_size`       VARCHAR(45)       NOT NULL,
    `file_extension`  VARCHAR(10)       NOT NULL,
    `date_created`    DATETIME          NOT NULL,
    `last_modified`   DATETIME,
    PRIMARY KEY (`resource_map_id`),
    UNIQUE KEY (`public_id`)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET UTF8MB4;

CREATE TABLE `resource_map_resources`
(
    `resource_map_resources_id` INTEGER UNSIGNED  NOT NULL AUTO_INCREMENT,
    `resource_map_id`           SMALLINT UNSIGNED NOT NULL,
    `resource_id`               SMALLINT UNSIGNED NOT NULL,
    `public_id`                 VARCHAR(25)       NOT NULL,
    `coordinates`               TEXT              NOT NULL,
    `date_created`              DATETIME          NOT NULL,
    `last_modified`             DATETIME,
    PRIMARY KEY (`resource_map_resources_id`),
    UNIQUE KEY (`public_id`),
    FOREIGN KEY (`resource_map_id`)
        REFERENCES `resource_maps` (`resource_map_id`)
        ON DELETE CASCADE,
    FOREIGN KEY (`resource_id`)
        REFERENCES `resources` (`resource_id`)
        ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARACTER SET UTF8MB4;

ALTER TABLE `reservation_series`
    ADD COLUMN `approved_by` MEDIUMINT UNSIGNED;

ALTER TABLE `reservation_series`
    ADD COLUMN `date_approved` DATETIME;

ALTER TABLE `reservation_series`
    ADD COLUMN `delete_reason` TEXT;

CREATE TABLE `oauth_authentication_providers`
(
    `provider_id`        SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `public_id`          VARCHAR(25)       NOT NULL,
    `provider_name`      VARCHAR(255)      NOT NULL,
    `client_id`          VARCHAR(1000)     NOT NULL,
    `client_secret`      TEXT              NOT NULL,
    `url_authorize`      VARCHAR(1000)     NOT NULL,
    `url_access_token`   VARCHAR(1000)     NOT NULL,
    `url_user_details`   VARCHAR(1000)     NOT NULL,
    `access_token_grant` VARCHAR(75)       NOT NULL,
    `field_mappings`     TEXT              NOT NULL,
    `scope`              VARCHAR(1000)     NOT NULL,
    `date_created`       DATETIME          NOT NULL,
    `last_modified`      DATETIME,
    PRIMARY KEY (`provider_id`),
    UNIQUE KEY (`public_id`)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET UTF8MB4;

ALTER TABLE `reservation_color_rules`
    ADD COLUMN `priority` SMALLINT UNSIGNED;

ALTER TABLE `resources`
    ADD COLUMN `auto_release_action` SMALLINT UNSIGNED;
