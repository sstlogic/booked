<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../');

require_once(ROOT_DIR . 'Pages/Authentication/ExternalAuthLoginPage.php');
require_once(ROOT_DIR . 'Presenters/Authentication/ExternalAuthLoginPresenter.php');

$page = new ExternalAuthLoginPage();
$page->PageLoad();