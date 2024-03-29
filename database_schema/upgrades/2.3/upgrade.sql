

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

insert into `roles` values (4, 'Schedule Admin', 4);

insert into `dbversion` values('2.3', now());