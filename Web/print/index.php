<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../../');

require_once(ROOT_DIR . 'Pages/Print/PrintReservationsPage.php');

$page = new SecureActionPageDecorator(new PrintReservationsPage());
$page->PageLoad();