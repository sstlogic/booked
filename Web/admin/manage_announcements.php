<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

define('ROOT_DIR', '../../');

require_once(ROOT_DIR . 'Pages/Admin/ManageAnnouncementsPage.php');

$page = new RoleRestrictedPageDecorator(new ManageAnnouncementsPage(), RoleLevel::All());
$page->PageLoad();