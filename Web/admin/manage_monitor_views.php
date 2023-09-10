<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

define('ROOT_DIR', '../../');

require_once(ROOT_DIR . 'Pages/Admin/ManageMonitorViewsPage.php');
require_once(ROOT_DIR . 'Presenters/Admin/ManageMonitorViewsPresenter.php');

$page = new AdminPageDecorator(new ManageMonitorViewsPage(1));
$page->PageLoad();