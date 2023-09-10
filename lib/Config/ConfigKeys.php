<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

class ConfigKeys
{
    const ADMIN_EMAIL = 'admin.email';
    const ADMIN_EMAIL_NAME = 'admin.email.name';
    const ALLOW_REGISTRATION = 'allow.self.registration';
    const CREDITS_ENABLED = 'enabled';
    const CREDITS_ALLOW_PURCHASE = 'allow.purchase';
    const CSS_EXTENSION_FILE = 'css.extension.file';
    const DEFAULT_HOMEPAGE = 'default.homepage';
    const DISABLE_PASSWORD_RESET = 'disable.password.reset';
    const ENABLE_EMAIL = 'enable.email';
    const HOME_URL = 'home.url';
    const INACTIVITY_TIMEOUT = 'inactivity.timeout';
    const LANGUAGE = 'default.language';
    const LOGOUT_URL = 'logout.url';
    const NAME_FORMAT = 'name.format';
    const SCRIPT_URL = 'script.url';
    const DEFAULT_TIMEZONE = 'default.timezone';
    const REGISTRATION_ENABLE_CAPTCHA = 'registration.captcha.enabled';
    const REGISTRATION_REQUIRE_ACTIVATION = 'registration.require.email.activation';
    const REGISTRATION_AUTO_SUBSCRIBE_EMAIL = 'registration.auto.subscribe.email';
    const REGISTRATION_NOTIFY = 'registration.notify.admin';
    const FIRST_DAY_OF_WEEK = 'first.day.of.week';

    const VERSION = 'version';

    const SCHEDULE_SHOW_INACCESSIBLE_RESOURCES = 'show.inaccessible.resources';
    const SCHEDULE_RESERVATION_LABEL = 'reservation.label';
    const SCHEDULE_HIDE_BLOCKED_PERIODS = 'hide.blocked.periods';
    const SCHEDULE_UPDATE_HIGHLIGHT_MINUTES = 'update.highlight.minutes';
    const SCHEDULE_SHOW_WEEK_NUMBERS = 'show.week.numbers';
    const SCHEDULE_SHOW_CHECKIN = 'show.checkin.information';
    const SESSION_SAVE_PATH = 'session.save.path';

    const DATABASE_TYPE = 'type';
    const DATABASE_USER = 'user';
    const DATABASE_PASSWORD = 'password';
    const DATABASE_HOSTSPEC = 'hostspec';
    const DATABASE_NAME = 'name';

    const PLUGIN_AUTHENTICATION = 'Authentication';
    const PLUGIN_AUTHORIZATION = 'Authorization';
    const PLUGIN_PERMISSION = 'Permission';
    const PLUGIN_POSTREGISTRATION = 'PostRegistration';
    const PLUGIN_PRERESERVATION = 'PreReservation';
    const PLUGIN_POSTRESERVATION = 'PostReservation';

    const RESERVATION_START_TIME_CONSTRAINT = 'start.time.constraint';
    const RESERVATION_UPDATES_REQUIRE_APPROVAL = 'updates.require.approval';
    const RESERVATION_PREVENT_PARTICIPATION = 'prevent.participation';
    const RESERVATION_PREVENT_RECURRENCE = 'prevent.recurrence';
    const RESERVATION_REMINDERS_ENABLED = 'enable.reminders';
    const RESERVATION_ALLOW_GUESTS = 'allow.guest.participation';
    const RESERVATION_ALLOW_WAITLIST = 'allow.wait.list';
    const RESERVATION_CHECKIN_MINUTES = 'checkin.minutes.prior';
    const RESERVATION_START_REMINDER = 'default.start.reminder';
    const RESERVATION_END_REMINDER = 'default.end.reminder';
    const RESERVATION_TITLE_REQUIRED = 'title.required';
    const RESERVATION_DESCRIPTION_REQUIRED = 'description.required';
    const RESERVATION_DELETE_REASON_REQUIRED = 'cancel.reason.required';
    const RESERVATION_TOTAL_USER_CONCURRENT_LIMIT = 'user.total.concurrent.limit';
    const RESERVATION_SHOW_DETAILED_SAVE_RESPONSE = 'show.detailed.save.information';
    const RESERVATION_MAXIMUM_RESOURCE_CHECKLIST = 'maximum.resource.checklist';
    const RESERVATION_HIDE_EMAIL = 'hide.email.address';
    const RESERVATION_LIMIT_INVITEES_TO_MAX_PARTICIPANTS = 'limit.invitees.to.max.participants';
    const RESERVATION_ALLOW_MEETING_LINKS = 'allow.meeting.links';
    const RESERVATION_DEFAULT_ALLOW_PARTICIPATION_JOIN = 'default.allow.others.to.join';

    const IMAGE_UPLOAD_DIRECTORY = 'image.upload.directory';
    const IMAGE_UPLOAD_URL = 'image.upload.url';

    const CACHE_TEMPLATES = 'cache.templates';

    const USE_LOCAL_JS = 'use.local.js.libs';

    const INSTALLATION_PASSWORD = 'install.password';

    const ICS_SUBSCRIPTION_KEY = 'subscription.key';
    const ICS_FUTURE_DAYS = 'future.days';
    const ICS_PAST_DAYS = 'past.days';

    const PRIVACY_HIDE_USER_DETAILS = 'hide.user.details';
    const PRIVACY_ALWAYS_SHOW_USER_NAME = 'always.show.user.name';
    const PRIVACY_HIDE_RESERVATION_DETAILS = 'hide.reservation.details';
    const PRIVACY_VIEW_RESERVATIONS = 'view.reservations';
    const PRIVACY_VIEW_SCHEDULES = 'view.schedules';
    const PRIVACY_ALLOW_GUEST_BOOKING = 'allow.guest.reservations';
//    const PRIVACY_HIDE_USER_EMAIL = 'hide.user.email';

    const NOTIFY_CREATE_RESOURCE_ADMINS = 'resource.admin.add';
    const NOTIFY_CREATE_APPLICATION_ADMINS = 'application.admin.add';
    const NOTIFY_CREATE_GROUP_ADMINS = 'group.admin.add';

    const NOTIFY_UPDATE_RESOURCE_ADMINS = 'resource.admin.update';
    const NOTIFY_UPDATE_APPLICATION_ADMINS = 'application.admin.update';
    const NOTIFY_UPDATE_GROUP_ADMINS = 'group.admin.update';

    const NOTIFY_DELETE_RESOURCE_ADMINS = 'resource.admin.delete';
    const NOTIFY_DELETE_APPLICATION_ADMINS = 'application.admin.delete';
    const NOTIFY_DELETE_GROUP_ADMINS = 'group.admin.delete';

    const NOTIFY_APPROVAL_RESOURCE_ADMINS = 'resource.admin.approval';
    const NOTIFY_APPROVAL_APPLICATION_ADMINS = 'application.admin.approval';
    const NOTIFY_APPROVAL_GROUP_ADMINS = 'group.admin.approval';

    const NOTIFY_MISSED_CHECKIN_RESOURCE_ADMINS = 'resource.admin.missed.checkin';
    const NOTIFY_MISSED_CHECKIN_APPLICATION_ADMINS = 'application.admin.missed.checkin';
    const NOTIFY_MISSED_CHECKIN_GROUP_ADMINS = 'group.admin.missed.checkin';

    const NOTIFY_MISSED_CHECKOUT_RESOURCE_ADMINS = 'resource.admin.missed.checkout';
    const NOTIFY_MISSED_CHECKOUT_APPLICATION_ADMINS = 'application.admin.missed.checkout';
    const NOTIFY_MISSED_CHECKOUT_GROUP_ADMINS = 'group.admin.missed.checkout';

    const UPLOAD_ENABLE_RESERVATION_ATTACHMENTS = 'enable.reservation.attachments';
    const UPLOAD_RESERVATION_ATTACHMENTS = 'reservation.attachment.path';
    const UPLOAD_RESERVATION_EXTENSIONS = 'reservation.attachment.extensions';

    const PAGES_ENABLE_CONFIGURATION = 'enable.configuration';

    const API_ENABLED = 'enabled';
    const RECAPTCHA_ENABLED = 'enabled';
    const RECAPTCHA_PUBLIC_KEY = 'public.key';
    const RECAPTCHA_PRIVATE_KEY = 'private.key';
    const RECAPTCHA_VERSION = 'recaptcha.version';

    const DEFAULT_FROM_ADDRESS = 'default.from.address';
    const DEFAULT_FROM_NAME = 'default.from.name';

    const REPORTS_ALLOW_ALL = 'allow.all.users';
    const REPORTS_RESTRICT_TO_ADMINS = 'restrict.to.application.admins';

    const APP_TITLE = 'app.title';

    const SCHEDULE_PER_USER_COLORS = 'use.per.user.colors';

    const PASSWORD_UPPER_AND_LOWER = 'upper.and.lower';
    const PASSWORD_LETTERS = 'minimum.letters';
    const PASSWORD_NUMBERS = 'minimum.numbers';

    const RESERVATION_LABELS_ICS_SUMMARY = 'ics.summary';
    const RESERVATION_LABELS_ICS_DESCRIPTION = 'ics.description';
    const RESERVATION_LABELS_MY_ICS_SUMMARY = 'ics.my.summary';
    const RESERVATION_LABELS_MY_ICS_DESCRIPTION = 'ics.my.description';
    const RESERVATION_LABELS_RSS_DESCRIPTION = 'rss.description';
    const RESERVATION_LABELS_MY_CALENDAR = 'my.calendar';
    const RESERVATION_LABELS_RESOURCE_CALENDAR = 'resource.calendar';
    const RESERVATION_LABELS_RESERVATION_POPUP = 'reservation.popup';

    const SECURITY_HEADERS = 'security.headers';
    const SECURITY_STRICT_TRANSPORT = 'security.strict-transport';
    const SECURITY_X_FRAME = 'security.x-frame';
    const SECURITY_X_XSS = 'security.x-xss';
    const SECURITY_X_CONTENT_TYPE = 'security.x-content-type';
    const SECURITY_CONTENT_SECURITY_POLICY = 'security.content-security-policy';

    const GOOGLE_ANALYTICS_TRACKING_ID = 'tracking.id';

    const AUTHENTICATION_ALLOW_FACEBOOK = 'allow.facebook.login';
    const AUTHENTICATION_ALLOW_GOOGLE = 'allow.google.login';
    const AUTHENTICATION_REQUIRED_EMAIL_DOMAINS = 'required.email.domains';
    const AUTHENTICATION_HIDE_BOOKED_LOGIN_PROMPT = 'hide.booked.login.prompt';
    const AUTHENTICATION_CAPTCHA_ON_LOGIN = 'captcha.on.login';
    const AUTHENTICATION_ALLOW_OAUTH = 'allow.oauth.login';

    const SLACK_TOKEN = 'token';

    const TABLET_VIEW_ALLOW_GUESTS = 'allow.guest.reservations';
    const TABLET_VIEW_AUTOCOMPLETE = 'auto.suggest.emails';

    const USE_DATABASE_SESSION = 'use.database.session';

    const REGISTRATION_REQUIRE_PHONE = 'require.phone';
    const REGISTRATION_REQUIRE_ORGANIZATION = 'require.organization';
    const REGISTRATION_REQUIRE_POSITION = 'require.position';
    const REGISTRATION_LOCK_TIMEZONE = 'lock.timezone';
    const REGISTRATION_DISABLE_PROFILE_UPDATES = 'profile.updates.disabled';
    const REGISTRATION_BYPASS_FIRST_LOGIN_SCREEN = 'bypass.first.login.screen';

    const JOBS_PHP_EXEC_PATH = 'php.exec.path';

    const MFA_TYPE = 'mfa.type';

    const HELP_URL = 'help.url';

    const SMS_CLIENT_ID = 'sms.client.id';
    const SMS_SECRET_KEY = 'sms.secret.key';
    const SMS_API_URL = 'sms.api.url';

    const MAPS_ENABLED = 'enable.maps';

    const LOGGING_DIR = 'log.dir';
    const LOGGING_LEVEL = 'log.level';
}

class ConfigSection
{
    const API = 'api';
    const AUTHENTICATION = 'authentication';
    const CREDITS = 'credits';
    const DATABASE = 'database';
    const EMAIL = 'email';
    const ICS = 'ics';
    const PAGES = 'pages';
    const PASSWORD = 'password';
    const PLUGINS = 'plugins';
    const PRIVACY = 'privacy';
    const REPORTS = 'reports';
    const RESERVATION = 'reservation';
    const RESERVATION_LABELS = 'reservation.labels';
    const RESERVATION_NOTIFY = 'reservation.notify';
    const SCHEDULE = 'schedule';
    const SECURITY = 'security';
    const UPLOADS = 'uploads';
    const RECAPTCHA = 'recaptcha';
    const USERS = 'users';
    const GOOGLE_ANALYTICS = 'google.analytics';
    const PAYMENTS = 'payments';
    const SLACK = 'slack';
    const TABLET_VIEW = 'tablet.view';
    const REGISTRATION = 'registration';
    const JOBS = 'jobs';
    const MFA = 'mfa';
    const SMS = 'sms';
    const MAPS = 'resource.maps';

    const LOGGING = 'logging';
}