

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

insert into `dbversion` values('3.5', now());