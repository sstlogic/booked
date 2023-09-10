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