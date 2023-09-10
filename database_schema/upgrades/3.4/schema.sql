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
