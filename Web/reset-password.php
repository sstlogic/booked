<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../');

require_once(ROOT_DIR . 'Pages/Authentication/ResetPasswordPage.php');

$page = new ResetPasswordPage();
$page->PageLoad();