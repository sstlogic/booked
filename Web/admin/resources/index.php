<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../../../');

require_once(ROOT_DIR . 'Pages/Admin/ManageResourcesPage.php');
require_once(ROOT_DIR . 'Presenters/Admin/ManageResourcesPresenter.php');
require_once(ROOT_DIR . 'Pages/Admin/ResourceAdminManageResourcesPage.php');

if (ServiceLocator::GetServer()->GetUserSession()->IsAdmin) {
    $page = new AdminPageDecorator(new ManageResourcesPage());
} else {
    $page = new RoleRestrictedPageDecorator(new ResourceAdminManageResourcesPage(), [RoleLevel::APPLICATION_ADMIN, RoleLevel::RESOURCE_ADMIN, RoleLevel::SCHEDULE_ADMIN]);
}

$page->PageLoad();