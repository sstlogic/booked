

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

insert into `dbversion` values('2.9', now());