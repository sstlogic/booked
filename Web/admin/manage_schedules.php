<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

define('ROOT_DIR', '../../');

require_once(ROOT_DIR . 'Pages/Admin/ManageSchedulesPage.php');
require_once(ROOT_DIR . 'Presenters/Admin/ManageSchedulesPresenter.php');

$page = new AdminPageDecorator(new ManageSchedulesPage());
$page->PageLoad();