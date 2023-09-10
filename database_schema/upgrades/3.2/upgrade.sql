

ALTER TABLE `groups`
    ADD COLUMN `limit_on_reservation` TINYINT UNSIGNED;

ALTER TABLE `quotas`
    ADD COLUMN `interval` SMALLINT UNSIGNED;


insert into `dbversion` values('3.2', now());