<?php
/**
 * Copyright 2018-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../');

require_once(ROOT_DIR . 'Pages/ResourceQRRouterPage.php');

$page = new ResourceQRRouterPage();
$page->PageLoad();