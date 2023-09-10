<?php
/**
 * Copyright 2018-2023 Twinkle Toes Software, LLC
 */

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
} else {
    header('Access-Control-Allow-Origin: *');
}
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Origin, Authorization');
header('Vary: Origin');
header('Access-Control-Max-Age: 30');

if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    return;
}

define('ROOT_DIR', '../../');

require_once(ROOT_DIR . 'Pages/Export/EmbeddedCalendarPage.php');

$page = new EmbeddedCalendarPage();
$page->PageLoad();
