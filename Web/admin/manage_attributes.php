<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
*/

define('ROOT_DIR', '../../');

require_once(ROOT_DIR . 'Pages/Admin/ManageAttributesPage.php');

$page = new AdminPageDecorator(new ManageAttributesPage());
$page->PageLoad();
