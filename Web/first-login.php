<?php
/**
Copyright 2021-2023 Twinkle Toes Software, LLC
*/

define('ROOT_DIR', '../');

require_once(ROOT_DIR . 'Pages/Authentication/FirstLoginPage.php');

$page = new FirstLoginPage();
$page->PageLoad();