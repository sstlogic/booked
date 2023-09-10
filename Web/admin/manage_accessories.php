<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

define('ROOT_DIR', '../../');

require_once(ROOT_DIR . 'Pages/Admin/ManageAccessoriesPage.php');

$page = new AdminPageDecorator(new ManageAccessoriesPage());
$page->PageLoad();