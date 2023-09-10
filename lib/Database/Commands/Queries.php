<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

class Queries
{
    private function __construct()
    {
    }

    const ADD_ACCESSORY =
        'INSERT INTO `accessories` (`accessory_name`, `accessory_quantity`, `date_created`)
		VALUES (@accessoryname, @quantity, UTC_TIMESTAMP())';

    const ADD_ACCESSORY_RESOURCE =
        'INSERT INTO `resource_accessories` (`resource_id`, `accessory_id`, `minimum_quantity`, `maximum_quantity`, `date_created`)
		VALUES (@resourceid, @accessoryid, @minimum_quantity, @maximum_quantity, UTC_TIMESTAMP())';

    const ADD_ACCOUNT_ACTIVATION =
        'INSERT INTO `account_activation` (`user_id`, `activation_code`, `date_created`) VALUES (@userid, @activation_code, @dateCreated)';

    const ADD_ANNOUNCEMENT =
        'INSERT INTO `announcements` (`announcement_text`, `priority`, `start_date`, `end_date`, `display_page`, `date_created`)
		VALUES (@text, @priority, @startDate, @endDate, @display_page, UTC_TIMESTAMP())';

    const ADD_ANNOUNCEMENT_GROUP = 'INSERT INTO `announcement_groups` (`announcementid`, `group_id`) VALUES (@announcementid, @groupid)';

    const ADD_ANNOUNCEMENT_RESOURCE = 'INSERT INTO `announcement_resources` (`announcementid`, `resource_id`) VALUES (@announcementid, @resourceid)';

    const ADD_ATTRIBUTE =
        'INSERT INTO `custom_attributes` (`display_label`, `display_type`, `attribute_category`, `validation_regex`, `is_required`, `possible_values`, `sort_order`, `admin_only`, `secondary_category`, `secondary_entity_ids`, `is_private`, `date_created`)
		VALUES (@display_label, @display_type, @attribute_category, @validation_regex, @is_required, @possible_values, @sort_order, @admin_only, @secondary_category, @secondary_entity_ids, @is_private, UTC_TIMESTAMP())';

    const ADD_ATTRIBUTE_ENTITY =
        'INSERT INTO `custom_attribute_entities` (`custom_attribute_id`, `entity_id`)
				VALUES (@custom_attribute_id, @entity_id)';

    const ADD_ATTRIBUTE_VALUE =
        'INSERT INTO `custom_attribute_values` (`custom_attribute_id`, `attribute_category`, `attribute_value`, `entity_id`, `date_created`)
			VALUES (@custom_attribute_id, @attribute_category, @attribute_value, @entity_id, UTC_TIMESTAMP())';

    const ADD_BLACKOUT_INSTANCE =
        'INSERT INTO `blackout_instances` (`start_date`, `end_date`, `blackout_series_id`)
		VALUES (@startDate, @endDate, @seriesid)';

    const ADD_BLACKOUT_RESOURCE = 'INSERT INTO `blackout_series_resources` (`blackout_series_id`, `resource_id`) VALUES (@seriesid, @resourceid)';

    const ADD_EMAIL_PREFERENCE =
        'INSERT IGNORE INTO `user_email_preferences` (`user_id`, `event_category`, `event_type`, `notification_method`) VALUES (@userid, @event_category, @event_type, @notification_method)';

    const ADD_BLACKOUT_SERIES =
        'INSERT INTO `blackout_series` (`date_created`, `title`, `owner_id`, `repeat_type`, `repeat_options`) VALUES (@dateCreated, @title, @userid, @repeatType, @repeatOptions)';

    const ADD_GROUP =
        'INSERT INTO `groups` (`name`, `isdefault`, `date_created`) VALUES (@groupname, @isdefault, UTC_TIMESTAMP())';

    const ADD_GROUP_RESOURCE_PERMISSION =
        'INSERT INTO `group_resource_permissions` (`group_id`, `resource_id`, `permission_type`, `date_created`) 
			VALUES (@groupid, @resourceid, @permission_type, UTC_TIMESTAMP())';

    const ADD_GROUP_ROLE =
        'INSERT IGNORE INTO `group_roles` (`group_id`, `role_id`, `date_created`) VALUES (@groupid, @roleid, UTC_TIMESTAMP())';

    const ADD_GROUP_USER_CREDITS = 'INSERT INTO `credit_log` (`user_id`, `original_credit_count`, `credit_count`, `credit_note`, `date_created`) 
            SELECT `user_id`, `credit_count`, COALESCE(`credit_count`,0) + @credit_count, @credit_note, @dateCreated FROM `users` WHERE `user_id` IN (SELECT `user_id` FROM `user_groups` WHERE `group_id` = @groupid);
          UPDATE `users` SET `credit_count` = COALESCE(`credit_count`,0) + @credit_count WHERE `user_id` IN (SELECT `user_id` FROM `user_groups` WHERE `group_id` = @groupid)';

    const ADD_GROUP_CREDITS_REPLENISHMENT = 'INSERT INTO `group_credit_replenishment_rule` (`group_id`, `type`, `amount`, `day_of_month`, `interval`, `date_created`) 
            VALUES (@groupid, @type, @amount, @day_of_month, @interval, UTC_TIMESTAMP())';

    const ADJUST_USER_CREDITS =
        'INSERT INTO `credit_log` (`user_id`, `original_credit_count`, `credit_count`, `credit_note`, `date_created`) 
            SELECT `user_id`, `credit_count`, COALESCE(`credit_count`,0) - @credit_count, @credit_note, @dateCreated FROM `users` WHERE `user_id` = @userid;
          UPDATE `users` SET `credit_count` = COALESCE(`credit_count`,0) - @credit_count WHERE `user_id` = @userid';

    const ADD_LAYOUT =
        'INSERT INTO `layouts` (`timezone`, `layout_type`, `date_created`) VALUES (@timezone, @layout_type, UTC_TIMESTAMP())';

    const ADD_LAYOUT_TIME =
        'INSERT INTO `time_blocks` (`layout_id`, `start_time`, `end_time`, `availability_code`, `label`, `day_of_week`)
		VALUES (@layoutid, @startTime, @endTime, @periodType, @label, @day_of_week)';

    const ADD_CUSTOM_LAYOUT_SLOT =
        'INSERT INTO `custom_time_blocks` (`start_time`, `end_time`, `layout_id`)
		VALUES (@startTime, @endTime, (select `layout_id` from `schedules` where `schedule_id` = @scheduleid))';

    const ADD_MONITOR_VIEW = 'INSERT INTO `monitor_views` (`monitor_view_name`, `public_id`, `serialized_settings`, `date_created`) VALUES (@name, @publicid, @serialized_settings, @dateCreated)';

    const ADD_OAUTH_PROVIDER = 'INSERT INTO `oauth_authentication_providers`
        (`public_id`, `provider_name`, `client_id`, `client_secret`, `url_authorize`, `url_access_token`, `url_user_details`, `access_token_grant`, `field_mappings`, `scope`, `date_created`)
        VALUES (@publicid, @provider_name, @client_id, @client_secret, @url_authorize, @url_access_token, @url_user_details, @access_token_grant, @field_mappings, @scope, @dateCreated)';

    const ADD_RESOURCE_MAP = 'INSERT INTO `resource_maps` 
            (`name`, `public_id`, `status_id`, `file_type`, `file_size`, `file_extension`, `date_created`) 
            VALUES (@name, @publicid, @statusid, @file_type, @file_size, @file_extension, @dateCreated)';

    const ADD_RESOURCE_MAP_RESOURCE = 'INSERT INTO `resource_map_resources` 
            (`resource_map_id`, `resource_id`, `public_id`, `coordinates`, `date_created`) 
            VALUES (@resource_map_id, @resourceid, @publicid, @coordinates, @dateCreated)';

    const ADD_QUOTA =
        'INSERT INTO `quotas` (`quota_limit`, `unit`, `duration`, `resource_id`, `group_id`, `schedule_id`, `enforced_time_start`, `enforced_time_end`, `enforced_days`, `scope`, `interval`, `date_created`, `stop_enforcement_minutes_prior`)
			VALUES (@limit, @unit, @duration, @resourceid, @groupid, @scheduleid, @startTime, @endTime, @enforcedDays, @scope, @interval, UTC_TIMESTAMP(), @stop_enforcement_minutes_prior)';

    const ADD_PAYMENT_GATEWAY_SETTING = 'INSERT INTO `payment_gateway_settings` (`gateway_type`, `setting_name`, `setting_value`, `date_created`) 
                                      VALUES (@gateway_type, @setting_name, @setting_value, UTC_TIMESTAMP())';

    const ADD_PAYMENT_TRANSACTION_LOG =
        'INSERT INTO `payment_transaction_log` (`user_id`, `status`, `invoice_number`, `transaction_id`, `subtotal_amount`, `tax_amount`, `total_amount`, `transaction_fee`, `currency`, `transaction_href`, `refund_href`, `date_created`, `gateway_date_created`, `gateway_name`, `payment_response`) 
          VALUES (@userid, @status, @invoice_number, @transaction_id, @total_amount, 0, @total_amount, @transaction_fee, @currency, @transaction_href, @refund_href, @date_created, @gateway_date_created, @gateway_name, @payment_response)';

    const ADD_PEAK_TIMES =
        'INSERT INTO `peak_times` (`schedule_id`, `all_day`, `start_time`, `end_time`, `every_day`, `peak_days`, `all_year`, `begin_month`, `begin_day`, `end_month`, `end_day`, `date_created`)
			VALUES (@scheduleid, @all_day, @start_time, @end_time, @every_day, @peak_days, @all_year, @begin_month, @begin_day, @end_month, @end_day, UTC_TIMESTAMP())';

    const ADD_PASSWORD_RESET_REQUEST = 'INSERT INTO `reset_password_requests` (`user_id`, `date_created`, `reset_token`) VALUES (@userid, @dateCreated, @reset_token)';

    const ADD_REFUND_TRANSACTION_LOG =
        'INSERT INTO `refund_transaction_log` (`payment_transaction_log_id`, `status`, `transaction_id`, `total_refund_amount`, `payment_refund_amount`, `fee_refund_amount`, `transaction_href`, `date_created`, `gateway_date_created`, `refund_response`) 
          VALUES (@payment_transaction_log_id, @status, @transaction_id, @total_refund_amount, @payment_refund_amount, @fee_refund_amount, @transaction_href, @date_created, @gateway_date_created, @refund_response)';

    const ADD_RESERVATION =
        'INSERT INTO `reservation_instances` (`start_date`, `end_date`, `reference_number`, `series_id`, `credit_count`)
        VALUES (@startDate, @endDate, @referenceNumber, @seriesid, @credit_count)';
//		SELECT @startDate, @endDate, @referenceNumber, @seriesid, @credit_count
//		WHERE NOT EXISTS(SELECT `ri`.`reference_number`
//		    FROM `reservation_instances` `ri`
//		    INNER JOIN `reservation_resources` `rr` on `ri`.`series_id` = `rr`.`series_id`
//		    INNER JOIN `reservation_series` `rs` ON `ri`.`series_id` = `ri`.`series_id`
//		    WHERE `ri`.`reference_number` <> @referenceNumber AND `rs`.`status_id` <> 2
//		    AND ((`ri`.`start_date` > @startDate AND `ri`.`start_date` < @endDate) OR
//					(`ri`.`end_date` > @startDate AND `ri`.`end_date` < @endDate) OR
//					(`ri`.`start_date` <= @startDate AND `ri`.`end_date` >= @endDate)) LIMIT 1)';

    const ADD_RESERVATION_ACCESSORY =
        'INSERT IGNORE INTO `reservation_accessories` (`series_id`, `accessory_id`, `quantity`)
		VALUES (@seriesid, @accessoryid, @quantity)';

    const ADD_RESERVATION_ATTACHMENT =
        'INSERT INTO `reservation_files` (`series_id`, `file_name`, `file_type`, `file_size`, `file_extension`)
		VALUES (@seriesid, @file_name, @file_type, @file_size, @file_extension)';

    const ADD_RESERVATION_COLOR_RULE =
        'INSERT INTO `reservation_color_rules` (`custom_attribute_id`, `attribute_type`, `required_value`, `comparison_type`, `color`, `priority`, `date_created`)
		VALUES (@custom_attribute_id, @attribute_type, @required_value, @comparison_type, @color, @priority, UTC_TIMESTAMP())';

    const ADD_RESERVATION_REMINDER =
        'INSERT INTO `reservation_reminders` (`series_id`, `minutes_prior`, `reminder_type`)
			VALUES (@seriesid, @minutes_prior, @reminder_type)';

    const ADD_RESERVATION_RESOURCE =
        'INSERT INTO `reservation_resources` (`series_id`, `resource_id`, `resource_level_id`)
		VALUES (@seriesid, @resourceid, @resourceLevelId)';

    const ADD_RESERVATION_SERIES =
        'INSERT INTO
        `reservation_series` (`date_created`, `title`, `description`, `allow_participation`, `allow_anon_participation`, `repeat_type`, `repeat_options`, `type_id`, `status_id`, `owner_id`, `terms_date_accepted`, `last_action_by`)
		VALUES (@dateCreated, @title, @description, @allow_participation, false, @repeatType, @repeatOptions, @typeid, @statusid, @userid, @terms_date_accepted, @last_action_by)';

    const ADD_RESERVATION_GUEST =
        'INSERT INTO `reservation_guests` (`reservation_instance_id`, `email`, `reservation_user_level`)
			VALUES (@reservationid, @email, @levelid)';

    const ADD_RESERVATION_USER =
        'INSERT IGNORE INTO `reservation_users` (`reservation_instance_id`, `user_id`, `reservation_user_level`)
		VALUES (@reservationid, @userid, @levelid)';

    const ADD_RESERVATION_WAITLIST =
        'INSERT INTO `reservation_waitlist_requests` (`user_id`, `start_date`, `end_date`, `resource_id`, `date_created`)
      VALUES (@userid, @startDate, @endDate, @resourceid, UTC_TIMESTAMP())';

    const ADD_RESERVATION_MEETING_LINK =
        'INSERT INTO `reservation_meeting_links` (`series_id`, `meeting_link_type`, `meeting_link_url`, `meeting_external_id`, `date_created`)
      VALUES (@seriesid, @meeting_link_type, @meeting_link_url, @meeting_external_id, UTC_TIMESTAMP())';

    const ADD_SAVED_REPORT =
        'INSERT INTO `saved_reports` (`report_name`, `user_id`, `date_created`, `report_details`)
			VALUES (@report_name, @userid, @dateCreated, @report_details)';

    const ADD_SCHEDULE =
        'INSERT INTO `schedules` (`name`, `isdefault`, `weekdaystart`, `daysvisible`, `layout_id`, `admin_group_id`, `date_created`)
		VALUES (@scheduleName, @scheduleIsDefault, @scheduleWeekdayStart, @scheduleDaysVisible, @layoutid, @admin_group_id, UTC_TIMESTAMP())';

    const ADD_TERMS_OF_SERVICE =
        'INSERT INTO `terms_of_service` (`terms_text`, `terms_url`, `terms_file`, `applicability`, `date_created`) 
      VALUES (@terms_text, @terms_url, @terms_file, @applicability, @dateCreated)';

    const ADD_USER_GROUP =
        'INSERT INTO `user_groups` (`user_id`, `group_id`, `date_created`)
		VALUES (@userid, @groupid, UTC_TIMESTAMP())';

    const ADD_USER_RESOURCE_PERMISSION =
        'INSERT IGNORE INTO `user_resource_permissions` (`user_id`, `resource_id`, `permission_type`, `date_created`)
		VALUES (@userid, @resourceid, @permission_type, UTC_TIMESTAMP())';

    const ADD_USER_TO_DEFAULT_GROUPS =
        'INSERT IGNORE INTO `user_groups` (`user_id`, `group_id`, `date_created`) SELECT @userid, `group_id`, UTC_TIMESTAMP() FROM `groups` WHERE `isdefault`=1';

    const ADD_USER_SESSION =
        'INSERT INTO `user_session` (`user_id`, `last_modified`, `session_token`, `user_session_value`)
		VALUES (@userid, @dateModified, @session_token, @user_session_value)';

    const ADD_USER_RESOURCE_FAVORITE =
        'INSERT IGNORE INTO `user_resource_favorites` (`user_id`, `resource_id`, `date_created`)
		VALUES (@userid, @resourceid, @dateCreated)';

    const ADD_USER_SMS_CONFIGURATION =
        'INSERT INTO `user_sms` (`user_id`, `confirmation_code`, `date_created`)
		VALUES (@userid, @confirmation_code, @dateCreated)';

    const ADD_USER_OAUTH = 'INSERT INTO `user_oauth` (`user_id`, `access_token`, `refresh_token`, `expires_at`, `provider_id`, `date_created`) 
        VALUES (@userid, @access_token, @refresh_token, @expires_at, @provider_id, @dateCreated)';

    const AUTO_ASSIGN_PERMISSIONS =
        'INSERT INTO `user_resource_permissions` (`user_id`, `resource_id`, `date_created`)
		SELECT @userid as `user_id`, `resource_id`, UTC_TIMESTAMP() as `date_created` FROM `resources` WHERE `autoassign`=1';

    const AUTO_ASSIGN_GUEST_PERMISSIONS =
        'INSERT INTO `user_resource_permissions` (`user_id`, `resource_id`, `date_created`)
		SELECT @userid as `user_id`, `resource_id`, UTC_TIMESTAMP() as `date_created`
		FROM `resources` WHERE `schedule_id` = @scheduleid';

    const AUTO_ASSIGN_RESOURCE_PERMISSIONS =
        'INSERT IGNORE INTO `user_resource_permissions` (`user_id`, `resource_id`, `date_created`)
			(
			SELECT
				`user_id`, @resourceid as `resource_id`, UTC_TIMESTAMP() as `date_created`
			FROM
				`users` `u`)';

    const AUTO_ASSIGN_CLEAR_RESOURCE_PERMISSIONS = 'DELETE FROM `user_resource_permissions` WHERE `resource_id` = @resourceid';

    const ADD_GROUP_RESERVATION_LIMITS = 'UPDATE `groups` SET `limit_on_reservation` = 1 WHERE `group_id` IN (@groupid)';

    const CLEAR_GROUP_RESERVATION_LIMITS = 'UPDATE `groups` SET `limit_on_reservation` = null';

    const CHECK_EMAIL = 'SELECT `user_id` FROM `users` WHERE `email` = @email';

    const CHECK_USERNAME =
        'SELECT `user_id` FROM `users` WHERE `username` = @username';

    const CHECK_USER_EXISTENCE =
        'SELECT *
		FROM `users`
		WHERE ( (`username` IS NOT NULL AND `username` = @username) OR (`email` IS NOT NULL AND `email` = @email) )';

    const CLEANUP_USER_SESSIONS =
        'DELETE FROM `user_session` WHERE utc_timestamp()>date_add(`last_modified`,interval 24 hour)';

    const COOKIE_LOGIN =
        'SELECT `user_id`, `remember_me_token`, `email`
		FROM `users` WHERE `user_id` = @userid';

    const DELETE_ACCESSORY = 'DELETE FROM `accessories` WHERE `accessory_id` = @accessoryid';

    const DELETE_ACCESSORY_RESOURCES = 'DELETE FROM `resource_accessories` WHERE `accessory_id` = @accessoryid';

    const DELETE_ATTRIBUTE = 'DELETE FROM `custom_attributes` WHERE `custom_attribute_id` = @custom_attribute_id';

    const DELETE_ATTRIBUTE_VALUES = 'DELETE FROM `custom_attribute_values` WHERE `custom_attribute_id` = @custom_attribute_id';

    const DELETE_ATTRIBUTE_ENTITY_VALUES = 'DELETE FROM `custom_attribute_values` WHERE `entity_id` = @entity_id';

    const DELETE_ATTRIBUTE_COLOR_RULES = 'DELETE FROM `reservation_color_rules` WHERE `custom_attribute_id` = @custom_attribute_id';

    const DELETE_ACCOUNT_ACTIVATION = 'DELETE FROM `account_activation` WHERE `activation_code` = @activation_code';

    const DELETE_ANNOUNCEMENT = 'DELETE FROM `announcements` WHERE `announcementid` = @announcementid';

    const DELETE_BLACKOUT_SERIES = 'DELETE `blackout_series` FROM `blackout_series`
		INNER JOIN `blackout_instances` ON `blackout_series`.`blackout_series_id` = `blackout_instances`.`blackout_series_id`
		WHERE `blackout_instance_id` = @blackout_instance_id';

    const DELETE_CUSTOM_LAYOUT_PERIOD = 'DELETE FROM `custom_time_blocks` 
      WHERE `start_time` = @startTime AND 
        `layout_id` = (select `layout_id` from `schedules` where `schedule_id` = @scheduleid)';

    const DELETE_BLACKOUT_INSTANCE = 'DELETE FROM `blackout_instances` WHERE `blackout_instance_id` = @blackout_instance_id';

    const DELETE_EMAIL_PREFERENCE =
        'DELETE FROM `user_email_preferences` WHERE `user_id` = @userid AND `event_category` = @event_category AND `event_type` = @event_type AND `notification_method` = @notification_method';

    const DELETE_GROUP = 'DELETE FROM `groups` WHERE `group_id` = @groupid';

    const DELETE_GROUP_RESOURCE_PERMISSION =
        'DELETE	FROM `group_resource_permissions` WHERE `group_id` = @groupid AND `resource_id` = @resourceid';

    const DELETE_GROUP_RESOURCE_PERMISSION_ALL =
        'DELETE	FROM `group_resource_permissions` WHERE `resource_id` = @resourceid';

    const DELETE_GROUP_ROLE = 'DELETE FROM `group_roles` WHERE `group_id` = @groupid AND `role_id` = @roleid';

    const DELETE_GROUP_CREDIT_REPLENISHMENT = 'DELETE FROM `group_credit_replenishment_rule` WHERE `group_id` = @groupid ';

    const DELETE_MONITOR_VIEW = 'DELETE FROM `monitor_views` WHERE `public_id` = @publicid';

    const DELETE_OAUTH_PROVIDER = 'DELETE FROM `oauth_authentication_providers` WHERE `provider_id` = @provider_id';

    const DELETE_ORPHAN_LAYOUTS = 'DELETE `l`.* FROM `layouts` `l` LEFT JOIN `schedules` `s` ON `l`.`layout_id` = `s`.`layout_id` WHERE `s`.`layout_id` IS NULL';

    const DELETE_PAYMENT_GATEWAY_SETTINGS = 'DELETE FROM `payment_gateway_settings` WHERE `gateway_type` = @gateway_type';

    const DELETE_PEAK_TIMES = 'DELETE FROM `peak_times` WHERE `schedule_id` = @scheduleid';

    const DELETE_PASSWORD_RESET_REQUEST = 'DELETE FROM `reset_password_requests` WHERE `user_id` = @userid';

    const DELETE_QUOTA = 'DELETE FROM `quotas` WHERE `quota_id` = @quotaid';

    const DELETE_RESERVATION_COLOR_RULE_COMMAND = 'DELETE FROM `reservation_color_rules` WHERE `reservation_color_rule_id` = @reservation_color_rule_id';

    const DELETE_RESERVATION_WAITLIST_COMMAND = 'DELETE FROM `reservation_waitlist_requests` WHERE `reservation_waitlist_request_id` = @reservation_waitlist_request_id';

    const DELETE_RESOURCE_COMMAND = 'DELETE FROM `resources` WHERE `resource_id` = @resourceid';

    const DELETE_RESOURCE_GROUP_COMMAND = 'DELETE FROM `resource_groups` WHERE `resource_group_id` = @resourcegroupid';

    const DELETE_RESOURCE_GROUP_ASSIGNMENT_COMMAND = 'DELETE FROM `resource_group_assignment` WHERE `resource_id` = @resourceid';

    const DELETE_RESOURCE_GROUP_ASSIGNMENT_FOR_GROUP_COMMAND = 'DELETE FROM `resource_group_assignment` WHERE `resource_group_id` = @resourcegroupid';

    const DELETE_RESOURCE_RESERVATIONS_COMMAND =
        'DELETE `s`.*
		FROM `reservation_series` `s`
		INNER JOIN `reservation_resources` `rs` ON `s`.`series_id` = `rs`.`series_id`
		WHERE `rs`.`resource_id` = @resourceid';

    const DELETE_RESOURCE_IMAGES = 'DELETE FROM `resource_images` WHERE `resource_id` = @resourceid';

    const DELETE_RESOURCE_RELATIONSHIP = 'DELETE FROM `resource_relationships` 
        WHERE (`resource_id` = @resourceid AND `related_resource_id` = @related_resource_id) OR (`resource_id` = @resourceid AND `related_resource_id` = @related_resource_id)';

    const DELETE_RESOURCE_STATUS_REASON_COMMAND = 'DELETE FROM `resource_status_reasons` WHERE `resource_status_reason_id` = @resource_status_reason_id';

    const DELETE_RESOURCE_TYPE_COMMAND = 'DELETE FROM `resource_types` WHERE `resource_type_id` = @resource_type_id';

    const DELETE_RESOURCE_MAP = 'DELETE FROM `resource_maps` WHERE `public_id` = @publicid';

    const DELETE_RESOURCE_MAP_RESOURCES = 'DELETE FROM `resource_map_resources` WHERE `resource_map_id` = @resource_map_id';

    const DELETE_SAVED_REPORT = 'DELETE FROM `saved_reports` WHERE `saved_report_id` = @report_id AND `user_id` = @userid';

    const DELETE_SAVED_REPORT_SCHEDULE = 'UPDATE `saved_reports` SET `report_schedule` = @report_schedule WHERE `saved_report_id` = @report_id';

    const DELETE_SCHEDULE = 'DELETE FROM `schedules` WHERE `schedule_id` = @scheduleid';

    const DELETE_SERIES = 'UPDATE `reservation_series`
		    SET `status_id` = @statusid,
			`last_modified` = @dateModified,
			`last_action_by` = @last_action_by,
			`delete_reason` = @delete_reason
		  WHERE `series_id` = @seriesid';

    const DELETE_SERIES_PERMANENT = 'DELETE FROM `reservation_series` WHERE `series_id` = @seriesid';

    const DELETE_TERMS_OF_SERVICE = 'DELETE FROM `terms_of_service`';

    const DELETE_USER = 'DELETE FROM `users` WHERE `user_id` = @userid';

    const DELETE_USER_GROUP = 'DELETE FROM `user_groups` WHERE `user_id` = @userid AND `group_id` = @groupid';

    const DELETE_USER_RESOURCE_PERMISSION =
        'DELETE	FROM `user_resource_permissions` WHERE `user_id` = @userid AND `resource_id` = @resourceid';

    const DELETE_USER_RESOURCE_PERMISSION_ALL =
        'DELETE	FROM `user_resource_permissions` WHERE `resource_id` = @resourceid';

    const DELETE_USER_SESSION =
        'DELETE	FROM `user_session` WHERE `session_token` = @session_token';

    const DELETE_USER_OAUTH = 'DELETE FROM `user_oauth` WHERE `user_id` = @userid AND `provider_id` = @provider_id';

    const DELETE_USER_RESOURCE_FAVORITE = 'DELETE FROM `user_resource_favorites` WHERE `user_id` = @userid AND `resource_id` = @resourceid';

    const LOG_CREDIT_ACTIVITY_COMMAND =
        'INSERT INTO `credit_log` (`user_id`, `original_credit_count`, `credit_count`, `credit_note`, `date_created`)
            VALUES (@userid, @original_credit_count, @credit_count, @credit_note, @dateCreated)';

    const LOGIN_USER =
        'SELECT * FROM `users` WHERE (`username` = @username OR `email` = @username)';

    const GET_ACCESSORY_BY_ID = 'SELECT * FROM `accessories` WHERE `accessory_id` = @accessoryid';

    const GET_ACCESSORY_RESOURCES = 'SELECT * FROM `resource_accessories` WHERE `accessory_id` = @accessoryid';

    const GET_ACCESSORY_LIST =
        'SELECT *, `rs`.`status_id` as `status_id`
		FROM `reservation_instances` `ri`
		INNER JOIN `reservation_series` `rs` ON `ri`.`series_id` = `rs`.`series_id`
		INNER JOIN `reservation_accessories` `ar` ON `ar`.`series_id` = `rs`.`series_id`
		INNER JOIN `accessories` `a` on `ar`.`accessory_id` = `a`.`accessory_id`
		WHERE
			(
				(`ri`.`start_date` >= @startDate AND `ri`.`start_date` <= @endDate)
				OR
				(`ri`.`end_date` >= @startDate AND `ri`.`end_date` <= @endDate)
				OR
				(`ri`.`start_date` <= @startDate AND `ri`.`end_date` >= @endDate)
			) AND
			`rs`.`status_id` <> 2 AND 
		    `ar`.`quantity` > 0
		ORDER BY
			`ri`.`start_date` ASC';

    const GET_ALL_ACCESSORIES =
        'SELECT `a`.*, `c`.`num_resources`,
			(SELECT GROUP_CONCAT(CONCAT(`ra`.`resource_id`, ",", COALESCE(`ra`.`minimum_quantity`,""), ",",  COALESCE(`ra`.`maximum_quantity`,"")) SEPARATOR "!sep!")
				FROM `resource_accessories` `ra` WHERE `ra`.`accessory_id` = `a`.`accessory_id`) as `resource_accessory_list`
 			FROM `accessories` `a`
			LEFT JOIN (
				SELECT `accessory_id`, COUNT(*) AS `num_resources`
				FROM `resource_accessories` `ra`
				GROUP BY `ra`.`accessory_id`
				) AS `c` ON `a`.`accessory_id` = `c`.`accessory_id`

 			ORDER BY `accessory_name`';

    const GET_ALL_ANNOUNCEMENTS = 'SELECT `a`.*, 
			(SELECT GROUP_CONCAT(`ag`.`group_id`) FROM `announcement_groups` `ag` WHERE `ag`.`announcementid` = `a`.`announcementid`) as `group_ids`,
			(SELECT GROUP_CONCAT(`ar`.`resource_id`) FROM `announcement_resources` `ar` WHERE `ar`.`announcementid` = `a`.`announcementid`) as `resource_ids`
			FROM `announcements` `a` ORDER BY `start_date`';

    const GET_ALL_APPLICATION_ADMINS = 'SELECT *
            FROM `users`
            WHERE `status_id` = @user_statusid AND
            (`user_id` IN (
                SELECT `user_id`
                FROM `user_groups` `ug`
                INNER JOIN `groups` `g` ON `ug`.`group_id` = `g`.`group_id`
                INNER JOIN `group_roles` `gr` ON `g`.`group_id` = `gr`.`group_id`
                INNER JOIN `roles` ON `roles`.`role_id` = `gr`.`role_id` AND `roles`.`role_level` = @role_level
              ) OR `email` IN (@email))
              GROUP BY `user_id`';

    const GET_ALL_CREDIT_LOGS = 'SELECT `cl`.*, `u`.`fname`, `u`.`lname`, `u`.`email` FROM `credit_log` `cl` 
            LEFT JOIN `users` `u` ON `cl`.`user_id` = `u`.`user_id` 
            WHERE (@userid = -1 or `cl`.`user_id` = @userid)
            ORDER BY `cl`.`date_created` DESC';

    const GET_ALL_GROUPS =
        'SELECT `g`.*, `admin_group`.`name` as `admin_group_name`,
			(SELECT GROUP_CONCAT(`gr`.`role_id`) FROM `group_roles` `gr` WHERE `gr`.`group_id` = `g`.`group_id`) as `group_role_list`
		FROM `groups` `g`
		LEFT JOIN `groups` `admin_group` ON `g`.`admin_group_id` = `admin_group`.`group_id`
		ORDER BY `g`.`name`';

    const GET_ALL_GROUPS_BY_ROLE =
        'SELECT `g`.*,
			(SELECT GROUP_CONCAT(`gr`.`role_id`) FROM `group_roles` `gr` WHERE `gr`.`group_id` = `g`.`group_id`) as `group_role_list`
		FROM `groups` `g`
		INNER JOIN `group_roles` `gr` ON `g`.`group_id` = `gr`.`group_id`
		INNER JOIN `roles` `r` ON `r`.`role_id` = `gr`.`role_id`
		WHERE `r`.`role_level` = @role_level
		ORDER BY `g`.`name`';

    const GET_ALL_GROUP_RESOURCE_PERMISSIONS = 'SELECT `grp`.*, `r`.`name`
        FROM `group_resource_permissions` `grp` 
        INNER JOIN `resources` `r` ON `grp`.`resource_id` = `r`.`resource_id`';

    const GET_ALL_GROUP_ADMINS =
        'SELECT `u`.* FROM `users` `u`
        INNER JOIN `user_groups` `ug` ON `u`.`user_id` = `ug`.`user_id`
        WHERE `status_id` = @user_statusid AND `ug`.`group_id` IN (
          SELECT `g`.`admin_group_id` FROM `user_groups` `ug`
          INNER JOIN `groups` `g` ON `ug`.`group_id` = `g`.`group_id`
          WHERE `ug`.`user_id` = @userid AND `g`.`admin_group_id` IS NOT NULL)';

    const GET_ALL_GROUP_USERS =
        'SELECT `u`.*, (SELECT GROUP_CONCAT(CONCAT(`cav`.`custom_attribute_id`, \'=\', `cav`.`attribute_value`) SEPARATOR "!sep!")
			FROM `custom_attribute_values` `cav` WHERE `cav`.`entity_id` = `u`.`user_id` AND `cav`.`attribute_category` = 2) as `attribute_list`
		FROM `users` `u`
		WHERE `u`.`user_id` IN (
		  SELECT DISTINCT (`ug`.`user_id`) FROM `user_groups` `ug`
		  INNER JOIN `groups` `g` ON `g`.`group_id` = `ug`.`group_id`
		  WHERE `g`.`group_id` IN (@groupid)
		  )
		AND (0 = @user_statusid OR `u`.`status_id` = @user_statusid)
		AND `u`.`api_only` = 0
		ORDER BY `u`.`lname`, `u`.`fname`';

    const GET_ALL_MONITOR_VIEWS = 'SELECT * FROM `monitor_views` ORDER BY `monitor_view_name` DESC';

    const GET_ALL_OAUTH_PROVIDERS = 'SELECT * FROM `oauth_authentication_providers` ORDER BY `provider_name` DESC';

    const GET_ALL_QUOTAS =
        'SELECT `q`.*, `r`.`name` as `resource_name`, `g`.`name` as `group_name`, `s`.`name` as `schedule_name`
		FROM `quotas` `q`
		LEFT JOIN `resources` `r` ON `r`.`resource_id` = `q`.`resource_id`
		LEFT JOIN `groups` `g` ON `g`.`group_id` = `q`.`group_id`
		LEFT JOIN `schedules` `s` ON `s`.`schedule_id` = `q`.`schedule_id`';

    const GET_ALL_REMINDERS = 'SELECT * FROM `reminders`';

    const GET_ALL_RESERVATION_WAITLIST_REQUESTS = 'SELECT * FROM `reservation_waitlist_requests`';

    const GET_ALL_RESOURCES =
        'SELECT `r`.*, `s`.`admin_group_id` as `s_admin_group_id`,
		(SELECT GROUP_CONCAT(CONCAT(`cav`.`custom_attribute_id`, \'=\', `cav`.`attribute_value`) SEPARATOR "!sep!")
						FROM `custom_attribute_values` `cav` WHERE `cav`.`entity_id` = `r`.`resource_id` AND `cav`.`attribute_category` = 4) as `attribute_list`,
		(SELECT GROUP_CONCAT(`rga`.`resource_group_id` SEPARATOR "!sep!") FROM `resource_group_assignment` `rga` WHERE `rga`.`resource_id` = `r`.`resource_id`) AS `group_list`,
		(SELECT GROUP_CONCAT(`ri`.`image_name` SEPARATOR "!sep!") FROM `resource_images` `ri` WHERE `ri`.`resource_id` = `r`.`resource_id`) AS `image_list`,
		(SELECT GROUP_CONCAT(CONCAT(`rr`.`resource_id`, "|", `rr`.`related_resource_id`, "|", `rr`.`relationship_type`) SEPARATOR "!sep!") FROM `resource_relationships` `rr` WHERE `rr`.`resource_id` = `r`.`resource_id` OR `rr`.`related_resource_id` = `r`.`resource_id`) AS `relationship_list`
        FROM `resources` as `r`
		INNER JOIN `schedules` as `s` ON `r`.`schedule_id` = `s`.`schedule_id`
		ORDER BY COALESCE(`r`.`sort_order`,0), `r`.`name`';

    const GET_ALL_RESOURCE_GROUPS = 'SELECT * FROM `resource_groups` ORDER BY `parent_id`, `resource_group_name`';

    const GET_ALL_RESOURCE_GROUP_ASSIGNMENTS = 'SELECT `r`.*, `a`.`resource_group_id`
		FROM `resource_group_assignment` as `a`
		INNER JOIN `resources` as `r` ON `r`.`resource_id` = `a`.`resource_id`
		WHERE (-1 = @scheduleid OR `r`.`schedule_id` = @scheduleid)
		ORDER BY COALESCE(`r`.`sort_order`,0), `r`.`name`';

    const GET_ALL_RESOURCE_ADMINS =
        'SELECT *
        FROM `users`
        WHERE `status_id` = @user_statusid AND
        `user_id` IN (
            SELECT `user_id`
            FROM `user_groups` `ug`
            INNER JOIN `groups` `g` ON `ug`.`group_id` = `g`.`group_id`
            INNER JOIN `group_roles` `gr` ON `g`.`group_id` = `gr`.`group_id`
            INNER JOIN `roles` ON `roles`.`role_id` = `gr`.`role_id` AND `roles`.`role_level` = @role_level
            INNER JOIN `resources` `r` ON `g`.`group_id` = `r`.`admin_group_id`
            WHERE `r`.`resource_id` = @resourceid
          )';

    const GET_ALL_SCHEDULE_ADMINS =
        'SELECT *
        FROM `users`
        WHERE `status_id` = @user_statusid AND
        `user_id` IN (
            SELECT `user_id`
            FROM `user_groups` `ug`
            INNER JOIN `groups` `g` ON `ug`.`group_id` = `g`.`group_id`
            INNER JOIN `group_roles` `gr` ON `g`.`group_id` = `gr`.`group_id`
            INNER JOIN `roles` ON `roles`.`role_id` = `gr`.`role_id` AND `roles`.`role_level` = @role_level
            INNER JOIN `schedules` `s` ON `g`.`group_id` = `s`.`admin_group_id`
            WHERE `s`.`schedule_id` = @scheduleid
          )';

    const GET_ALL_RESOURCE_STATUS_REASONS = 'SELECT * FROM `resource_status_reasons` ORDER BY `description`';

    const GET_ALL_RESOURCE_TYPES = 'SELECT *,
			(SELECT GROUP_CONCAT(CONCAT(`cav`.`custom_attribute_id`, \'=\', `cav`.`attribute_value`) SEPARATOR "!sep!")
							FROM `custom_attribute_values` `cav` INNER JOIN `custom_attribute_entities` `cae` on `cav`.`custom_attribute_id` = cae.custom_attribute_id
							WHERE `cav`.`entity_id` = `r`.`resource_type_id` AND `cav`.`attribute_category` = 5) as `attribute_list`
							FROM `resource_types` `r` ORDER BY `r`.`resource_type_name`';

    const GET_ALL_RESOURCE_MAPS = 'SELECT * FROM `resource_maps` WHERE (@status_id = -1 OR `status_id` = @status_id) ORDER BY `name` ASC';

    const GET_ALL_TRANSACTION_LOGS = 'SELECT `ptl`.*, `u`.`fname`, `u`.`lname`, `u`.`email`, SUM(`total_refund_amount`) as `refund_amount`
            FROM `payment_transaction_log` `ptl`
            LEFT JOIN `refund_transaction_log` `refunds` on `ptl`.`payment_transaction_log_id` = `refunds`.`payment_transaction_log_id`
            LEFT JOIN `users` `u` ON `ptl`.`user_id` = `u`.`user_id` 
            WHERE (@userid = -1 OR `ptl`.`user_id` = @userid)
            GROUP BY `ptl`.`payment_transaction_log_id`
            ORDER BY `date_created` DESC';

    const GET_ALL_SAVED_REPORTS = 'SELECT * FROM `saved_reports` WHERE `user_id` = @userid ORDER BY `report_name`, `date_created`';

    const GET_ALL_SAVED_REPORTS_SCHEDULED = 'SELECT * FROM `saved_reports` WHERE `report_schedule` IS NOT NULL';

    const GET_ALL_SCHEDULES = 'SELECT `s`.*, `l`.`timezone`, `l`.`layout_type` FROM `schedules` `s` INNER JOIN `layouts` `l` ON `s`.`layout_id` = `l`.`layout_id` ORDER BY `s`.`name`';

    const GET_ALL_USERS_BY_STATUS =
        'SELECT `u`.*,
			(SELECT GROUP_CONCAT(CONCAT(`p`.`name`, "=", `p`.`value`) SEPARATOR "!sep!")
						FROM `user_preferences` `p` WHERE `u`.`user_id` = `p`.`user_id`) as `preferences`,
			(SELECT GROUP_CONCAT(CONCAT(`cav`.`custom_attribute_id`, \'=\', `cav`.`attribute_value`) SEPARATOR "!sep!")
						FROM `custom_attribute_values` `cav` WHERE `cav`.`entity_id` = `u`.`user_id` AND `cav`.`attribute_category` = 2) as attribute_list,
            (SELECT GROUP_CONCAT(`ug`.`group_id` SEPARATOR "!sep!")
                        FROM `user_groups` `ug` WHERE `ug`.`user_id` = `u`.`user_id`) as `group_ids`
			FROM `users` `u`
			WHERE (0 = @user_statusid OR `status_id` = @user_statusid) ORDER BY `lname`, `fname`';

    const GET_ALL_USER_OAUTH = 'SELECT * FROM `user_oauth` WHERE `user_id` = @userid';

    const GET_ANNOUNCEMENT_BY_ID = 'SELECT `a`.*,
 		(SELECT GROUP_CONCAT(`ag`.`group_id`) FROM `announcement_groups` `ag` WHERE `ag`.`announcementid` = `a`.`announcementid`) as `group_ids`,
		(SELECT GROUP_CONCAT(`ar`.`resource_id`) FROM `announcement_resources` `ar` WHERE `ar`.`announcementid` = `a`.`announcementid`) as `resource_ids`
		FROM `announcements` `a` WHERE `a`.`announcementid` = @announcementid';

    const GET_ATTRIBUTES_BASE_QUERY = 'SELECT `a`.*,
				(SELECT GROUP_CONCAT(`e`.`entity_id` SEPARATOR "!sep!")
							FROM `custom_attribute_entities` `e` WHERE `e`.`custom_attribute_id` = `a`.`custom_attribute_id` ORDER BY `e`.`entity_id`) as `entity_ids`,
				(CASE
				WHEN `a`.`attribute_category` = 2 THEN (SELECT GROUP_CONCAT(CONCAT(`u`.`fname`, " ", `u`.`lname`) SEPARATOR "!sep!")
													FROM `users` `u` INNER JOIN `custom_attribute_entities` `e`
													WHERE `e`.`custom_attribute_id` = `a`.`custom_attribute_id` AND `u`.`user_id` = `e`.`entity_id` ORDER BY `e`.`entity_id`)
				WHEN `a`.`attribute_category` = 4 THEN (SELECT GROUP_CONCAT(`r`.`name` SEPARATOR "!sep!")
													FROM `resources` `r` INNER JOIN `custom_attribute_entities` `e`
													WHERE `e`.`custom_attribute_id` = `a`.`custom_attribute_id` AND `r`.`resource_id` = `e`.`entity_id` ORDER BY `e`.`entity_id`)
				WHEN `a`.`attribute_category` = 5  THEN (SELECT GROUP_CONCAT(`rt`.`resource_type_name` SEPARATOR "!sep!")
													FROM `resource_types` `rt` INNER JOIN `custom_attribute_entities` `e`
													WHERE `e`.`custom_attribute_id` = `a`.`custom_attribute_id` AND `rt`.`resource_type_id` = `e`.`entity_id` ORDER BY `e`.`entity_id`)
				ELSE null
				END) as `entity_descriptions`,
				(CASE
				WHEN `a`.`secondary_category` = 2 THEN (SELECT GROUP_CONCAT(CONCAT( `fname`, " ", `lname` ) SEPARATOR  "!sep!" ) FROM `users` WHERE FIND_IN_SET( `user_id`, `a`.`secondary_entity_ids` ))
				WHEN `a`.`secondary_category` = 4 THEN (SELECT GROUP_CONCAT(`name` SEPARATOR  "!sep!" ) FROM `resources` WHERE FIND_IN_SET( `resource_id`, `a`.`secondary_entity_ids` ))
				WHEN `a`.`secondary_category` = 5 THEN (SELECT GROUP_CONCAT(`resource_type_name` SEPARATOR  "!sep!" ) FROM `resource_types` WHERE FIND_IN_SET( `resource_type_id`, `a`.`secondary_entity_ids` ))
				ELSE null
				END) as `secondary_entity_descriptions`
				FROM `custom_attributes` as `a`';

    const GET_ATTRIBUTES_BY_CATEGORY_WHERE = ' WHERE `a`.`attribute_category` = @attribute_category ORDER BY `a`.`sort_order`, `a`.`display_label`';

    const GET_ATTRIBUTE_BY_ID_WHERE = '	WHERE `custom_attribute_id` = @custom_attribute_id';

    const GET_ATTRIBUTE_ALL_VALUES = 'SELECT * FROM `custom_attribute_values` WHERE `attribute_category` = @attribute_category';

    const GET_ATTRIBUTE_MULTIPLE_VALUES = 'SELECT *
		FROM `custom_attribute_values` WHERE `entity_id` IN (@entity_ids) AND `attribute_category` = @attribute_category';

    const GET_ATTRIBUTE_VALUES = 'SELECT `cav`.*, `ca`.`display_label`
		FROM `custom_attribute_values` `cav`
		INNER JOIN `custom_attributes` `ca` ON `ca`.`custom_attribute_id` = `cav`.`custom_attribute_id`
		WHERE `cav`.`attribute_category` = @attribute_category AND `cav`.`entity_id` = @entity_id';

    const GET_BLACKOUT_LIST =
        'SELECT *
		FROM `blackout_instances` `bi`
		INNER JOIN `blackout_series` `bs` ON `bi`.`blackout_series_id` = `bs`.`blackout_series_id`
		INNER JOIN `blackout_series_resources` `bsr` ON  `bi`.`blackout_series_id` = `bsr`.`blackout_series_id`
		INNER JOIN `resources` `r` on `bsr`.`resource_id` = `r`.`resource_id`
		INNER JOIN `users` `u` ON `u`.`user_id` = `bs`.`owner_id`
		WHERE
			(
				(`bi`.`start_date` >= @startDate AND `bi`.`start_date` <= @endDate)
				OR
				(`bi`.`end_date` >= @startDate AND `bi`.`end_date` <= @endDate)
				OR
				(`bi`.`start_date` <= @startDate AND `bi`.`end_date` >= @endDate)
			) AND
			(@scheduleid = -1 OR `r`.`schedule_id` = @scheduleid) AND (@all_resources = 1 OR `r`.`resource_id` IN(@resourceid))
		ORDER BY `bi`.`start_date` ASC';

    const GET_BLACKOUT_LIST_FULL =
        'SELECT `bi`.*, `r`.`resource_id`, `r`.`name`, `u`.*, `bs`.`description`, `bs`.`title`, `bs`.`repeat_type`, `bs`.`repeat_options`, `schedules`.`schedule_id`
					FROM `blackout_instances` `bi`
					INNER JOIN `blackout_series` `bs` ON `bi`.`blackout_series_id` = `bs`.`blackout_series_id`
					INNER JOIN `blackout_series_resources` `bsr` ON  `bi`.`blackout_series_id` = `bsr`.`blackout_series_id`
					INNER JOIN `resources` `r` on `bsr`.`resource_id` = `r`.`resource_id`
					INNER JOIN `schedules` on `r`.`schedule_id` = `schedules`.`schedule_id`
					INNER JOIN `users` `u` ON `u`.`user_id` = `bs`.`owner_id`
		ORDER BY `bi`.`start_date` ASC';

    const GET_BLACKOUT_INSTANCES = 'SELECT * FROM `blackout_instances` WHERE `blackout_series_id` = @blackout_series_id';

    const GET_BLACKOUT_SERIES_BY_BLACKOUT_ID = 'SELECT *
		FROM `blackout_series` `bs`
		INNER JOIN `blackout_instances` `bi` ON `bi`.`blackout_series_id` = `bs`.`blackout_series_id`
		WHERE `blackout_instance_id` = @blackout_instance_id';

    const GET_BLACKOUT_RESOURCES = 'SELECT `r`.*, `s`.`admin_group_id` as `s_admin_group_id`
		FROM `blackout_series_resources` `rr`
		INNER JOIN `resources` `r` ON `rr`.`resource_id` = `r`.`resource_id`
		INNER JOIN `schedules` `s` ON `r`.`schedule_id` = `s`.`schedule_id`
		WHERE `rr`.`blackout_series_id` = @blackout_series_id
		ORDER BY `r`.`name`';

    const GET_CUSTOM_LAYOUT = 'SELECT `l`.`timezone`, `ctb`.* 
        FROM `layouts` `l` 
        INNER JOIN `custom_time_blocks` `ctb` ON `l`.`layout_id` = `ctb`.`layout_id`
        INNER JOIN `schedules` `s` ON `s`.`layout_id` = `l`.`layout_id`
        WHERE `ctb`.`start_time` >= @startDate AND `ctb`.`end_time` <= @endDate AND `s`.`schedule_id` = @scheduleid
        ORDER BY `ctb`.`start_time`';

    const GET_CONFLICTING_RESERVATIONS_COMMAND = 'SELECT `ri`.*, `r`.*
        FROM `reservation_instances` `ri` 
        INNER JOIN `reservation_resources` `rr` on `ri`.`series_id` = `rr`.`series_id`
        INNER JOIN `resources` `r` ON `rr`.`resource_id` = `r`.`resource_id`
        INNER JOIN `reservation_series` `rs` ON `ri`.`series_id` = `rs`.`series_id` AND `rs`.`status_id` <> 2
        WHERE (
				(`ri`.`start_date` >= @startDate AND `ri`.`start_date` < @endDate)
				OR
				(`ri`.`end_date` > @startDate AND `ri`.`end_date` <= @endDate)
				OR
				(`ri`.`start_date` <= @startDate AND `ri`.`end_date` >= @endDate)
			) AND
			(@scheduleid = -1 OR `r`.`schedule_id` = @scheduleid) AND (@all_resources = -2 OR `r`.`resource_id` IN(@resourceids))
		ORDER BY `ri`.`start_date` ASC';

    const GET_CONFLICTING_BLACKOUTS_COMMAND =
        'SELECT *
		FROM `blackout_instances` `bi`
		INNER JOIN `blackout_series` `bs` ON `bi`.`blackout_series_id` = `bs`.`blackout_series_id`
		INNER JOIN `blackout_series_resources` `bsr` ON  `bi`.`blackout_series_id` = `bsr`.`blackout_series_id`
		INNER JOIN `resources` `r` on `bsr`.`resource_id` = `r`.`resource_id`
		WHERE
			(
				(`bi`.`start_date` >= @startDate AND `bi`.`start_date` < @endDate)
				OR
				(`bi`.`end_date` > @startDate AND `bi`.`end_date` <= @endDate)
				OR
				(`bi`.`start_date` <= @startDate AND `bi`.`end_date` >= @endDate)
			) AND
			(@scheduleid = -1 OR `r`.`schedule_id` = @scheduleid) AND (@all_resources = -2 OR `r`.`resource_id` IN(@resourceids))
		ORDER BY `bi`.`start_date` ASC';

    const GET_DASHBOARD_ANNOUNCEMENTS =
        'SELECT `a`.*, 
			(SELECT GROUP_CONCAT(`ag`.`group_id`) FROM `announcement_groups` `ag` WHERE `ag`.`announcementid` = `a`.`announcementid`) as `group_ids`,
			(SELECT GROUP_CONCAT(`ar`.`resource_id`) FROM `announcement_resources` `ar` WHERE `ar`.`announcementid` = `a`.`announcementid`) as `resource_ids`
			FROM `announcements` `a`
		WHERE ((`start_date` <= @current_date AND `end_date` >= @current_date) OR (`end_date` IS NULL)) AND (@display_page = -1 OR @display_page = `display_page`)
		ORDER BY `priority`, `start_date`, `end_date`';

    const GET_GROUP_BY_ID =
        'SELECT *, `g`.`group_id` as `group_id`
		FROM `groups` `g`
		LEFT JOIN `group_credit_replenishment_rule` `rule` ON `g`.`group_id` = `rule`.`group_id`
		WHERE `g`.`group_id` = @groupid';

    const GET_GROUPS_I_CAN_MANAGE = 'SELECT `g`.`group_id`, `g`.`name`
		FROM `groups` `g`
		INNER JOIN `groups` `a` ON `g`.`admin_group_id` = `a`.`group_id`
		INNER JOIN `user_groups` `ug` on `ug`.`group_id` = `a`.`group_id`
		WHERE `ug`.`user_id` = @userid';

    const GET_GROUP_RESOURCE_PERMISSIONS =
        'SELECT *
		FROM `group_resource_permissions`
		WHERE `group_id` = @groupid';

    const GET_GROUP_ROLES =
        'SELECT `r`.*
		FROM `roles` `r`
		INNER JOIN `group_roles` `gr` ON `r`.`role_id` = `gr`.`role_id`
		WHERE `gr`.`group_id` = @groupid';

    const GET_MONITOR_VIEW_BY_PUBLIC_ID = 'SELECT * FROM `monitor_views` WHERE `public_id` = @publicid';

    const GET_OAUTH_PROVIDER_BY_PUBLIC_ID = 'SELECT * FROM `oauth_authentication_providers` WHERE `public_id` = @publicid';

    const GET_ALL_GROUP_CREDIT_REPLENISHMENT_RULES = 'SELECT * from `group_credit_replenishment_rule`';

    const GET_REMINDER_NOTICES = 'SELECT DISTINCT
		`rs`.*,
		`ri`.*,
		`u`.`fname`, `u`.`lname`, `u`.`language`, `u`.`timezone`, `u`.`email`, `u`.`date_format`, `u`.`time_format`, `ru`.`user_id` AS `owner_id`,
		(SELECT GROUP_CONCAT(`r`.`name`  SEPARATOR "!sep!")
			FROM `reservation_resources` `rr`
			INNER JOIN `resources` `r` on `rr`.`resource_id` = `r`.`resource_id` WHERE `rr`.`series_id` = `rs`.`series_id`  ORDER BY `rr`.`resource_level_id` DESC, `r`.`sort_order` ASC) as `resource_names`
		FROM `reservation_instances` `ri`
		INNER JOIN `reservation_series` `rs` ON `ri`.`series_id` = `rs`.`series_id`
		INNER JOIN `reservation_reminders` `rr` on `ri`.`series_id` = `rr`.`series_id` 
		INNER JOIN `reservation_users` `ru` on `ru`.`reservation_instance_id` = `ri`.`reservation_instance_id`
		INNER JOIN `users` `u` on `ru`.`user_id` = `u`.`user_id` AND `u`.`status_id` = 1	
		WHERE `rs`.`status_id` <> 2 AND 
		((`reminder_type` = @reminder_type AND @reminder_type=0 AND date_sub(`start_date`,INTERVAL `rr`.`minutes_prior` MINUTE) = @current_date) OR (`reminder_type` = @reminder_type AND @reminder_type=1 AND date_sub(`end_date`,INTERVAL `rr`.`minutes_prior` MINUTE) = @current_date))';

    const GET_REMINDERS_BY_USER = 'SELECT * FROM `reminders` WHERE `user_id` = @user_id';

    const GET_REMINDERS_BY_REFNUMBER = 'SELECT * FROM `reminders` WHERE `refnumber` = @refnumber';

    const GET_RESOURCE_BY_CONTACT_INFO =
        'SELECT `r`.*, `s`.`admin_group_id` as `s_admin_group_id`
			FROM `resources` `r`
			INNER JOIN `schedules` `s` ON `r`.`schedule_id` = `s`.`schedule_id`
			WHERE `r`.`contact_info` = @contact_info';

    const GET_RESOURCE_BY_ID =
        'SELECT `r`.*, `s`.`admin_group_id` as `s_admin_group_id`,
				(SELECT GROUP_CONCAT( `ri`.`image_name` SEPARATOR  "!sep!" ) FROM `resource_images` `ri` WHERE `ri`.`resource_id` = `r`.`resource_id`) AS `image_list`
			FROM `resources` `r`
			INNER JOIN `schedules` `s` ON `r`.`schedule_id` = `s`.`schedule_id`
			WHERE `r`.`resource_id` = @resourceid';

    const GET_RESOURCE_BY_PUBLIC_ID =
        'SELECT `r`.*, `s`.`admin_group_id` as `s_admin_group_id`,
				(SELECT GROUP_CONCAT( `ri`.`image_name` SEPARATOR  "!sep!" ) FROM `resource_images` `ri` WHERE `ri`.`resource_id` = `r`.`resource_id`) AS `image_list`
			FROM `resources` `r`
			INNER JOIN `schedules` `s` ON `r`.`schedule_id` = `s`.`schedule_id`
			WHERE `r`.`public_id` = @publicid';

    const GET_RESOURCES_PUBLIC = 'SELECT * FROM `resources` WHERE `allow_calendar_subscription` = 1 AND `public_id` IS NOT NULL';

    const GET_RESOURCE_BY_NAME =
        'SELECT `r`.*, `s`.`admin_group_id` as `s_admin_group_id`,
				(SELECT GROUP_CONCAT( `ri`.`image_name` SEPARATOR  "!sep!" ) FROM `resource_images` `ri` WHERE `ri`.`resource_id` = `r`.`resource_id`) AS `image_list`
			FROM `resources` `r`
			INNER JOIN  `schedules` `s` ON `r`.`schedule_id` = `s`.`schedule_id`
			WHERE `r`.`name` = @resource_name';

    const GET_RESOURCE_GROUP_BY_ID = 'SELECT * FROM `resource_groups` WHERE `resource_group_id` = @resourcegroupid';

    const GET_RESOURCE_GROUP_ASSIGNMENTS = 'SELECT * FROM `resource_group_assignment` WHERE `resource_id` = @resourceid';

    const GET_RESOURCE_GROUP_BY_PUBLIC_ID = 'SELECT * FROM `resource_groups` WHERE `public_id` = @publicid';

    const GET_RESOURCE_TYPE_BY_ID = 'SELECT * FROM `resource_types` WHERE `resource_type_id` = @resource_type_id';

    const GET_RESOURCE_TYPE_BY_NAME = 'SELECT * FROM `resource_types` WHERE `resource_type_name` = @resource_type_name';

    const GET_RESOURCE_RELATIONSHIPS = 'SELECT * FROM `resource_relationships` WHERE `resource_id` = @resourceid OR `related_resource_id` = @resourceid';

    const GET_RESERVATION_BY_ID =
        'SELECT *
		FROM `reservation_instances` `r`
		INNER JOIN `reservation_series` `rs` ON `r`.`series_id` = `rs`.`series_id`
		WHERE
			`r`.`reservation_instance_id` = @reservationid AND
			`status_id` <> 2';

    const GET_RESERVATION_BY_REFERENCE_NUMBER =
        'SELECT *
		FROM `reservation_instances` `r`
		INNER JOIN `reservation_series` `rs` ON `r`.`series_id` = `rs`.`series_id`
		WHERE
			`reference_number` = @referenceNumber AND
			`status_id` <> 2';

    const GET_RESERVATION_FOR_EDITING =
        'SELECT `ri`.*, `rs`.*, `rr`.*, `u`.`user_id`, `u`.`fname`, `u`.`lname`, `u`.`email`, `u`.`phone`, `r`.`schedule_id`, `r`.`name`, `rs`.`status_id` as `status_id`, `r`.`checkin_limited_to_admins`, `rml`.`meeting_link_type`, `rml`.`meeting_link_url`
		FROM `reservation_instances` `ri`
		INNER JOIN `reservation_series` `rs` ON `rs`.`series_id` = `ri`.`series_id`
		INNER JOIN `users` `u` ON `u`.`user_id` = `rs`.`owner_id`
		INNER JOIN `reservation_resources` `rr` ON `rs`.`series_id` = `rr`.`series_id` AND `rr`.`resource_level_id` = @resourceLevelId
		INNER JOIN `resources` `r` ON `r`.`resource_id` = `rr`.`resource_id`
		LEFT JOIN `reservation_meeting_links` `rml` ON `rs`.`series_id` = `rml`.`series_id`
		WHERE
			`reference_number` = @referenceNumber AND
			`rs`.`status_id` <> 2';

    const GET_RESERVATION_LIST_TEMPLATE =
        'SELECT
				[SELECT_TOKEN]
			FROM `reservation_instances` `ri`
			INNER JOIN `reservation_series` `rs` ON `rs`.`series_id` = `ri`.`series_id`
			INNER JOIN `reservation_users` `ru` ON `ru`.`reservation_instance_id` = `ri`.`reservation_instance_id`
			INNER JOIN `users` ON `users`.`user_id` = `rs`.`owner_id`
			INNER JOIN `users` AS `owner` ON `owner`.`user_id` = `rs`.`owner_id`
			INNER JOIN `reservation_resources` `rr` ON `rs`.`series_id` = `rr`.`series_id`
			INNER JOIN `resources` ON `rr`.`resource_id` = `resources`.`resource_id`
			INNER JOIN `schedules` ON `resources`.`schedule_id` = `schedules`.`schedule_id`
			LEFT JOIN `reservation_reminders` AS `start_reminder` ON `start_reminder`.`series_id` = `rs`.`series_id` AND `start_reminder`.`reminder_type` = 0
			LEFT JOIN `reservation_reminders` AS `end_reminder` ON `end_reminder`.`series_id` = `rs`.`series_id` AND `end_reminder`.`reminder_type` = 1
			LEFT JOIN `users` AS `approver` ON `rs`.`approved_by` = `approver`.`user_id`
			[JOIN_TOKEN]
			WHERE `rs`.`status_id` <> 2
			[AND_TOKEN]
			ORDER BY `ri`.`start_date` ASC, `resources`.`sort_order` ASC';

    const GET_RESERVATION_ACCESSORIES =
        'SELECT *
		FROM `reservation_accessories` `ra`
		INNER JOIN `accessories` `a` ON `ra`.`accessory_id` = `a`.`accessory_id`
		WHERE `ra`.`series_id` = @seriesid AND `ra`.`quantity` > 0';

    const GET_RESERVATION_ATTACHMENT = 'SELECT * FROM `reservation_files` WHERE `file_id` = @file_id';

    const GET_RESERVATION_ATTACHMENTS_FOR_SERIES = 'SELECT * FROM `reservation_files` WHERE `series_id` = @seriesid';

    const GET_RESERVATION_GUESTS =
        'SELECT	`rg`.*
		FROM `reservation_guests` `rg`
		WHERE `reservation_instance_id` = @reservationid';

    const GET_RESERVATION_COLOR_RULES = 'SELECT * FROM `reservation_color_rules` `r`
		INNER JOIN `custom_attributes` `ca` ON `ca`.`custom_attribute_id` = `r`.`custom_attribute_id`
		ORDER BY `r`.`priority` ASC, `r`.`date_created` ASC';

    const GET_RESERVATION_COLOR_RULE = 'SELECT `r`.*, `ca`.`custom_attribute_id`, `ca`.`display_type`, `ca`.`display_label` FROM `reservation_color_rules` `r`
		INNER JOIN `custom_attributes` `ca` ON `ca`.`custom_attribute_id` = `r`.`custom_attribute_id`
		WHERE `reservation_color_rule_id` = @reservation_color_rule_id';

    const GET_RESERVATION_PARTICIPANTS =
        'SELECT
			`u`.`user_id`,
			`u`.`fname`,
			`u`.`lname`,
			`u`.`email`,
			`ru`.*
		FROM `reservation_users` `ru`
		INNER JOIN `users` `u` ON `ru`.`user_id` = `u`.`user_id`
		WHERE `reservation_instance_id` = @reservationid';

    const GET_RESERVATION_REMINDERS = 'SELECT * FROM `reservation_reminders` WHERE `series_id` = @seriesid';

    const GET_RESERVATION_REPEAT_DATES = 'SELECT `start_date` FROM `reservation_instances` WHERE `series_id` = @seriesid';

    const GET_RESERVATION_RESOURCES =
        'SELECT `r`.*, `rr`.`resource_level_id`, `s`.`admin_group_id` as `s_admin_group_id`
		FROM `reservation_resources` `rr`
		INNER JOIN `resources` `r` ON `rr`.`resource_id` = `r`.`resource_id`
		INNER JOIN `schedules` `s` ON `r`.`schedule_id` = `s`.`schedule_id`
		WHERE `rr`.`series_id` = @seriesid
		ORDER BY `resource_level_id`, `r`.`name`';

    const GET_RESERVATION_SERIES_GUESTS =
        'SELECT `rg`.*, `ri`.*
			FROM `reservation_guests` `rg`
			INNER JOIN `reservation_instances` `ri` ON `rg`.`reservation_instance_id` = `ri`.`reservation_instance_id`
			WHERE `series_id` = @seriesid';

    const GET_RESERVATION_SERIES_INSTANCES =
        'SELECT *
		FROM `reservation_instances`
		WHERE `series_id` = @seriesid';

    const GET_RESERVATION_SERIES_PARTICIPANTS =
        'SELECT `ru`.*, `ri`.*
		FROM `reservation_users` `ru`
		INNER JOIN `reservation_instances` `ri` ON `ru`.`reservation_instance_id` = `ri`.`reservation_instance_id`
		WHERE `series_id` = @seriesid';

    const GET_RESERVATION_WAITLIST_REQUEST = 'SELECT * FROM `reservation_waitlist_requests` WHERE `reservation_waitlist_request_id` = @reservation_waitlist_request_id';

    const GET_RESERVATION_WAITLIST_REQUEST_FOR_USER = 'SELECT * FROM `reservation_waitlist_requests` 
            WHERE `user_id` = @userid AND `resource_id` IN (@resourceids) AND ((`start_date` BETWEEN @startDate AND @endDate) OR (`end_date` BETWEEN @startDate AND @endDate))';

    const GET_RESERVATION_WAITLIST_UPCOMING_REQUEST_FOR_USER = 'SELECT * FROM `reservation_waitlist_requests` 
            WHERE `user_id` = @userid AND `start_date` > @startDate ORDER BY `start_date` ASC';

    const GET_RESERVATION_WAITLIST_REQUESTS = 'SELECT * FROM `reservation_waitlist_requests` 
            WHERE `resource_id` IN (@resourceids) AND ((`start_date` BETWEEN @startDate AND @endDate) OR (`end_date` BETWEEN @startDate AND @endDate))';

    const GET_RESERVATION_WAITLIST_SEARCH = 'SELECT `w`.*, `u`.`fname`, `u`.`lname`, `u`.`email`, `r`.`name` FROM `reservation_waitlist_requests` `w`
INNER JOIN `resources` `r` ON `w`.`resource_id` = `r`.`resource_id`
INNER JOIN `schedules` `s` ON `s`.`schedule_id` = `r`.`schedule_id`
INNER JOIN `users` `u` ON `w`.`user_id` = `u`.`user_id`
WHERE (@userid = -1 OR `w`.`user_id` = @userid) 
  AND (@resourceid = -1 OR `w`.`resource_id` IN (@resourceid))
  AND (@scheduleid = -1 OR `s`.`schedule_id` = @scheduleid)
  AND (`w`.`start_date` >= @startDate)
  AND (`w`.`end_date` <= @endDate)';

    const GET_RESERVATION_MEETING_LINK = 'SELECT * FROM `reservation_meeting_links` WHERE `series_id` = @seriesid';

    const GET_SCHEDULE_TIME_BLOCK_GROUPS =
        'SELECT
			`tb`.`label`,
			`tb`.`end_label`,
			`tb`.`start_time`,
			`tb`.`end_time`,
			`tb`.`availability_code`,
			`tb`.`day_of_week`,
			`l`.`timezone`,
			`l`.`layout_type`
		FROM
		`layouts` `l` 
		INNER JOIN `schedules` `s` ON `l`.`layout_id` = `s`.`layout_id`
		LEFT JOIN `time_blocks` `tb` ON `tb`.`layout_id` = `l`.`layout_id`
		WHERE
			`s`.`schedule_id` = @scheduleid
		ORDER BY `tb`.`start_time`';

    const GET_PEAK_TIMES = 'SELECT * FROM `peak_times` WHERE `schedule_id` = @scheduleid';

    const GET_PASSWORD_RESET_REQUEST = 'SELECT * FROM `reset_password_requests` WHERE `reset_token` = @reset_token';

    const GET_QUOTA_BY_ID = 'SELECT * FROM `quotas` WHERE `quota_id` = @quotaid';

    const GET_PAYMENT_CONFIGURATION = 'SELECT * FROM `payment_configuration`';

    const GET_PAYMENT_GATEWAY_SETTINGS = 'SELECT * FROM `payment_gateway_settings` WHERE `gateway_type` = @gateway_type';

    const GET_SAVED_REPORT = 'SELECT * FROM `saved_reports` WHERE `saved_report_id` = @report_id AND `user_id` = @userid';

    const GET_SCHEDULE_BY_ID =
        'SELECT * FROM `schedules` `s`
		INNER JOIN `layouts` `l` ON `s`.`layout_id` = `l`.`layout_id`
		WHERE `schedule_id` = @scheduleid';

    const GET_SCHEDULE_BY_PUBLIC_ID =
        'SELECT * FROM `schedules` `s`
        INNER JOIN `layouts` `l` ON `s`.`layout_id` = `l`.`layout_id`
        WHERE `public_id` = @publicid';

    const GET_SCHEDULE_BY_DEFAULT =
        'SELECT * FROM `schedules` `s`
        INNER JOIN `layouts` `l` ON `s`.`layout_id` = `l`.`layout_id`
        WHERE `isdefault` = 1';

    const GET_SCHEDULE_RESOURCES =
        'SELECT `r`.*, `s`.`admin_group_id` as `s_admin_group_id` FROM  `resources` as `r`
		INNER JOIN `schedules` as `s` ON `r`.`schedule_id` = `s`.`schedule_id`
		WHERE (-1 = @scheduleid OR `r`.`schedule_id` = @scheduleid) AND
			`r`.`status_id` <> 0
		ORDER BY COALESCE(`r`.`sort_order`,0), `r`.`name`';

    const GET_SCHEDULES_PUBLIC = 'SELECT * FROM `schedules` WHERE `allow_calendar_subscription` = 1 AND `public_id` IS NOT NULL';

    const GET_TRANSACTION_LOG =
        'SELECT `ptl`.*, SUM(`total_refund_amount`) as `refund_amount`
        FROM `payment_transaction_log` `ptl`
        LEFT JOIN `refund_transaction_log` `refunds` on `ptl`.`payment_transaction_log_id` = `refunds`.`payment_transaction_log_id`
        WHERE `ptl`.`payment_transaction_log_id` = @payment_transaction_log_id
        GROUP BY `ptl`.`payment_transaction_log_id`';

    const GET_TERMS_OF_SERVICE = 'SELECT * FROM `terms_of_service`';

    const GET_USERID_BY_ACTIVATION_CODE =
        'SELECT `a`.`user_id` FROM `account_activation` `a`
			INNER JOIN `users` `u` ON `u`.`user_id` = `a`.`user_id`
			WHERE `activation_code` = @activation_code AND `u`.`status_id` = @statusid';

    const GET_USER_BY_ID = 'SELECT * FROM `users` WHERE `user_id` = @userid';

    const GET_USER_BY_PUBLIC_ID = 'SELECT * FROM `users` WHERE `public_id` = @publicid';

    const GET_USER_COUNT = 'SELECT COUNT(*) as `count` FROM `users` WHERE `status_id` = @user_statusid';

    const GET_USER_EMAIL_PREFERENCES = 'SELECT * FROM `user_email_preferences` WHERE `user_id` = @userid';

    const GET_USER_GROUPS =
        'SELECT `g`.*, `r`.`role_level`
		FROM `user_groups` `ug`
		INNER JOIN `groups` `g` ON `ug`.`group_id` = `g`.`group_id`
		LEFT JOIN `group_roles` `gr` ON `ug`.`group_id` = `gr`.`group_id`
		LEFT JOIN `roles` `r` ON `gr`.`role_id` = `r`.`role_id`
		WHERE `user_id` = @userid AND (@role_null is null OR `r`.`role_level` IN (@role_level) )';

    const GET_USER_RESOURCE_PERMISSIONS =
        'SELECT
			`urp`.`user_id`, `urp`.`permission_type`, `r`.`resource_id`, `r`.`name`
		FROM
			`user_resource_permissions` `urp`, `resources` `r`
		WHERE
			`urp`.`user_id` = @userid AND `r`.`resource_id` = `urp`.`resource_id`';

    const GET_USER_GROUP_RESOURCE_PERMISSIONS =
        'SELECT
			`grp`.`group_id`, `r`.`resource_id`, `r`.`name`, `grp`.`permission_type`
		FROM
			`group_resource_permissions` `grp`, `resources` `r`, `user_groups` `ug`
		WHERE
			`ug`.`user_id` = @userid AND `ug`.`group_id` = `grp`.`group_id` AND `grp`.`resource_id` = `r`.`resource_id`';

    const GET_USER_ADMIN_GROUP_RESOURCE_PERMISSIONS =
        'SELECT `r`.`resource_id`, `r`.`name` FROM `resources` `r`
		WHERE `r`.`schedule_id` IN (SELECT `s`.`schedule_id` FROM `schedules` `s`
			INNER JOIN `groups` `g` ON `g`.`group_id` = `s`.`admin_group_id`
			INNER JOIN `user_groups` `ug` on `ug`.`group_id` = `g`.`group_id`
			WHERE `ug`.`user_id` = @userid)
		OR `r`.`resource_id` IN (SELECT `r2`.`resource_id` FROM `resources` `r2`
			INNER JOIN `groups` `g` ON `g`.`group_id` = `r2`.`admin_group_id`
			INNER JOIN `user_groups` `ug` on `ug`.`group_id` = `g`.`group_id`
			WHERE `ug`.`user_id` = @userid)';

    const GET_USER_PREFERENCE = 'SELECT `value` FROM `user_preferences` WHERE `user_id` = @userid AND `name` = @name';

    const GET_USER_PREFERENCES = 'SELECT `name`, `value` FROM `user_preferences` WHERE `user_id` = @userid';

    const GET_USER_ROLES =
        'SELECT
			`user_id`, `user_level`
		FROM
			`roles` `r`
		INNER JOIN
			`user_roles` `ur` on `r`.`role_id` = `ur`.`role_id`
		WHERE
			`ur`.`user_id` = @userid';

    const GET_USER_SESSION_BY_SESSION_TOKEN = 'SELECT * FROM `user_session` WHERE `session_token` = @session_token';

    const GET_USER_SESSION_BY_USERID = 'SELECT * FROM `user_session` WHERE `user_id` = @userid';

    const GET_USER_SMS_CONFIGURATION = 'SELECT * FROM `user_sms` WHERE `user_id` = @userid ORDER BY `date_created` DESC LIMIT 1';

    const GET_USER_MFA_SETTINGS = 'SELECT * FROM `users` WHERE `user_id` = @userid';

    const GET_USER_RESOURCE_FAVORITES = 'SELECT * FROM `resources` 
        WHERE `status_id` = @statusid AND `resource_id` IN (SELECT `resource_id` FROM `user_resource_favorites` WHERE `user_id` = @userid)';

    const GET_USER_OAUTH = 'SELECT * FROM `user_oauth` WHERE `user_id` = @userid AND `provider_id` = @provider_id';

    const GET_VERSION = 'SELECT * FROM `dbversion` order by `version_number` desc limit 0,1';

    const GET_RESOURCE_GROUP_PERMISSION = 'SELECT
				`g`.*, `grp`.`permission_type`,
                (SELECT GROUP_CONCAT(`gr`.`role_id`) FROM `group_roles` `gr` WHERE `gr`.`group_id` = `g`.`group_id`) as `group_role_list`
			FROM
				`group_resource_permissions` `grp`, `resources` `r`, `groups` `g`
			WHERE
				`r`.`resource_id` = @resourceid AND `r`.`resource_id` = `grp`.`resource_id` AND `g`.`group_id` = `grp`.`group_id`';

    const GET_RESOURCE_MAP_BY_PUBLIC_ID = 'SELECT * FROM `resource_maps` WHERE `public_id` = @publicid';

    const GET_RESOURCE_MAP_RESOURCES = 'SELECT * FROM `resource_map_resources` WHERE `resource_map_id` = @resource_map_id';

    const GET_RESOURCE_USER_PERMISSION = 'SELECT
				`u`.*, `urp`.`permission_type`
			FROM
				`user_resource_permissions` `urp`, `resources` `r`, `users` `u`
			WHERE
				`r`.`resource_id` = @resourceid AND `r`.`resource_id` = `urp`.`resource_id` AND `u`.`user_id` = `urp`.`user_id` AND `u`.`status_id` = @user_statusid';

    const GET_RESOURCES_RECENTLY_USED = 'SELECT 
				`last_used`, `r`.*, `s`.`admin_group_id` AS `s_admin_group_id`
			FROM
				`resources` `r`
					INNER JOIN
				`schedules` `s` ON `r`.`schedule_id` = `s`.`schedule_id`
					LEFT JOIN
				(SELECT 
					`r`.`resource_id`, MAX(`rs`.`date_created`) AS `last_used`
				FROM
					`reservation_series` `rs`
				INNER JOIN `reservation_resources` `rr` ON `rs`.`series_id` = `rr`.`series_id`
				INNER JOIN `resources` `r` ON `r`.`resource_id` = `rr`.`resource_id`
				WHERE
					`owner_id` = @userid AND `rs`.`status_id` <> 2
						AND `rs`.`date_created` > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 90 DAY)
				GROUP BY `r`.`resource_id`) `recently_used` ON `r`.`resource_id` = `recently_used`.`resource_id`
			WHERE
				`r`.`status_id` = @status_id
			ORDER BY `last_used` DESC, `r`.`sort_order` ASC, `r`.`name` ASC
			LIMIT 10';

    const GET_RESOURCE_USER_GROUP_PERMISSION = 'SELECT `u`.*, `urp`.`permission_type`
			FROM
				`user_resource_permissions` `urp`, `resources` `r`, `users` `u`
			WHERE
				`r`.`resource_id` = @resourceid AND `r`.`resource_id` = `urp`.`resource_id` AND `u`.`user_id` = `urp`.`user_id` AND `u`.`status_id` = @user_statusid
		UNION 
			SELECT `u`.*, `grp`.`permission_type`
			FROM `users` `u` 
			INNER JOIN `user_groups` `ug` on `u`.`user_id` = `ug`.`user_id` 
			INNER JOIN `group_resource_permissions` `grp` on `ug`.`group_id` = `grp`.`group_id`
			WHERE `ug`.`group_id` IN (
				SELECT
					`g`.`group_id`
				FROM
					`group_resource_permissions` `grp`, `resources` `r`, `groups` `g`
				WHERE
					`r`.`resource_id` = @resourceid AND `r`.`resource_id` = `grp`.`resource_id` AND `g`.`group_id` = `grp`.`group_id`) 
			AND `u`.`status_id` = @user_statusid';

    const MIGRATE_PASSWORD =
        'UPDATE
			`users`
		SET
			`password` = @password_value, `salt` = null, `password_hash_version` = @password_hash_version
		WHERE
			`user_id` = @userid';

    const REGISTER_USER =
        'INSERT INTO
			`users` (`email`, `password`, `fname`, `lname`, `phone`, `organization`, `position`, `username`, `password_hash_version`, `timezone`, `language`, `homepageid`, `status_id`, `date_created`, `public_id`, `default_schedule_id`, `terms_date_accepted`, `api_only`, `login_token`, `phone_country_code`)
		VALUES
			(@email, @password_value, @fname, @lname, @phone_number, @organization, @position, @username, @password_hash_version, @timezone, @language, @homepageid, @user_statusid, @dateCreated, @publicid, @scheduleid, @terms_date_accepted, @api_only, @login_token, @phone_country_code)';

    const REMOVE_ATTRIBUTE_ENTITY =
        'DELETE FROM `custom_attribute_entities` WHERE `custom_attribute_id` = @custom_attribute_id AND `entity_id` = @entity_id';

    const REMOVE_ATTRIBUTE_VALUE =
        'DELETE FROM `custom_attribute_values` WHERE `custom_attribute_id` = @custom_attribute_id AND `entity_id` = @entity_id';

    const DELETE_REMINDER = 'DELETE FROM `reminders` WHERE `reminder_id` = @reminder_id';

    const DELETE_REMINDER_BY_USER = 'DELETE FROM `reminders` WHERE `user_id` = @user_id';

    const DELETE_REMINDER_BY_REFNUMBER = 'DELETE FROM `reminders` WHERE `refnumber` = @refnumber';

    const REMOVE_RESERVATION_ACCESSORY =
        'DELETE FROM `reservation_accessories` WHERE `accessory_id` = @accessoryid AND `series_id` = @seriesid';

    const REMOVE_RESERVATION_ATTACHMENT =
        'DELETE FROM `reservation_files` WHERE `file_id` = @file_id';

    const REMOVE_RESERVATION_INSTANCE =
        'DELETE FROM `reservation_instances` WHERE `reference_number` = @referenceNumber';

    const REMOVE_RESERVATION_GUEST =
        'DELETE FROM `reservation_guests` WHERE `reservation_instance_id` = @reservationid AND `email` = @email';

    const REMOVE_RESERVATION_REMINDER =
        'DELETE FROM `reservation_reminders` WHERE `series_id` = @seriesid AND `reminder_type` = @reminder_type';

    const REMOVE_RESERVATION_RESOURCE =
        'DELETE FROM `reservation_resources` WHERE `series_id` = @seriesid AND `resource_id` = @resourceid';

    const REMOVE_RESERVATION_USER =
        'DELETE FROM `reservation_users` WHERE `reservation_instance_id` = @reservationid AND `user_id` = @userid';

    const REMOVE_RESERVATION_USERS =
        'DELETE FROM `reservation_users` WHERE `reservation_instance_id` = @reservationid AND `reservation_user_level` = @levelid';

    const REMOVE_RESERVATION_MEETING_LINK = 'DELETE FROM `reservation_meeting_links` WHERE `series_id` = @seriesid';

    const REMOVE_RESOURCE_FROM_GROUP = 'DELETE FROM `resource_group_assignment` WHERE `resource_group_id` = @resourcegroupid AND `resource_id` = @resourceid';

    const ADD_RESOURCE =
        'INSERT INTO
			`resources` (`name`, `location`, `contact_info`, `description`, `notes`, `status_id`, `min_duration`, `min_increment`,
					   `max_duration`, `unit_cost`, `autoassign`, `requires_approval`, `allow_multiday_reservations`,
					   `max_participants`, `min_notice_time_add`, `max_notice_time`, `schedule_id`, `admin_group_id`, `date_created`, `public_id`)
		VALUES
			(@resource_name, @location, @contact_info, @description, @resource_notes, @status_id, @min_duration, @min_increment,
			 @max_duration, @unit_cost, @autoassign, @requires_approval, @allow_multiday_reservations,
		     @max_participants, @min_notice_time_add, @max_notice_time, @scheduleid, @admin_group_id, @dateCreated, @public_id)';

    const ADD_RESOURCE_GROUP = 'INSERT INTO `resource_groups` (`resource_group_name`, `parent_id`, `date_created`) VALUES (@groupname, @resourcegroupid, UTC_TIMESTAMP())';

    const ADD_RESOURCE_STATUS_REASON = 'INSERT INTO `resource_status_reasons` (`status_id`, `description`) VALUES (@status_id, @description)';

    const ADD_RESOURCE_TO_GROUP = 'INSERT IGNORE INTO
			`resource_group_assignment` (`resource_group_id`, `resource_id`, `date_created`)
			VALUES (@resourcegroupid, @resourceid, UTC_TIMESTAMP())';

    const ADD_RESOURCE_TYPE = 'INSERT INTO `resource_types` (`resource_type_name`, `resource_type_description`, `date_created`) VALUES (@resource_type_name, @resource_type_description, UTC_TIMESTAMP())';

    const ADD_RESOURCE_IMAGE = 'INSERT INTO `resource_images` (`resource_id`, `image_name`, `date_created`) VALUES (@resourceid, @imageName, UTC_TIMESTAMP())';

    const ADD_RESOURCE_RELATIONSHIP = 'INSERT INTO `resource_relationships` (`resource_id`, `related_resource_id`, `relationship_type`, `date_created`) VALUES (@resourceid, @related_resource_id, @relationship_type, UTC_TIMESTAMP())';

    const ADD_USER_PREFERENCE = 'INSERT INTO `user_preferences` (`user_id`, `name`, `value`) VALUES (@userid, @name, @value)';

    const DELETE_ALL_USER_PREFERENCES = 'DELETE FROM `user_preferences` WHERE `user_id` = @userid';

    const SET_DEFAULT_SCHEDULE =
        'UPDATE `schedules`
		SET `isdefault` = 0, `last_modified` = UTC_TIMESTAMP()
		WHERE `schedule_id` <> @scheduleid';

    const UPDATE_ACCESSORY =
        'UPDATE `accessories`
		SET `accessory_name` = @accessoryname, `accessory_quantity` = @quantity, `credit_count` = @credit_count, `peak_credit_count` = @peak_credit_count, `credit_applicability` = @credit_applicability, `credits_charged_all_slots` = @credits_charged_all_slots, `last_modified` = UTC_TIMESTAMP(), `public_id` = @publicid
		WHERE `accessory_id` = @accessoryid';

    const UPDATE_ANNOUNCEMENT =
        'UPDATE `announcements`
		SET `announcement_text` = @text, `priority` = @priority, `start_date` = @startDate, `end_date` = @endDate, `last_modified` = UTC_TIMESTAMP()
		WHERE `announcementid` = @announcementid';

    const UPDATE_ATTRIBUTE =
        'UPDATE `custom_attributes`
				SET `display_label` = @display_label, `display_type` = @display_type, `attribute_category` = @attribute_category,
				`validation_regex` = @validation_regex, `is_required` = @is_required, `possible_values` = @possible_values, `sort_order` = @sort_order, `admin_only` = @admin_only,
				`secondary_category` = @secondary_category, `secondary_entity_ids` = @secondary_entity_ids, `is_private` = @is_private, `last_modified` = UTC_TIMESTAMP()
			WHERE `custom_attribute_id` = @custom_attribute_id';

    const UPDATE_BLACKOUT_INSTANCE = 'UPDATE `blackout_instances`
			SET `blackout_series_id` = @blackout_series_id, `start_date` = @startDate, `end_date` = @endDate
			WHERE `blackout_instance_id` = @blackout_instance_id';

    const UPDATE_GROUP_CREDIT_REPLENISHMENT = 'UPDATE `group_credit_replenishment_rule` SET
        `group_id` = @groupid, `type` =  @type, `amount` = @amount, `day_of_month` = @day_of_month, `interval` = @interval, 
         `last_replenishment_date` = COALESCE(@last_replenishment_date, `last_replenishment_date`)
        WHERE `group_credit_replenishment_rule_id` = @rule_id';

    const UPDATE_GROUP =
        'UPDATE `groups`
		SET `name` = @groupname, `admin_group_id` = @admin_group_id, `isdefault` = @isdefault, `last_modified` = UTC_TIMESTAMP(), limit_on_reservation = @limit_on_reservation
		WHERE `group_id` = @groupid';

    const UPDATE_LOGINDATA = 'UPDATE `users` SET `lastlogin` = @lastlogin, `language` = @language WHERE `user_id` = @userid';

    const UPDATE_MONITOR_VIEW = 'UPDATE `monitor_views` SET `monitor_view_name` = @name, `serialized_settings` = @serialized_settings, `last_modified` = @dateModified WHERE `public_id` = @publicid';

    const UPDATE_OAUTH_PROVIDER = 'UPDATE `oauth_authentication_providers`
        SET
        `provider_name` = @provider_name,
        `client_id` = @client_id,
        `client_secret` = @client_secret,
        `url_authorize` = @url_authorize,
        `url_access_token` = @url_access_token,
        `url_user_details` = @url_user_details,
        `access_token_grant` = @access_token_grant,
        `field_mappings` = @field_mappings,
        `scope` = @scope,
        `last_modified` = @dateModified
        WHERE `provider_id` = @provider_id';

    const UPDATE_PAYMENT_CONFIGURATION = 'UPDATE `payment_configuration` SET `credit_cost` = @credit_cost, `credit_currency` = @credit_currency, `last_modified` = UTC_TIMESTAMP()';

    const UPDATE_QUOTA =
        'UPDATE `quotas` 
            SET `quota_limit` = @limit, 
                `unit` = @unit, 
                `duration` = @duration, 
                `resource_id` = @resourceid, 
                `group_id` = @groupid, 
                `schedule_id` = @scheduleid, 
                `enforced_time_start` = @startTime, 
                `enforced_time_end` = @endTime, 
                `enforced_days` = @enforcedDays, 
                `scope` = @scope, 
                `interval` = @interval, 
                `last_modified` = UTC_TIMESTAMP(),
                `stop_enforcement_minutes_prior` = @stop_enforcement_minutes_prior
			WHERE `quota_id` = @quotaid';

    const UPDATE_FUTURE_RESERVATION_INSTANCES =
        'UPDATE `reservation_instances`
		SET `series_id` = @seriesid
		WHERE
			`series_id` = @currentSeriesId AND
			`start_date` >= (SELECT `start_date` FROM `reservation_instances` WHERE `reference_number` = @referenceNumber)';

    const UPDATE_RESERVATION_INSTANCE =
        'UPDATE `reservation_instances`
		SET
			`series_id` = @seriesid,
			`start_date` = @startDate,
			`end_date` = @endDate,
			`checkin_date` = @checkin_date,
			`checkout_date` = @checkout_date,
			`previous_end_date` = @previous_end_date,
			`credit_count` = @credit_count
		WHERE
			`reference_number` = @referenceNumber';

    const UPDATE_RESERVATION_OWNER_COMMAND =
        'UPDATE `reservation_series` SET `owner_id` = @target_user_id WHERE `owner_id` = @source_user_id; 
        UPDATE IGNORE `reservation_users` SET `user_id` = @target_user_id WHERE `user_id` = @source_user_id AND `reservation_user_level` = 1;';

    const UPDATE_RESERVATION_OWNER_FUTURE_COMMAND =
        'UPDATE IGNORE `reservation_series` SET `owner_id` = @target_user_id WHERE `series_id` IN (
        SELECT DISTINCT(`ri`.`series_id`) FROM `reservation_users` `ru` INNER JOIN `reservation_instances` `ri` on `ru`.`reservation_instance_id` = `ri`.`reservation_instance_id`
        WHERE `ri`.`start_date` > @minimum_date) AND `owner_id` = @source_user_id; 
        
        UPDATE IGNORE `reservation_users` SET `user_id` = @target_user_id WHERE `reservation_instance_id` IN 
        (SELECT `reservation_instance_id` FROM `reservation_instances` WHERE `series_id` IN (SELECT DISTINCT(`ri`.`series_id`) FROM `reservation_users` `ru` INNER JOIN `reservation_instances` `ri` ON `ru`.`reservation_instance_id` = `ri`.`reservation_instance_id`
        WHERE `ri`.`start_date` >  @minimum_date));';

    const UPDATE_RESERVATION_SERIES =
        'UPDATE
			`reservation_series`
		SET
			`last_modified` = @dateModified,
			`title` = @title,
			`description` = @description,
			`repeat_type` = @repeatType,
			`repeat_options` = @repeatOptions,
			`status_id` = @statusid,
			`owner_id` = @userid,
			`allow_participation` = @allow_participation,
			`last_action_by` = @last_action_by
		WHERE
			`series_id` = @seriesid';

    const UPDATE_RESERVATION_SERIES_RECURRENCE =
        'UPDATE
			`reservation_series`
		SET
			`last_modified` = UTC_TIMESTAMP(),
		    `repeat_options` = @repeatOptions
		WHERE
			`series_id` = @seriesid';

    const UPDATE_RESERVATION_SERIES_APPROVED_BY =
        'UPDATE
			`reservation_series`
		SET
			`approved_by` = @userid, `date_approved` = @date_approved
		WHERE
			`series_id` = @seriesid';

    const UPDATE_RESERVATION_COLOR_RULE ='UPDATE `reservation_color_rules` 
        SET `custom_attribute_id` = @custom_attribute_id,
            `attribute_type` = @attribute_type,
            `required_value` =  @required_value,
            `comparison_type` = @comparison_type, 
            `color` = @color,
            `priority` = @priority,
            `last_modified` =  @dateModified
        WHERE `reservation_color_rule_id` = @reservation_color_rule_id';

    const UPDATE_RESERVATION_MEETING_LINK = 'UPDATE `reservation_meeting_links` 
        SET `meeting_link_type` = @meeting_link_type,
            `meeting_link_url` = @meeting_link_url,
            `last_modified` = @dateModified
        WHERE `series_id` = @seriesid';

    const UPDATE_RESOURCE =
        'UPDATE `resources`
		SET
			`name` = @resource_name,
			`location` = @location,
			`contact_info` = @contact_info,
			`description` = @description,
			`notes` = @resource_notes,
			`min_duration` = @min_duration,
			`max_duration` = @max_duration,
			`autoassign` = @autoassign,
			`requires_approval` = @requires_approval,
			`allow_multiday_reservations` = @allow_multiday_reservations,
			`max_participants` = @max_participants,
			`min_notice_time_add` = @min_notice_time_add,
			`min_notice_time_update` = @min_notice_time_update,
			`min_notice_time_delete` = @min_notice_time_delete,
			`max_notice_time` = @max_notice_time,
			`image_name` = @imageName,
			`schedule_id` = @scheduleid,
			`admin_group_id` = @admin_group_id,
			`allow_calendar_subscription` = @allow_calendar_subscription,
			`public_id` = @publicid,
			`sort_order` = @sort_order,
			`resource_type_id` = @resource_type_id,
			`status_id` = @status_id,
			`resource_status_reason_id` = @resource_status_reason_id,
			`buffer_time` = @buffer_time,
			`color` = @color,
			`enable_check_in` = @enable_check_in,
			`auto_release_minutes` = @auto_release_minutes,
			`allow_display` = @allow_display,
			`credit_count` = @credit_count,
			`peak_credit_count` = @peak_credit_count,
			`last_modified` = @dateModified,
			`additional_properties` = @additional_properties,
			`credit_applicability` = @credit_applicability,
			`credits_charged_all_slots` = @credits_charged_all_slots,
		    `auto_extend_reservations` = @auto_extend_reservations,
		    `checkin_limited_to_admins` = @checkin_limited_to_admins,
		    `min_participants` = @min_participants,
		    `auto_release_action` = @auto_release_action
		WHERE
			`resource_id` = @resourceid';

    const UPDATE_RESOURCE_GROUP = 'UPDATE `resource_groups` SET `resource_group_name` = @resourcegroupname, `parent_id` = @parentid, `last_modified` = UTC_TIMESTAMP() WHERE `resource_group_id` = @resourcegroupid';

    const UPDATE_RESOURCE_MAP = 'UPDATE `resource_maps` SET `name` = @name, `status_id` = @statusid, `last_modified` = UTC_TIMESTAMP() WHERE `resource_map_id` = @resource_map_id';

    const UPDATE_RESOURCE_STATUS_REASON = 'UPDATE `resource_status_reasons` SET `description` = @description WHERE `resource_status_reason_id` = @resource_status_reason_id';

    const UPDATE_RESOURCE_TYPE = 'UPDATE `resource_types` SET `resource_type_name` = @resource_type_name, `resource_type_description` = @resource_type_description, `last_modified` = UTC_TIMESTAMP() WHERE `resource_type_id` = @resource_type_id';

    const UPDATE_SCHEDULE =
        'UPDATE `schedules`
		SET
			`name` = @scheduleName,
			`isdefault` = @scheduleIsDefault,
			`weekdaystart` = @scheduleWeekdayStart,
			`daysvisible` = @scheduleDaysVisible,
			`allow_calendar_subscription` = @allow_calendar_subscription,
			`public_id` = @publicid,
			`admin_group_id` = @admin_group_id,
			`start_date` = @start_date,
			`end_date` = @end_date,
			`default_layout` = @default_layout,
			`total_concurrent_reservations` = @total_concurrent_reservations,
			`max_resources_per_reservation` = @max_resources_per_reservation,
			`allow_blocked_slot_end` = @allow_blocked_slot_end,
		    `last_modified` = UTC_TIMESTAMP()
		WHERE
			`schedule_id` = @scheduleid';

    const UPDATE_SCHEDULE_LAYOUT =
        'UPDATE `schedules`
		SET
			`layout_id` = @layoutid,
		    `last_modified` = UTC_TIMESTAMP()
		WHERE
			`schedule_id` = @scheduleid';

    const UPDATE_SAVED_REPORT = 'UPDATE `saved_reports` SET 
                           `report_name` = @report_name, 
                           `report_details` = @report_details,
                           `report_schedule` = @report_schedule, 
                           `report_last_sent_date` = @report_last_sent_date
        WHERE `saved_report_id` = @report_id';

    const UPDATE_USER =
        'UPDATE `users`
		SET
			`status_id` = @user_statusid,
			`password` = @password_value,
			`salt` = @salt,
		    `password_hash_version` = @password_hash_version,
			`fname` = @fname,
			`lname` = @lname,
			`email` = @email,
			`username` = @username,
			`homepageId` = @homepageid,
			`last_modified` = @dateModified,
			`timezone` = @timezone,
			`allow_calendar_subscription` = @allow_calendar_subscription,
			`public_id` = @publicid,
			`language` = @language,
			`lastlogin` = @lastlogin,
			`default_schedule_id` = @scheduleid,
			`credit_count` = @credit_count,
			`api_only` = @api_only,
		    `force_password_reset` = @force_password_reset,
		    `remember_me_token` = @remember_me_token,
		    `login_token` = @login_token,
		    `phone_country_code` = @phone_country_code,
		    `phone` = @phone_number,
		    `organization` = @organization,
		    `position` = @position,
		    `phone_last_updated` = @phone_last_updated,
		    `date_format` = @date_format,
		    `time_format` =  @time_format
		WHERE
			`user_id` = @userid';

    const UPDATE_USER_BY_USERNAME =
        'UPDATE `users`
		SET
		    `username` = COALESCE(@username, `username`),
			`email` = COALESCE(@email, `email`),
			`password` = COALESCE(@password_value, `password`),
			`salt` = COALESCE(@salt, `salt`),
			`fname` = COALESCE(@fname, `fname`),
			`lname` = COALESCE(@lname, `lname`),
			`phone` = COALESCE(@phone_number, `phone`),
			`organization` = COALESCE(@organization, `organization`),
			`position` = COALESCE(@position, `position`),
		    `password_hash_version` = @password_hash_version,
		    `last_modified` = UTC_TIMESTAMP()
		WHERE
			`user_id` = @userid';

    const UPDATE_USER_PREFERENCE = 'UPDATE `user_preferences` SET `value` = @value WHERE `user_id` = @userid AND `name` = @name';

    const UPDATE_USER_SESSION =
        'UPDATE `user_session`
		SET
			`last_modified` = @dateModified,
			`user_session_value` = @user_session_value
		WHERE `user_id` = @userid AND `session_token` = @session_token';

    const UPDATE_USER_MFA_SETTINGS = 'UPDATE `users` SET `mfa_key` = @mfa_key, `mfa_generated` = @dateCreated, `last_modified` = UTC_TIMESTAMP() WHERE `user_id` = @userid';

    const UPDATE_USER_SMS_CONFIGURATION = 'UPDATE `user_sms` SET `opt_in_date` = @opt_in_date, `last_modified` = UTC_TIMESTAMP(), confirmation_code = @confirmation_code WHERE user_sms_id = @user_sms_id';

    const UPDATE_USER_OAUTH = 'UPDATE `user_oauth` 
            SET `access_token` = @access_token, `refresh_token` = @refresh_token, `expires_at` = @expires_at, `last_modified` = @dateModified
            WHERE `user_id` = @userid AND `provider_id` = @provider_id';

    const VALIDATE_USER =
        'SELECT `user_id`, `password`, `salt`, `password_hash_version`
		FROM `users`
		WHERE (`username` = @username OR `email` = @username) AND `status_id` = 1';
}

class QueryBuilder
{
    public static $DATE_FRAGMENT = '((`ri`.`start_date` >= @startDate AND `ri`.`start_date` <= @endDate) OR
					(`ri`.`end_date` >= @startDate AND `ri`.`end_date` <= @endDate) OR
					(`ri`.`start_date` <= @startDate AND `ri`.`end_date` >= @endDate))';

    public static $SELECT_LIST_FRAGMENT = '`ri`.*, 
                    `rs`.`date_created` as `date_created`, `rs`.`last_modified` as `last_modified`, `rs`.`description` as `description`, `rs`.`status_id` as `status_id`, `rs`.`title`, `rs`.`repeat_type`, `rs`.`repeat_options`, `rs`.`date_approved`, `rs`.`approved_by`,
					`owner`.`fname` as `owner_fname`, `owner`.`lname` as `owner_lname`, `owner`.`user_id` as `owner_id`, `owner`.`phone` as `owner_phone`, `owner`.`position` as `owner_position`, `owner`.`organization` as `owner_organization`, `owner`.`email` as `email`, `owner`.`language`, `owner`.`timezone`,
					`resources`.`name`, `resources`.`resource_id`, `resources`.`schedule_id`, `resources`.`status_id` as `resource_status_id`, `resources`.`resource_status_reason_id`, `resources`.`buffer_time`, `resources`.`color`, `resources`.`enable_check_in`, `resources`.`auto_release_minutes`, `resources`.`admin_group_id` as `resource_admin_group_id`, `resources`.`additional_properties` as `resource_additional_properties`, `resources`.`checkin_limited_to_admins`,
					`ru`.`reservation_user_level`, `schedules`.`admin_group_id` as `schedule_admin_group_id`,
					`start_reminder`.`minutes_prior` AS `start_reminder_minutes`, `end_reminder`.`minutes_prior` AS `end_reminder_minutes`,
					`approver`.`fname` as `approver_fname`, `approver`.`lname` as `approver_lname`, `approver`.`user_id` as `approver_id`,
					(SELECT GROUP_CONCAT(CONCAT(`groups`.`group_id`,\'=\', `groups`.`name`) SEPARATOR "!sep!")
						FROM `user_groups` INNER JOIN `groups` ON `user_groups`.`group_id` = `groups`.`group_id` WHERE `owner`.`user_id` = `user_groups`.`user_id`) as `owner_group_list`,

                    (SELECT GROUP_CONCAT(CONCAT(`coowners`.`user_id`, \'=\', CONCAT(`coowner_users`.`fname`, " ", `coowner_users`.`lname`)) SEPARATOR "!sep!")
						FROM `reservation_users` `coowners` INNER JOIN `users` `coowner_users` ON `coowners`.`user_id` = `coowner_users`.`user_id` WHERE `coowners`.`reservation_instance_id` = `ri`.`reservation_instance_id` AND `coowners`.`reservation_user_level` = 4) as `coowner_list`,

					(SELECT GROUP_CONCAT(CONCAT(`participants`.`user_id`, \'=\', CONCAT(`participant_users`.`fname`, " ", `participant_users`.`lname`)) SEPARATOR "!sep!")
						FROM `reservation_users` `participants` INNER JOIN `users` `participant_users` ON `participants`.`user_id` = `participant_users`.`user_id` WHERE `participants`.`reservation_instance_id` = `ri`.`reservation_instance_id` AND `participants`.`reservation_user_level` = 2) as `participant_list`,

					(SELECT GROUP_CONCAT(CONCAT(`invitees`.`user_id`, \'=\', CONCAT(`invitee_users`.`fname`, " ", `invitee_users`.`lname`)) SEPARATOR "!sep!")
						FROM `reservation_users` `invitees` INNER JOIN `users` `invitee_users` ON `invitees`.`user_id` = `invitee_users`.`user_id` WHERE `invitees`.`reservation_instance_id` = `ri`.`reservation_instance_id` AND `invitees`.`reservation_user_level` = 3) as `invitee_list`,

					(SELECT GROUP_CONCAT(CONCAT(`cav`.`custom_attribute_id`,\'=\', `cav`.`attribute_value`) SEPARATOR "!sep!")
						FROM `custom_attribute_values` `cav` WHERE `cav`.`entity_id` = `ri`.`series_id` AND `cav`.`attribute_category` = 1) as `attribute_list`,

					(SELECT GROUP_CONCAT(CONCAT(`p`.`name`, "=", `p`.`value`) SEPARATOR "!sep!")
						FROM `user_preferences` `p` WHERE `owner`.`user_id` = `p`.`user_id`) as `preferences`,
						
					(SELECT GROUP_CONCAT(CONCAT(`guests`.`email`, "=", `guests`.`reservation_user_level`) SEPARATOR "!sep!")
						FROM `reservation_guests` `guests` WHERE `guests`.`reservation_instance_id` = `ri`.`reservation_instance_id`) as `guest_list`,
						
					(SELECT GROUP_CONCAT(`reservation_files`.`file_id`) 
					    FROM `reservation_files` WHERE `reservation_files`.`series_id` = `rs`.`series_id`) as `attachment_list`';

    private static function Build($selectValue, $joinValue, $andValue)
    {
        return str_replace('[AND_TOKEN]', $andValue . '',
            str_replace('[JOIN_TOKEN]', $joinValue . '',
                str_replace('[SELECT_TOKEN]', $selectValue . '',
                    Queries::GET_RESERVATION_LIST_TEMPLATE)));
    }

    public static function GET_RESERVATION_LIST()
    {
        return self::Build(self::$SELECT_LIST_FRAGMENT, null, 'AND ' . self::$DATE_FRAGMENT . ' AND
					(@all_owners = 1 OR `ru`.`user_id` IN (@userid)) AND
					(@levelid = 0 OR `ru`.`reservation_user_level` = @levelid) AND
					(@all_schedules = 1 OR `resources`.`schedule_id` IN (@scheduleid)) AND
					(@all_resources = 1 OR `rr`.`resource_id` IN (@resourceid)) AND
					(@all_participants = 1 OR `ri`.`reservation_instance_id` IN (SELECT `reservation_instance_id` FROM `reservation_users` WHERE `user_id` IN (@participant_id) AND `reservation_user_level` IN (2, 3)))');
    }

    public static function GET_RESERVATION_LIST_FULL()
    {
        return self::Build(self::$SELECT_LIST_FRAGMENT, null, 'AND `ru`.`reservation_user_level` = @levelid');
    }
}