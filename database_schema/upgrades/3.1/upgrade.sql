

ALTER TABLE `resources`
    ADD COLUMN `min_participants` SMALLINT UNSIGNED;


insert into `dbversion` values('3.1', now());