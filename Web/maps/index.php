<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../../');
require_once(ROOT_DIR . 'Pages/Maps/ResourceMapsPage.php');

$page = new SecureActionPageDecorator(new ResourceMapsPage());
$page->PageLoad();