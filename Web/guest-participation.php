<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../');
require_once(ROOT_DIR . 'Pages/GuestParticipationPage.php');

$page = new GuestParticipationPage();
$page->PageLoad();