<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../../../');

require_once(ROOT_DIR . 'Pages/Admin/ManageQuotasPage.php');
require_once(ROOT_DIR . 'Presenters/Admin/ManageQuotasPresenter.php');

$page = new AdminPageDecorator(new ManageQuotasPage());
$page->PageLoad();