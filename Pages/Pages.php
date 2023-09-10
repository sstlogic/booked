<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/UrlPaths.php');

class Pages
{
    const ID_DASHBOARD = 1;
    const ID_LOGIN = 5;

    const DEFAULT_HOMEPAGE_ID = self::ID_DASHBOARD;

    const ACCESSORY_QR_ROUTER = 'accessory-qr-router.php';
    const ACTIVATION = 'activate.php';
    const CALENDAR = 'calendar.php';
    const CALENDAR_EXPORT = 'ical.php';
    const CALENDAR_SUBSCRIBE = 'ical-subscribe.php';
    const CALENDAR_SUBSCRIBE_ATOM = 'atom-subscribe.php';
    const CHECKOUT = 'checkout.php';
    const CREDITS = 'credits.php';
    const DASHBOARD = 'dashboard.php';
    const DISPLAY_RESOURCE = 'resource-display.php';
    const DEFAULT_LOGIN = 'dashboard.php';
    const GUEST_INVITATION_RESPONSES = 'guest-participation.php';
    const FIRST_LOGIN = 'first-login.php';
    const FORGOT_PASSWORD = 'forgot.php';
    const GUEST_RESERVATION = 'guest-reservation.php';
    const INVITATION_RESPONSES = 'participation.php';
    const LOGIN = 'index.php';
    const MANAGE_RESERVATIONS = 'manage_reservations.php';
    const MANAGE_RESOURCES = 'manage_resources.php';
    const MANAGE_SCHEDULES = 'manage_schedules.php';
    const MANAGE_CONFIGURATION = 'manage_configuration.php';
    const MANAGE_GROUPS = 'manage_groups.php';
    const MANAGE_GROUPS_ADMIN = 'manage_admin_groups.php';
    const MANAGE_GROUP_RESERVATIONS = 'manage_group_reservations.php';
    const MY_CALENDAR = 'my-calendar.php';
    const OPENINGS = 'search-availability.php';
    const NOTIFICATION_PREFERENCES = 'notification-preferences.php';
    const PARTICIPATION = 'participation.php';
    const PASSWORD = 'password.php';
    const PROFILE = 'profile.php';
    const REPORTS_GENERATE = 'generate-report.php';
    const REPORTS_SAVED = 'saved-reports.php';
    const REPORTS_COMMON = 'common-reports.php';
    const RESERVATION = 'reservation.php';
    const RESERVATION_FILE = 'reservation-file.php';
    const RESOURCE_MAPS = 'resource-qr-router.php';
    const RESOURCE_QR_ROUTER = 'resource-qr-router.php';
    const REGISTRATION = 'register.php';
    const RESET_PASSWORD = 'reset-password.php';
    const SCHEDULE = 'schedule.php';
    const SEARCH_RESERVATIONS = 'search-reservations.php';
    const VIEW_CALENDAR = 'view-calendar.php';
    const VIEW_SCHEDULE = 'view-schedule.php';
    const WAITLIST = 'waitlist.php';

    private static $_pages = [
        1 => ['url' => Pages::DASHBOARD, 'name' => 'Dashboard'],
        2 => ['url' => Pages::SCHEDULE, 'name' => 'Schedule'],
        3 => ['url' => Pages::MY_CALENDAR, 'name' => 'MyCalendar'],
        4 => ['url' => Pages::CALENDAR, 'name' => 'ResourceCalendar'],
        5 => ['url' => Pages::LOGIN, 'name' => 'Login'],
    ];

    private function __construct()
    {
    }

    public static function UrlFromId($pageId)
    {
        if (isset(self::$_pages[$pageId])) {
            return self::$_pages[$pageId]['url'];
        }
        return Pages::DASHBOARD;
    }

    public static function NameFromId($pageId)
    {
        if (isset(self::$_pages[$pageId])) {
            return self::$_pages[$pageId]['name'];
        }
        return "Dashboard";
    }

    public static function GetAvailablePages()
    {
    	$pages = array();
    	foreach(self::$_pages as $key => $page) {
    		if ($key != Pages::ID_LOGIN) {
    			$pages[$key] = $page;
			}
		}
    	
    	return $pages;
    }
}
