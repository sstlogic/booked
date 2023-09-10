<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

class ParameterNames
{
    private function __construct()
	{
	}

	const ACCESSORY_ID = '@accessoryid';
	const ACCESSORY_NAME = '@accessoryname';
	const ACCESSORY_QUANTITY = '@quantity';
	const ACCESSORY_MIN_QUANTITY = '@minimum_quantity';
	const ACCESSORY_MAX_QUANTITY = '@maximum_quantity';

	const ACTIVATION_CODE = '@activation_code';

	const ALL_RESOURCES = '@all_resources';
	const ALL_SCHEDULES = '@all_schedules';
	const All_OWNERS = '@all_owners';
	const ALL_PARTICIPANTS = '@all_participants';
	const ALLOW_CALENDAR_SUBSCRIPTION = '@allow_calendar_subscription';
	const ALLOW_PARTICIPATION = '@allow_participation';
	const ACCESS_TOKEN = '@access_token';

	const ANNOUNCEMENT_ID = '@announcementid';
	const ANNOUNCEMENT_TEXT = '@text';
	const ANNOUNCEMENT_PRIORITY = '@priority';
	const ANNOUNCEMENT_DISPLAY_PAGE = '@display_page';

	const ATTRIBUTE_ID = '@custom_attribute_id';
	const ATTRIBUTE_ADMIN_ONLY = '@admin_only';
	const ATTRIBUTE_CATEGORY = '@attribute_category';
	const ATTRIBUTE_LABEL = '@display_label';
	const ATTRIBUTE_POSSIBLE_VALUES = '@possible_values';
	const ATTRIBUTE_REGEX = '@validation_regex';
	const ATTRIBUTE_REQUIRED = '@is_required';
	const ATTRIBUTE_SORT_ORDER = '@sort_order';
	const ATTRIBUTE_TYPE = '@display_type';
	const ATTRIBUTE_VALUE = '@attribute_value';
	const ATTRIBUTE_ENTITY_ID = '@entity_id';
	const ATTRIBUTE_ENTITY_IDS = '@entity_ids';
	const ATTRIBUTE_UNIQUE_PER_ENTITY = '@unique_per_entity';
	const ATTRIBUTE_SECONDARY_CATEGORY = '@secondary_category';
	const ATTRIBUTE_SECONDARY_ENTITY_IDS = '@secondary_entity_ids';
	const ATTRIBUTE_IS_PRIVATE = '@is_private';
	const AUTO_RELEASE_MINUTES = '@auto_release_minutes';
	const AUTO_RELEASE_ACTION = '@auto_release_action';
	const ADDITIONAL_PROPERTIES = '@additional_properties';
	const API_ONLY = '@api_only';
	const ALLOW_BLOCKED_SLOT_END = '@allow_blocked_slot_end';
	const ACCESS_TOKEN_GRANT = '@access_token_grant';

	const BLACKOUT_SERIES_ID = '@blackout_series_id';
	const BLACKOUT_INSTANCE_ID = '@blackout_instance_id';

	const CHECKIN_DATE = '@checkin_date';
	const CHECKOUT_DATE = '@checkout_date';
	const COLOR_ATTRIBUTE_TYPE = '@attribute_type';
	const COLOR_PRIORITY = '@priority';
	const COLOR = '@color';
	const COMPARISON_TYPE = '@comparison_type';
	const COLOR_REQUIRED_VALUE = '@required_value';
	const COLOR_ATTRIBUTE_ID = '@attribute_id';
	const CURRENT_DATE = '@current_date';
	const CURRENT_SERIES_ID = '@currentSeriesId';
	const COLOR_RULE_ID = '@reservation_color_rule_id';
	const CREDIT_COUNT = '@credit_count';
	const CREDIT_COST = '@credit_cost';
	const CREDIT_CURRENCY = '@credit_currency';
	const CREDIT_NOTE = '@credit_note';
	const CREDIT_APPLICABILITY = '@credit_applicability';
	const CREDITS_CHARGED_ALL_SLOTS = '@credits_charged_all_slots';
	const CHECKIN_LIMITED_TO_ADMINS = '@checkin_limited_to_admins';
	const COOWNER_ID = '@coowner_id';
	const CLIENT_ID = '@client_id';
	const CLIENT_SECRET = '@client_secret';

	const DATE_CREATED = '@dateCreated';
	const DATE_MODIFIED = '@dateModified';
	const DESCRIPTION = '@description';
    const DATE_FORMAT = '@date_format';
    const DELETE_REASON = '@delete_reason';
    const DATE_APPROVED = '@date_approved';

	const END_DATE = '@endDate';
	const END_TIME = '@endTime';
	const ENABLE_CHECK_IN = '@enable_check_in';
	const ENFORCED_DAYS = '@enforcedDays';
	const EMAIL_ADDRESS = '@email';
	const EVENT_CATEGORY = '@event_category';
	const EVENT_TYPE = '@event_type';
	const EVENT_NOTIFICATION_METHOD = '@notification_method';
	const EXPIRES_AT = '@expires_at';

	const FILE_ID = '@file_id';
	const FILE_NAME = '@file_name';
	const FILE_TYPE = '@file_type';
	const FILE_SIZE = '@file_size';
	const FILE_EXTENSION = '@file_extension';
	const FIRST_NAME = '@fname';
    const FORCE_PASSWORD_RESET = '@force_password_reset';
    const FIELD_MAPPINGS = '@field_mappings';

    const GATEWAY_TYPE = '@gateway_type';
    const GATEWAY_SETTING_NAME = '@setting_name';
    const GATEWAY_SETTING_VALUE = '@setting_value';
    const GROUP_ID = '@groupid';
	const GROUP_NAME = '@groupname';
	const GROUP_ADMIN_ID = '@admin_group_id';
	const GROUP_ISDEFAULT = '@isdefault';
	const GROUP_LIMIT_ON_RESERVATION = '@limit_on_reservation';
    const GROUP_CREDIT_REPLENISHMENT_TYPE = "@type";
    const GROUP_CREDIT_REPLENISHMENT_AMOUNT = "@amount";
    const GROUP_CREDIT_REPLENISHMENT_INTERVAL = "@interval";
    const GROUP_CREDIT_REPLENISHMENT_DAYOFMONTH = "@day_of_month";
    const GROUP_CREDIT_REPLENISHMENT_ID = '@rule_id';
    const GROUP_CREDIT_REPLENISHMENT_LAST_DATE = '@last_replenishment_date';

    const HOMEPAGE_ID = '@homepageid';

	const LAST_LOGIN = '@lastlogin';
	const LAST_NAME = '@lname';
	const LAYOUT_ID = '@layoutid';
    const LANGUAGE = '@language';
    const LAYOUT_TYPE = '@layout_type';
    const LAST_ACTION_BY = '@last_action_by';
    const LOGIN_TOKEN = '@login_token';

	const NAME = '@name';

	const MFA_KEY = "@mfa_key";
	const MINIMUM_DATE = "@minimum_date";
	const RESOURCE_MIN_PARTICIPANTS = "@min_participants";

	const ORGANIZATION = '@organization';
	const ORIGINAL_CREDIT_COUNT = '@original_credit_count';
	const OAUTH_SCOPE = '@scope';

    const PASSWORD = '@password_value';
    const PASSWORD_HASH_VERSION = '@password_hash_version';
	const PARTICIPANT_ID = '@participant_id';
	const PAYMENT_STATUS = '@status';
	const PAYMENT_INVOICE_NUMBER = '@invoice_number';
	const PAYMENT_TRANSACTION_LOG_ID = '@payment_transaction_log_id';
	const PAYMENT_TRANSACTION_ID = '@transaction_id';
	const PAYMENT_TOTAL = '@total_amount';
	const PAYMENT_TRANSACTION_FEE = '@transaction_fee';
	const PAYMENT_CURRENCY = '@currency';
	const PAYMENT_TRANSACTION_HREF = '@transaction_href';
	const PAYMENT_REFUND_HREF = '@refund_href';
	const PAYMENT_DATE_CREATED = '@date_created';
	const PAYMENT_GATEWAY_DATE_CREATED = '@gateway_date_created';
	const PAYMENT_GATEWAY_NAME = '@gateway_name';
	const PAYMENT_GATEWAY_RESPONSE = '@payment_response';
	const PEAK_CREDIT_COUNT = '@peak_credit_count';
	const PEAK_TIMES_ALL_DAY = '@all_day';
	const PEAK_TIMES_START_TIME = '@start_time';
	const PEAK_TIMES_END_TIME = '@end_time';
	const PEAK_TIMES_EVERY_DAY = '@every_day';
	const PEAK_TIMES_DAYS = '@peak_days';
	const PEAK_TIMES_ALL_YEAR = '@all_year';
	const PEAK_TIMES_BEGIN_DAY = '@begin_day';
	const PEAK_TIMES_BEGIN_MONTH = '@begin_month';
	const PEAK_TIMES_END_DAY = '@end_day';
	const PEAK_TIMES_END_MONTH = '@end_month';
	const PERIOD_AVAILABILITY_TYPE = '@periodType';
	const PERIOD_DAY_OF_WEEK = '@day_of_week';
	const PERIOD_LABEL = '@label';
	const PHONE = '@phone_number';
	const PHONE_COUNTRY_CODE = '@phone_country_code';
	const POSITION = '@position';
	const PREVIOUS_END_DATE = '@previous_end_date';
	const PUBLIC_ID = '@publicid';
    const PERMISSION_TYPE = '@permission_type';
    const PASSWORD_RESET_TOKEN = '@reset_token';
    const PHONE_LAST_UPDATED = '@phone_last_updated';
    const PROVIDER_ID = '@provider_id';
    const PROVIDER_NAME = '@provider_name';

	const QUOTA_DURATION = '@duration';
	const QUOTA_ID = '@quotaid';
	const QUOTA_LIMIT = '@limit';
	const QUOTA_SCOPE = '@scope';
	const QUOTA_UNIT = '@unit';
	const QUOTA_INTERVAL = '@interval';
	const QUOTA_STOP_ENFORCEMENT_MINUTES_PRIOR = '@stop_enforcement_minutes_prior';

	const REFUND_STATUS = '@status';
	const REFUND_TRANSACTION_ID = '@transaction_id';
	const REFUND_TOTAL_AMOUNT = '@total_refund_amount';
	const REFUND_PAYMENT_AMOUNT = '@payment_refund_amount';
	const REFUND_FEE_AMOUNT = '@fee_refund_amount';
	const REFUND_TRANSACTION_HREF = '@transaction_href';
	const REFUND_DATE_CREATED = '@date_created';
	const REFUND_GATEWAY_DATE_CREATED = '@gateway_date_created';
	const REFUND_GATEWAY_RESPONSE = '@refund_response';
	const REMEMBER_ME_TOKEN = '@remember_me_token';

	const REMINDER_ID = '@reminder_id';
	const REMINDER_USER_ID = '@user_id';
	const REMINDER_SENDTIME = '@sendtime';
	const REMINDER_MESSAGE = '@message';
	const REMINDER_ADDRESS = '@address';
	const REMINDER_REFNUMBER = '@refnumber';
	const REMINDER_MINUTES_PRIOR = '@minutes_prior';
	const REMINDER_TYPE = '@reminder_type';

	const REFERENCE_NUMBER = '@referenceNumber';

	const REPEAT_OPTIONS = '@repeatOptions';
	const REPEAT_TYPE = '@repeatType';

	const REPORT_NAME = '@report_name';
	const REPORT_DETAILS = '@report_details';
	const REPORT_ID = "@report_id";
	const REPORT_SCHEDULE = "@report_schedule";
	const REPORT_LAST_SENT = "@report_last_sent_date";

	const RESERVATION_INSTANCE_ID = '@reservationid';
	const RESERVATION_USER_LEVEL_ID = '@levelid';
    const RESERVATION_WAITLIST_REQUEST_ID = '@reservation_waitlist_request_id';

    const RESERVATION_MEETING_TYPE = '@meeting_link_type';
    const RESERVATION_MEETING_URL = '@meeting_link_url';
    const RESERVATION_MEETING_EXTERNAL_ID = '@meeting_external_id';

	const RESOURCE_ID = '@resourceid';
	const RESOURCE_IDS = '@resourceids';
	const RESOURCE_ALLOW_MULTIDAY = '@allow_multiday_reservations';
	const RESOURCE_ALLOW_DISPLAY = '@allow_display';
	const RESOURCE_AUTOASSIGN = '@autoassign';
	const RESOURCE_CONTACT = '@contact_info';
	const RESOURCE_COST = '@unit_cost';
	const RESOURCE_DESCRIPTION = '@description';
	const RESOURCE_LOCATION = '@location';
	const RESOURCE_MAX_PARTICIPANTS = '@max_participants';
	const RESOURCE_MAXDURATION = '@max_duration';
	const RESOURCE_MAXNOTICE = '@max_notice_time';
	const RESOURCE_MINDURATION = '@min_duration';
	const RESOURCE_MININCREMENT = '@min_increment';
	const RESOURCE_MINNOTICE_ADD = '@min_notice_time_add';
	const RESOURCE_MINNOTICE_UPDATE = '@min_notice_time_update';
	const RESOURCE_MINNOTICE_DELETE = '@min_notice_time_delete';
	const RESOURCE_NAME = '@resource_name';
	const RESOURCE_NOTES = '@resource_notes';
	const RESOURCE_REQUIRES_APPROVAL = '@requires_approval';
	const RESOURCE_LEVEL_ID = '@resourceLevelId';
	const RESOURCE_IMAGE_NAME = '@imageName';
	const RESOURCE_SORT_ORDER = '@sort_order';
	const RESOURCE_STATUS = '@status_id';
	const RESOURCE_STATUS_REASON_ID = '@resource_status_reason_id';
	const RESOURCE_STATUS_REASON_DESCRIPTION = '@description';
	const RESOURCE_BUFFER_TIME = '@buffer_time';
    const RESOURCE_AUTO_EXTEND = '@auto_extend_reservations';

	const RESOURCE_GROUP_ID = '@resourcegroupid';
	const RESOURCE_GROUP_NAME = '@resourcegroupname';
	const RESOURCE_GROUP_PARENT_ID = '@parentid';

	const RESOURCE_TYPE_ID = '@resource_type_id';
	const RESOURCE_TYPE_NAME = '@resource_type_name';
	const RESOURCE_TYPE_DESCRIPTION = '@resource_type_description';
	const RELATED_RESOURCE_ID = '@related_resource_id';
	const RELATIONSHIP_TYPE = '@relationship_type';
	const REFRESH_TOKEN = '@refresh_token';

    const RESOURCE_MAP_ID = '@resource_map_id';
    const RESOURCE_MAP_COORDINATES = '@coordinates';
    const RESOURCE_MAP_STATUS = '@status_id';

	const ROLE_ID = '@roleid';
	const ROLE_LEVEL = '@role_level';

	const SALT = '@salt';
	const SCHEDULE_ID = '@scheduleid';
	const SCHEDULE_NAME = '@scheduleName';
	const SCHEDULE_ISDEFAULT = '@scheduleIsDefault';
	const SCHEDULE_WEEKDAYSTART = '@scheduleWeekdayStart';
	const SCHEDULE_DAYSVISIBLE = '@scheduleDaysVisible';
	const SCHEDULE_AVAILABILITY_BEGIN = '@start_date';
	const SCHEDULE_AVAILABILITY_END = '@end_date';
	const SCHEDULE_ALLOW_CONCURRENT_RESERVATIONS = '@allow_concurrent_bookings';
	const SCHEDULE_DEFAULT_STYLE = '@default_layout';
	const SCHEDULE_TOTAL_CONCURRENT_RESERVATIONS = '@total_concurrent_reservations';
	const SCHEDULE_MAX_RESOURCES_PER_RESERVATION = '@max_resources_per_reservation';
	const SERIES_ID = '@seriesid';
	const SESSION_TOKEN = '@session_token';
	const START_DATE = '@startDate';
	const START_TIME = '@startTime';
	const STATUS_ID = '@statusid';
	const SERIALIZED_SETTINGS = '@serialized_settings';
	const SOURCE_USER_ID = '@source_user_id';
    const SMS_CONFIRMATION_CODE = '@confirmation_code';
    const SMS_OPT_IN_DATE = '@opt_in_date';

	const TIMEZONE_NAME = '@timezone';
	const TYPE_ID = '@typeid';
	const TERMS_TEXT = '@terms_text';
	const TERMS_URL = '@terms_url';
	const TERMS_FILENAME = '@terms_file';
	const TERMS_APPLICABILITY = '@applicability';
	const TITLE = '@title';
	const TERMS_ACCEPTANCE_DATE = '@terms_date_accepted';
	const TARGET_USER_ID = '@target_user_id';
    const TIME_FORMAT = '@time_format';

	const USER_ID = '@userid';
	const USER_ROLE_ID = '@user_roleid';
	const USER_STATUS_ID = '@user_statusid';
	const USER_SESSION = '@user_session_value';
	const USERNAME = '@username';
	const USER_SMS_ID = '@user_sms_id';
	const URL_AUTHORIZE = '@url_authorize';
	const URL_ACCESS_TOKEN = '@url_access_token';
	const URL_USER_DETAILS = '@url_user_details';

	const VALUE = '@value';


	// used?
	const FIRST_NAME_SETTING = '@fname_setting';
	const LAST_NAME_SETTING = '@lname_setting';
	const USERNAME_SETTING = '@username_setting';
	const EMAIL_ADDRESS_SETTING = '@email_setting';
	const PASSWORD_SETTING = '@password_setting';
	const ORGANIZATION_SELECTION_SETTING = '@organization_setting';
	const GROUP_SETTING = '@group_setting';
	const POSITION_SETTING = '@position_setting';
	const ADDRESS_SETTING = '@address_setting';
	const PHONE_SETTING = '@phone_setting';
	const HOMEPAGE_SELECTION_SETTING = '@homepage_setting';
	const TIMEZONE_SELECTION_SETTING = '@timezone_setting';
}

