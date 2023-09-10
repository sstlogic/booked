<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../../');
require_once(ROOT_DIR . 'Pages/Admin/ManageConfigurationPage.php');

$page = new AdminPageDecorator(new ManageConfigurationPage());
$page->PageLoad();