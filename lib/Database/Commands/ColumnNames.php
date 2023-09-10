<?php

/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

class ColumnNames
{
    private function __construct()
    {
    }

    // USERS //
    const USER_ID = 'user_id';
    const USERNAME = 'username';
    const EMAIL = 'email';
    const FIRST_NAME = 'fname';
    const LAST_NAME = 'lname';
    const PASSWORD = 'password';
    const PASSWORD_HASH_VERSION = 'password_hash_version';
    const USER_CREATED = 'date_created';
    const USER_MODIFIED = 'last_modified';
    const USER_STATUS_ID = 'status_id';
    const HOMEPAGE_ID = 'homepageid';
    const LAST_LOGIN = 'lastlogin';
    const TIMEZONE_NAME = 'timezone';
    const LANGUAGE_CODE = 'language';
    const SALT = 'salt';
    const PHONE_NUMBER = 'phone';
    const PHONE_LAST_UPDATED = 'phone_last_updated';
    const PHONE_COUNTRY_CODE = 'phone_country_code';
    const ORGANIZATION = 'organization';
    const POSITION = 'position';
    const DEFAULT_SCHEDULE_ID = 'default_schedule_id';
    const USER_PREFERENCES = 'preferences';
    const USER_STATUS = 'status_id';
    const API_ONLY = 'api_only';
    const FORCE_PASSWORD_RESET = 'force_password_reset';
    const REMEMBER_ME_TOKEN = 'remember_me_token';
    const LOGIN_TOKEN = 'login_token';
    const MFA_KEY = 'mfa_key';
    const MFA_GENERATED = 'mfa_generated';
    const DATE_FORMAT = 'date_format';
    const TIME_FORMAT = 'time_format';

    // USER_ADDRESSES //
    const ADDRESS_ID = 'address_id';

    // USER_PREFERENCES //
    const PREFERENCE_NAME = 'name';
    const PREFERENCE_VALUE = 'value';

    // USER_OAUTH //
    const ACCESS_TOKEN = 'access_token';
    const REFRESH_TOKEN = 'refresh_token';
    const EXPIRES_AT = 'expires_at';
    const PROVIDER_ID = 'provider_id';

    // ROLES //
    const ROLE_LEVEL = 'role_level';
    const ROLE_ID = 'role_id';
    const ROLE_NAME = 'name';

    // ANNOUNCEMENTS //
    const ANNOUNCEMENT_ID = 'announcementid';
    const ANNOUNCEMENT_PRIORITY = 'priority';
    const ANNOUNCEMENT_START = 'start_date';
    const ANNOUNCEMENT_END = 'end_date';
    const ANNOUNCEMENT_TEXT = 'announcement_text';
    const ANNOUNCEMENT_DISPLAY_PAGE = 'display_page';

    // GROUPS //
    const GROUP_ID = 'group_id';
    const GROUP_NAME = 'name';
    const GROUP_ADMIN_GROUP_ID = 'admin_group_id';
    const GROUP_ADMIN_GROUP_NAME = 'admin_group_name';
    const GROUP_ISDEFAULT = 'isdefault';

    // RESOURCE_GROUPS //
    const RESOURCE_GROUP_ID = 'resource_group_id';
    const RESOURCE_GROUP_NAME = 'resource_group_name';
    const RESOURCE_GROUP_PARENT_ID = 'parent_id';

    // TIME BLOCKS //
    const BLOCK_DAY_OF_WEEK = 'day_of_week';
    const BLOCK_LABEL = 'label';
    const BLOCK_LABEL_END = 'end_label';
    const BLOCK_CODE = 'availability_code';
    const BLOCK_TIMEZONE = 'timezone';
    const LAYOUT_TYPE = 'layout_type';

    // TIME BLOCK USES //
    const BLOCK_START = 'start_time';
    const BLOCK_END = 'end_time';

    const CUSTOM_ATTRIBUTE_ID = 'custom_attribute_id';
    const CUSTOM_ATTRIBUTE_VALUE = 'attribute_value';

    // RESERVATION SERIES //
    const RESERVATION_USER = 'user_id';
    const RESERVATION_GROUP = 'group_id';
    const RESERVATION_CREATED = 'date_created';
    const RESERVATION_MODIFIED = 'last_modified';
    const RESERVATION_TYPE = 'type_id';
    const RESERVATION_TITLE = 'title';
    const RESERVATION_DESCRIPTION = 'description';
    const RESERVATION_COST = 'total_cost';
    const RESERVATION_PARENT_ID = 'parent_id';
    const REPEAT_TYPE = 'repeat_type';
    const REPEAT_OPTIONS = 'repeat_options';
    const RESERVATION_STATUS = 'status_id';
    const RESERVATION_DELETE_REASON = 'delete_reason';
    const SERIES_ID = 'series_id';
    const RESERVATION_OWNER = 'owner_id';
    const RESERVATION_ALLOW_PARTICIPATION = 'allow_participation';
    const RESERVATION_TERMS_ACCEPTANCE_DATE = 'terms_date_accepted';
    const RESERVATION_SERIES_ID = 'series_id';

    // RESERVATION_INSTANCE //
    const RESERVATION_INSTANCE_ID = 'reservation_instance_id';
    const RESERVATION_START = 'start_date';
    const RESERVATION_END = 'end_date';
    const REFERENCE_NUMBER = 'reference_number';
    const CHECKIN_DATE = 'checkin_date';
    const CHECKOUT_DATE = 'checkout_date';
    const PREVIOUS_END_DATE = 'previous_end_date';

    // RESERVATION_USER //
    const RESERVATION_USER_LEVEL = 'reservation_user_level';

    // RESERVATION MEETING LINK //
    const RESERVATION_MEETING_LINK_TYPE = 'meeting_link_type';
    const RESERVATION_MEETING_LINK_URL = 'meeting_link_url';
    const RESERVATION_MEETING_LINK_EXTERNAL_ID = 'meeting_external_id';

    // RESOURCE //
    const RESOURCE_ID = 'resource_id';
    const RESOURCE_NAME = 'name';
    const RESOURCE_LOCATION = 'location';
    const RESOURCE_CONTACT = 'contact_info';
    const RESOURCE_DESCRIPTION = 'description';
    const RESOURCE_NOTES = 'notes';
    const RESOURCE_MINDURATION = 'min_duration';
    const RESOURCE_MININCREMENT = 'min_increment';
    const RESOURCE_MAXDURATION = 'max_duration';
    const RESOURCE_COST = 'unit_cost';
    const RESOURCE_AUTOASSIGN = 'autoassign';
    const RESOURCE_REQUIRES_APPROVAL = 'requires_approval';
    const RESOURCE_ALLOW_MULTIDAY = 'allow_multiday_reservations';
    const RESOURCE_MAX_PARTICIPANTS = 'max_participants';
    const RESOURCE_MINNOTICE_ADD = 'min_notice_time_add';
    const RESOURCE_MINNOTICE_UPDATE = 'min_notice_time_update';
    const RESOURCE_MINNOTICE_DELETE = 'min_notice_time_delete';
    const RESOURCE_MAXNOTICE = 'max_notice_time';
    const RESOURCE_IMAGE_NAME = 'image_name';
    const RESOURCE_STATUS_ID = 'status_id';
    const RESOURCE_STATUS_ID_ALIAS = 'resource_status_id';
    const RESOURCE_STATUS_REASON_ID = 'resource_status_reason_id';
    const RESOURCE_STATUS_DESCRIPTION = 'description';
    const RESOURCE_ADMIN_GROUP_ID = 'admin_group_id';
    const RESOURCE_SORT_ORDER = 'sort_order';
    const RESOURCE_BUFFER_TIME = 'buffer_time';
    const ENABLE_CHECK_IN = 'enable_check_in';
    const AUTO_RELEASE_MINUTES = 'auto_release_minutes';
    const AUTO_RELEASE_ACTION = 'auto_release_action';
    const RESOURCE_ALLOW_DISPLAY = 'allow_display';
    const RESOURCE_IMAGE_LIST = 'image_list';
    const RESOURCE_RELATIONSHIP_LIST = 'relationship_list';
    const PERMISSION_TYPE = 'permission_type';
    const RESOURCE_ADDITIONAL_PROPERTIES = 'additional_properties';
    const RESOURCE_AUTO_EXTEND = 'auto_extend_reservations';
    const CHECKIN_LIMITED_TO_ADMINS = 'checkin_limited_to_admins';
    const MIN_PARTICIPANTS = 'min_participants';

    // RESERVATION RESOURCES
    const RESOURCE_LEVEL_ID = 'resource_level_id';

    // SCHEDULE //
    const SCHEDULE_ID = 'schedule_id';
    const SCHEDULE_NAME = 'name';
    const SCHEDULE_DEFAULT = 'isdefault';
    const SCHEDULE_WEEKDAY_START = 'weekdaystart';
    const SCHEDULE_DAYS_VISIBLE = 'daysvisible';
    const LAYOUT_ID = 'layout_id';
    const SCHEDULE_ADMIN_GROUP_ID = 'admin_group_id';
    const SCHEDULE_ADMIN_GROUP_ID_ALIAS = 's_admin_group_id';
    const SCHEDULE_AVAILABLE_START_DATE = 'start_date';
    const SCHEDULE_AVAILABLE_END_DATE = 'end_date';
    const SCHEDULE_ALLOW_CONCURRENT_RESERVATIONS = 'allow_concurrent_bookings';
    const SCHEDULE_DEFAULT_STYLE = 'default_layout';
    const TOTAL_CONCURRENT_RESERVATIONS = 'total_concurrent_reservations';
    const MAX_RESOURCES_PER_RESERVATION = 'max_resources_per_reservation';
    const ALLOW_BLOCKED_SLOT_END = 'allow_blocked_slot_end';

    // EMAIL PREFERENCES //
    const EVENT_CATEGORY = 'event_category';
    const EVENT_TYPE = 'event_type';
    const EVENT_NOTIFICATION_METHOD = 'notification_method';

    const REPEAT_START = 'repeat_start';
    const REPEAT_END = 'repeat_end';

    // QUOTAS //
    const QUOTA_ID = 'quota_id';
    const QUOTA_LIMIT = 'quota_limit';
    const QUOTA_UNIT = 'unit';
    const QUOTA_DURATION = 'duration';
    const ENFORCED_START_TIME = 'enforced_time_start';
    const ENFORCED_END_TIME = 'enforced_time_end';
    const ENFORCED_DAYS = 'enforced_days';
    const QUOTA_SCOPE = 'scope';
    const QUOTA_INTERVAL = 'interval';
    const QUOTA_STOP_ENFORCEMENT_MINUTES_PRIOR = 'stop_enforcement_minutes_prior';

    // ACCESSORIES //
    const ACCESSORY_ID = 'accessory_id';
    const ACCESSORY_NAME = 'accessory_name';
    const ACCESSORY_QUANTITY = 'accessory_quantity';
    const ACCESSORY_RESOURCE_COUNT = 'num_resources';
    const ACCESSORY_MINIMUM_QUANTITY = 'minimum_quantity';
    const ACCESSORY_MAXIMUM_QUANTITY = 'maximum_quantity';

    // RESERVATION ACCESSORY //
    const QUANTITY = 'quantity';

    // BLACKOUTS //
    const BLACKOUT_INSTANCE_ID = 'blackout_instance_id';
    const BLACKOUT_START = 'start_date';
    const BLACKOUT_END = 'end_date';
    const BLACKOUT_TITLE = 'title';
    const BLACKOUT_DESCRIPTION = 'description';
    const BLACKOUT_SERIES_ID = 'blackout_series_id';

    // ATTRIBUTES //
    const ATTRIBUTE_ID = 'custom_attribute_id';
    const ATTRIBUTE_ADMIN_ONLY = 'admin_only';
    const ATTRIBUTE_LABEL = 'display_label';
    const ATTRIBUTE_TYPE = 'display_type';
    const ATTRIBUTE_CATEGORY = 'attribute_category';
    const ATTRIBUTE_CONSTRAINT = 'validation_regex';
    const ATTRIBUTE_REQUIRED = 'is_required';
    const ATTRIBUTE_POSSIBLE_VALUES = 'possible_values';
    const ATTRIBUTE_VALUE = 'attribute_value';
    const ATTRIBUTE_ENTITY_ID = 'entity_id';
    const ATTRIBUTE_ENTITY_IDS = 'entity_ids';
    const ATTRIBUTE_ENTITY_DESCRIPTIONS = 'entity_descriptions';
    const ATTRIBUTE_SORT_ORDER = 'sort_order';
    const ATTRIBUTE_SECONDARY_CATEGORY = 'secondary_category';
    const ATTRIBUTE_SECONDARY_ENTITY_IDS = 'secondary_entity_ids';
    const ATTRIBUTE_SECONDARY_ENTITY_DESCRIPTIONS = 'secondary_entity_descriptions';
    const ATTRIBUTE_IS_PRIVATE = 'is_private';

    // RESERVATION FILES //
    const FILE_ID = 'file_id';
    const FILE_NAME = 'file_name';
    const FILE_TYPE = 'file_type';
    const FILE_SIZE = 'file_size';
    const FILE_EXTENSION = 'file_extension';

    // SAVED REPORTS //
    const REPORT_ID = 'saved_report_id';
    const REPORT_NAME = 'report_name';
    const DATE_CREATED = 'date_created';
    const REPORT_DETAILS = 'report_details';
    const REPORT_SCHEDULE = 'report_schedule';
    const REPORT_LAST_SENT = 'report_last_sent_date';

    // USER SESSION //
    const SESSION_TOKEN = 'session_token';
    const USER_SESSION = 'user_session_value';
    const SESSION_LAST_MODIFIED = 'last_modified';

    // REMINDERS //
    const REMINDER_ID = 'reminder_id';
    const REMINDER_SENDTIME = 'sendtime';
    const REMINDER_MESSAGE = 'message';
    const REMINDER_USER_ID = 'user_id';
    const REMINDER_ADDRESS = 'address';
    const REMINDER_REFNUMBER = 'refnumber';
    const REMINDER_MINUTES_PRIOR = 'minutes_prior';
    const REMINDER_TYPE = 'reminder_type';

    // RESOURCE TYPE //
    const RESOURCE_TYPE_ID = 'resource_type_id';
    const RESOURCE_TYPE_NAME = 'resource_type_name';
    const RESOURCE_TYPE_DESCRIPTION = 'resource_type_description';

    // DBVERSION //
    const VERSION_NUMBER = 'version_number';
    const VERSION_DATE = 'version_date';

    // RESERVATION COLOR RULES //
    const REQUIRED_VALUE = 'required_value';
    const RESERVATION_COLOR = 'color';
    const RESERVATION_COLOR_RULE_ID = 'reservation_color_rule_id';
    const COLOR_ATTRIBUTE_TYPE = 'attribute_type';
    const COMPARISON_TYPE = 'comparison_type';
    const RESERVATION_COLOR_RULE_PRIORITY = 'priority';

    // CURRENT_CREDITS //
    const CREDIT_COUNT = 'credit_count';
    const PEAK_CREDIT_COUNT = 'peak_credit_count';
    const CREDIT_APPLICABILITY = 'credit_applicability';
    const CREDIT_NOTE = 'credit_note';
    const ORIGINAL_CREDIT_COUNT = 'original_credit_count';
    const CREDITS_CHARGED_ALL_SLOTS = 'credits_charged_all_slots';

    // PEAK TIMES //
    const PEAK_TIMES_ID = 'peak_times_id';
    const PEAK_ALL_DAY = 'all_day';
    const PEAK_START_TIME = 'start_time';
    const PEAK_END_TIME = 'end_time';
    const PEAK_EVERY_DAY = 'every_day';
    const PEAK_DAYS = 'peak_days';
    const PEAK_ALL_YEAR = 'all_year';
    const PEAK_BEGIN_MONTH = 'begin_month';
    const PEAK_BEGIN_DAY = 'begin_day';
    const PEAK_END_MONTH = 'end_month';
    const PEAK_END_DAY = 'end_day';

    // RESERVATION_WAITLIST_REQUEST_ID //
    const RESERVATION_WAITLIST_REQUEST_ID = 'reservation_waitlist_request_id';

    // PAYMENT CONFIGURATION //
    const CREDIT_COST = 'credit_cost';
    const CREDIT_CURRENCY = 'credit_currency';

    // PAYMENT GATEWAYS //
    const GATEWAY_TYPE = 'gateway_type';
    const GATEWAY_SETTING_NAME = 'setting_name';
    const GATEWAY_SETTING_VALUE = 'setting_value';

    // PAYMENT TRANSACTION LOG //
    const TRANSACTION_LOG_ID = 'payment_transaction_log_id';
    const TRANSACTION_LOG_STATUS = 'status';
    const TRANSACTION_LOG_INVOICE = 'invoice_number';
    const TRANSACTION_LOG_TRANSACTION_ID = 'transaction_id';
    const TRANSACTION_LOG_TOTAL = 'total_amount';
    const TRANSACTION_LOG_FEE = 'transaction_fee';
    const TRANSACTION_LOG_CURRENCY = 'currency';
    const TRANSACTION_LOG_TRANSACTION_HREF = 'transaction_href';
    const TRANSACTION_LOG_REFUND_HREF = 'refund_href';
    const TRANSACTION_LOG_GATEWAY_NAME = 'gateway_name';
    const TRANSACTION_LOG_GATEWAY_DATE = 'gateway_date_created';
    const TRANSACTION_LOG_REFUND_AMOUNT = 'refund_amount';

    // TERMS OF SERVICE //
    const TERMS_ID = 'terms_of_service_id';
    const TERMS_TEXT = 'terms_text';
    const TERMS_URL = 'terms_url';
    const TERMS_FILE = 'terms_file';
    const TERMS_APPLICABILITY = 'applicability';

    // RESOURCE RELATIONSHIPS //
    const RESOURCE_RELATIONSHIP_ID1 = 'resource_id';
    const RESOURCE_RELATIONSHIP_ID2 = 'related_resource_id';
    const RESOURCE_RELATIONSHIP_TYPE = 'relationship_type';

    // MONITOR VIEW //
    const MONITOR_VIEW_ID = 'monitor_view_id';
    const MONITOR_VIEW_NAME = 'monitor_view_name';
    const MONITOR_VIEW_PUBLIC_ID = 'public_id';
    const MONITOR_VIEW_SETTINGS = 'serialized_settings';

    // GROUP CREDIT REPLENISHMENT RULE //
    const GROUP_CREDIT_REPLENISHMENT_RULE_ID = 'group_credit_replenishment_rule_id';
    const GROUP_CREDIT_REPLENISHMENT_RULE_TYPE = 'type';
    const GROUP_CREDIT_REPLENISHMENT_RULE_AMOUNT = 'amount';
    const GROUP_CREDIT_REPLENISHMENT_RULE_DAY_OF_MONTH = 'day_of_month';
    const GROUP_CREDIT_REPLENISHMENT_RULE_INTERVAL = 'interval';
    const GROUP_CREDIT_REPLENISHMENT_RULE_LAST_DATE = 'last_replenishment_date';

    // PASSWORD RESET REQUEST //
    const PASSWORD_RESET_TOKEN = 'reset_token';

    // SMS //
    const SMS_OPT_IN_DATE = 'opt_in_date';
    const SMS_OPT_OUT_DATE = 'opt_out_date';
    const SMS_CONFIRMATION_CODE = 'confirmation_code';
    const USER_SMS_ID = 'user_sms_id';

    // RESOURCE MAP //
    const RESOURCE_MAP_NAME = 'name';
    const RESOURCE_MAP_STATUS = 'status_id';
    const RESOURCE_MAP_ID = 'resource_map_id';
    const RESOURCE_MAP_COORDINATES = 'coordinates';

    // OAUTH AUTHENTICATION PROVIDERS //
    const OAUTH_PROVIDER_ID = 'provider_id';
    const OAUTH_PROVIDER_NAME = 'provider_name';
    const OAUTH_CLIENT_ID = 'client_id';
    const OAUTH_CLIENT_SECRET = 'client_secret';
    const OAUTH_URL_AUTHORIZE = 'url_authorize';
    const OAUTH_URL_ACCESS_TOKEN = 'url_access_token';
    const OAUTH_URL_USER_DETAILS = 'url_user_details';
    const OAUTH_ACCESS_TOKEN_GRANT = 'access_token_grant';
    const OAUTH_FIELD_MAPPINGS = 'field_mappings';
    const OAUTH_SCOPE = 'scope';

    // dynamic
    const TOTAL = 'total';
    const TOTAL_TIME = 'totalTime';
    const OWNER_FIRST_NAME = 'owner_fname';
    const OWNER_LAST_NAME = 'owner_lname';
    const OWNER_FULL_NAME_ALIAS = 'owner_name';
    const OWNER_USER_ID = 'owner_id';
    const OWNER_PHONE = 'owner_phone';
    const OWNER_ORGANIZATION = 'owner_organization';
    const OWNER_POSITION = 'owner_position';
    const GROUP_NAME_ALIAS = 'group_name';
    const RESOURCE_NAME_ALIAS = 'resource_name';
    const RESOURCE_NAMES = 'resource_names';
    const SCHEDULE_NAME_ALIAS = 'schedule_name';
    const PARTICIPANT_LIST = 'participant_list';
    const INVITEE_LIST = 'invitee_list';
    const COOWNER_LIST = 'coowner_list';
    const ATTRIBUTE_LIST = 'attribute_list';
    const RESOURCE_ATTRIBUTE_LIST = 'resource_attribute_list';
    const RESOURCE_TYPE_ATTRIBUTE_LIST = 'resource_type_attribute_list';
    const USER_ATTRIBUTE_LIST = 'user_attribute_list';
    const RESOURCE_ACCESSORY_LIST = 'resource_accessory_list';
    const RESOURCE_GROUP_LIST = 'group_list';
    const GROUP_LIST = 'owner_group_list';
    const START_REMINDER_MINUTES_PRIOR = 'start_reminder_minutes';
    const END_REMINDER_MINUTES_PRIOR = 'end_reminder_minutes';
    const DURATION_ALIAS = 'duration';
    const DURATION_HOURS = 'duration_in_hours';
    const GROUP_IDS = 'group_ids';
    const RESOURCE_IDS = 'resource_ids';
    const GUEST_LIST = 'guest_list';
    const USER_GROUP_LIST = 'user_group_list';
    const GROUP_ROLE_LIST = 'group_role_list';
    const GROUP_LIMIT_ON_RESERVATION = 'limit_on_reservation';
    const UTILIZATION_TYPE = 'utilization_type';
    const DATE = 'date';
    const UTILIZATION = 'utilization';
    const RESOURCE_ADDITIONAL_PROPERTIES_ALIAS = 'resource_additional_properties';
    const ATTACHMENT_LIST = 'attachment_list';

    const APPROVER_FIRST_NAME = 'approver_fname';
    const APPROVER_LAST_NAME = 'approver_lname';
    const APPROVER_USER_ID = 'approver_id';
    const DATE_APPROVED = 'date_approved';

    // shared
    const ALLOW_CALENDAR_SUBSCRIPTION = 'allow_calendar_subscription';
    const PUBLIC_ID = 'public_id';
    const RESOURCE_ADMIN_GROUP_ID_RESERVATIONS = 'resource_admin_group_id';
    const SCHEDULE_ADMIN_GROUP_ID_RESERVATIONS = 'schedule_admin_group_id';

}