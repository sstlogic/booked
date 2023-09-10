<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../../../');

require_once(ROOT_DIR . 'Pages/Admin/ManageAttributesPage.php');
require_once(ROOT_DIR . 'Presenters/Admin/ManageAttributesPresenter.php');

$page = new AdminPageDecorator(new ManageAttributesPage());
$page->PageLoad();