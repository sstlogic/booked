<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

define('ROOT_DIR', '../../');

require(ROOT_DIR . 'Pages/Ajax/ReservationPopupPage.php');

$page = new ReservationPopupPage();
$page->PageLoad();