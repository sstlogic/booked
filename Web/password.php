<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

define('ROOT_DIR', '../');

require_once(ROOT_DIR . 'Pages/PasswordPage.php');

$page = new PasswordPage();
$page->PageLoad();

