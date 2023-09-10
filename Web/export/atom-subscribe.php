<?php
/**
Copyright 2013-2023 Twinkle Toes Software, LLC
*/

define('ROOT_DIR', '../../');

require_once(ROOT_DIR . 'Pages/Export/AtomSubscriptionPage.php');

$page = new AtomSubscriptionPage();
$page->PageLoad();