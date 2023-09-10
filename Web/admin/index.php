<?php
/**
 * Copyright 2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../../');
require_once(ROOT_DIR . 'Pages/Admin/AdminRouterPage.php');

$page = new AdminRouterPage();
$page->PageLoad();