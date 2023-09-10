<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

define('ROOT_DIR', '../../');

require_once(ROOT_DIR . 'Pages/Admin/GroupAdminManageGroupsPage.php');
require_once(ROOT_DIR . 'Pages/Ajax/AutoCompletePage.php');

$page =  new RoleRestrictedPageDecorator(new GroupAdminManageGroupsPage(), array(RoleLevel::GROUP_ADMIN));
$page->PageLoad();