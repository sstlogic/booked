<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../../');
require_once (ROOT_DIR . 'Pages/Api/ClientErrorApiPage.php');

$page = new ClientErrorApiPage();
$page->PageLoad();
