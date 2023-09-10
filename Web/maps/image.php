<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../../');
require_once(ROOT_DIR . 'Pages/Maps/MapImagePage.php');

$page = new MapImagePage();
$page->PageLoad();