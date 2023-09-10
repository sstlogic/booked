<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

define('ROOT_DIR', '../../');

require_once(ROOT_DIR . 'Pages/Admin/ScheduleAdminManageSchedulesPage.php');
require_once(ROOT_DIR . 'Presenters/Admin/ManageSchedulesPresenter.php');

$page = new RoleRestrictedPageDecorator(new ScheduleAdminManageSchedulesPage(), array(RoleLevel::SCHEDULE_ADMIN));
$page->PageLoad();