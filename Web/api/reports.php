<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../../');
require_once(ROOT_DIR . 'Pages/Api/ReportsApiPage.php');

$page = new SecureActionPageDecorator(new ReportsApiPage());
$page->PageLoad();