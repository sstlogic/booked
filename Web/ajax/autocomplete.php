<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

define('ROOT_DIR', '../../');
require_once(ROOT_DIR . 'Pages/Ajax/AutoCompletePage.php');

$page = new AutoCompletePage();
$allowAnonSearch = ($page->GetType() != AutoCompleteType::Organization) ||
    (Configuration::Instance()->GetSectionKey(ConfigSection::TABLET_VIEW, ConfigKeys::TABLET_VIEW_AUTOCOMPLETE, new BooleanConverter()) ||
    !Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_HIDE_USER_DETAILS, new BooleanConverter()));

if (!$allowAnonSearch)
{
    $page = new SecurePageDecorator($page);
}
$page->PageLoad();

