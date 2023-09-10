<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

define('ROOT_DIR', '../../');

require_once(ROOT_DIR . 'Pages/Admin/ResourceAdminManageReservationsPage.php');

$page = new RoleRestrictedPageDecorator(new ResourceAdminManageReservationsPage(), array(RoleLevel::RESOURCE_ADMIN));
$page->PageLoad();