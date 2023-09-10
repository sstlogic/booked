

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


insert into `dbversion` values('3.6', now());