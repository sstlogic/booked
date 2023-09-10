<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

define('ROOT_DIR', '../../');

require_once(ROOT_DIR . 'Pages/Admin/ManageGroupsPage.php');
require_once(ROOT_DIR . 'Pages/Ajax/AutoCompletePage.php');

$page = new AdminPageDecorator(new ManageGroupsPage());
$page->PageLoad();