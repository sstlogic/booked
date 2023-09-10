<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

define('ROOT_DIR', '../../');

require(ROOT_DIR . 'Pages/Ajax/ResourceDetailsPage.php');

$page = new ResourceDetailsPage();
$page->PageLoad();