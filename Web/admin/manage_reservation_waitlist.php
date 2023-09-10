<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../../');

require_once(ROOT_DIR . 'Pages/Admin/ManageReservationWaitlistPage.php');

$page = new RoleRestrictedPageDecorator(new ManageReservationWaitlistPage(), RoleLevel::All());
$page->PageLoad();