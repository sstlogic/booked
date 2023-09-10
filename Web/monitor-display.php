<?php
/**
 * Copyright 2018-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../');

require_once(ROOT_DIR . 'Pages/MonitorDisplayPage.php');

$page = new MonitorDisplayPage();
$page->PageLoad();