<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../../');
require_once(ROOT_DIR . 'Pages/Authentication/ConfirmAccountPage.php');

$page = new SecureActionPageDecorator(new ConfirmAccountPage());
$page->PageLoad();