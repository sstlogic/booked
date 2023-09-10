<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../');

require_once(ROOT_DIR . 'Pages/Search/SearchReservationsPage.php');

$page = new SecurePageDecorator(new SearchReservationsPage());
$page->PageLoad();