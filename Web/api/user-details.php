<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../../');
require_once (ROOT_DIR . 'Pages/Api/UserDetailsApiPage.php');

$page = new UserDetailsApiPage();
$page->PageLoad();