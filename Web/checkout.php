<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../');
require_once(ROOT_DIR . 'Pages/Credits/CheckoutPage.php');

$page = new SecurePageDecorator(new CheckoutPage());
$page->PageLoad();
