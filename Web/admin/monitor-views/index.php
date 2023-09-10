<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../../../');

require_once(ROOT_DIR . 'Pages/Admin/ManageMonitorViewsPage.php');
require_once(ROOT_DIR . 'Presenters/Admin/ManageMonitorViewsPresenter.php');

$page = new AdminPageDecorator(new ManageMonitorViewsPage());
$page->PageLoad();