<?php
/**
 * Copyright 2018-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../../');

require_once (ROOT_DIR . 'Pages/Integrate/SlackPage.php');

$page = new SlackPage();
$page->PageLoad();