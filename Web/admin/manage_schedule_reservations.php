<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

define('ROOT_DIR', '../../');

require_once(ROOT_DIR . 'Pages/Admin/ScheduleAdminManageReservationsPage.php');

$page = new RoleRestrictedPageDecorator(new ScheduleAdminManageReservationsPage(), array(RoleLevel::SCHEDULE_ADMIN));
$page->PageLoad();