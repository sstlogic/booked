<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

define('ROOT_DIR', '../');

require_once(ROOT_DIR . 'Pages/ProfilePage.php');

$page = new SecureActionPageDecorator(new ProfilePage());
$page->PageLoad();

