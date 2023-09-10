<?php
/**
Copyright 2017-2023 Twinkle Toes Software, LLC
*/

define('ROOT_DIR', '../../');

require(ROOT_DIR . 'Pages/Ajax/UserDetailsPopupPage.php');

$page = new UserDetailsPopupPage();
$page->PageLoad();
