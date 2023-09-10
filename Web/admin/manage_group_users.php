<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../../');

require_once(ROOT_DIR . 'Pages/Admin/GroupAdminManageUsersPage.php');
require_once(ROOT_DIR . 'lib/Application/Admin/namespace.php');

$page = new RoleRestrictedPageDecorator(new GroupAdminManageUsersPage(), array(RoleLevel::GROUP_ADMIN));
$page->PageLoad();