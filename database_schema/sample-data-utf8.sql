SET foreign_key_checks = 0;

delete from `groups` where `admin_group_id` is not null;
delete from `groups`;
alter table `groups` AUTO_INCREMENT = 1;
delete from `resources`;
alter table `resources` AUTO_INCREMENT = 1;
delete from `accessories`;
alter table `accessories` AUTO_INCREMENT = 1;
delete from `users`;
alter table `users` AUTO_INCREMENT = 1;
truncate table `group_roles`;
truncate table `user_groups`;

insert into `groups` (`group_id`, `name`) values (1, 'Group Administrators'), (2, 'Application Administrators'), (3, 'Resource Administrators'), (4, 'Schedule Administrators');

insert into `group_roles` (`group_id`, `role_id`) values (1, 1);
insert into `group_roles` (`group_id`, `role_id`) values (2, 2);
insert into `group_roles` (`group_id`, `role_id`) values (4, 4);

insert into `users` (`fname`, `lname`, `email`, `username`, `password`, `salt`, `timezone`, `lastlogin`, `status_id`, `date_created`, `language`, `organization`, `password_hash_version`)
values ('User', 'User', 'user@example.com', 'user', '$2y$10$SA03ESsWqeONkRSIX4y7FO1.qWGsFVSFlcfdGfqRTP5i0njhN1CZe', null, 'America/New_York', '2008-09-16 01:59:00', 1, now(), 'en_us', 'XYZ Org Inc.', 1),
       ('Admin', 'Admin', 'admin@example.com', 'admin', '$2y$10$SA03ESsWqeONkRSIX4y7FO1.qWGsFVSFlcfdGfqRTP5i0njhN1CZe', null, 'America/New_York', '2010-03-26 12:44:00', 1, now(), 'en_us', 'ABC Org Inc.', 1);

insert into `user_groups` (`user_id`, `group_id`) values (2,2);

insert into `resources` (`resource_id`, `name`, `location`, `contact_info`, `description`, `notes`, `min_duration`, `min_increment`, `max_duration`, `unit_cost`, `autoassign`, `requires_approval`, `allow_multiday_reservations`, `max_participants`, `min_notice_time_add`, `max_notice_time`, `image_name`, `schedule_id`) VALUES
  (1, 'Conference Room 1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 1, NULL, NULL, NULL, 'resource1.jpg', 1),
  (2, 'Conference Room 2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 1, NULL, NULL, NULL, 'resource2.jpg', 1);

insert into `accessories` (`accessory_id`, `accessory_name`, `accessory_quantity`) values
  (1, 'accessory limited to 10', 10),
  (2, 'accessory limited to 2', 2),
  (3, 'unlimited accessory', NULL);

truncate table `user_resource_permissions`;
insert into `user_resource_permissions` (`user_id`, `resource_id`, `permission_id`, `permission_type`) values (1,1,1,0),(1,2,1,0),(2,1,1,0),(2,2,1,0);

truncate table `custom_attributes`;
insert into `custom_attributes` (`custom_attribute_id`,`display_label`,`display_type`,`attribute_category`,`validation_regex`,`is_required`,`possible_values`) VALUES
  (1, 'Test Number', 1, 1, null, false, null),
  (2, 'Test String', 1, 1, null, false, null),
  (3, 'Test Number', 1, 4, null, false, null),
  (4, 'Test String', 1, 4, null, false, null);

SET foreign_key_checks = 1;