<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../../');
require_once(ROOT_DIR . 'Pages/Reports/GenerateReportPage.php');

$roles = [RoleLevel::APPLICATION_ADMIN, RoleLevel::GROUP_ADMIN, RoleLevel::RESOURCE_ADMIN, RoleLevel::SCHEDULE_ADMIN];
if (Configuration::Instance()->GetSectionKey(ConfigSection::REPORTS, ConfigKeys::REPORTS_ALLOW_ALL, new BooleanConverter())) {
    $roles = [];
}
else if (Configuration::Instance()->GetSectionKey(ConfigSection::REPORTS, ConfigKeys::REPORTS_RESTRICT_TO_ADMINS, new BooleanConverter())) {
    $roles = [RoleLevel::APPLICATION_ADMIN, RoleLevel::GROUP_ADMIN, RoleLevel::SCHEDULE_ADMIN, RoleLevel::RESOURCE_ADMIN];
}

$page = new RoleRestrictedPageDecorator(new GenerateReportPage(), $roles);
$page->PageLoad();

