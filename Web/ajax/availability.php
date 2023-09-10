<?php
/**
 * Copyright 2018-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../../');

require_once(ROOT_DIR . 'Pages/SecurePage.php');
require_once(ROOT_DIR . 'Pages/Ajax/ReservationUserAvailabilityPage.php');

$config = Configuration::Instance();
$guestReservations = $config->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_ALLOW_GUEST_BOOKING, new BooleanConverter());
$viewReservations = $config->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_VIEW_RESERVATIONS, new BooleanConverter());

$page = new ReservationUserAvailabilityPage();
if (!$guestReservations && !$viewReservations) {
    $page = new SecurePageDecorator($page);
}
$page->PageLoad();