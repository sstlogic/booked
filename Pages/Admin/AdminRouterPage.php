<?php
/**
 * Copyright 2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/SecurePage.php');
require_once(ROOT_DIR . 'Pages/Admin/AdminPage.php');

class AdminRouterPage extends SecurePage {

    public function __construct($titleKey = '', $pageDepth = 0)
    {
        parent::__construct($titleKey, 1);
    }

    public function PageLoad()
    {
        $lastAdminPageId = intval($this->server->GetCookie(CookieKeys::LAST_ADMIN_PAGE));
        $pageIdToUrl = [
            AdminPageIds::Accessories => 'manage_accessories.php',
            AdminPageIds::Announcements => 'manage_announcements.php',
            AdminPageIds::Attributes => 'attributes',
            AdminPageIds::Blackouts => 'manage_blackouts.php',
            AdminPageIds::Groups => 'manage_groups.php',
            AdminPageIds::Payments => 'manage_payments.php',
            AdminPageIds::Quotas => 'quotas',
            AdminPageIds::Resources => 'resources',
            AdminPageIds::ResourceMaps => 'maps',
            AdminPageIds::Reservations => 'manage_reservations.php',
            AdminPageIds::ReservationColors => 'reservations/colors',
            AdminPageIds::ReservationSettings => 'manage_reservation_settings.php',
            AdminPageIds::ReservationWaitlist => 'manage_reservation_waitlist.php',
            AdminPageIds::Schedules => 'manage_schedules.php',
            AdminPageIds::Users => 'manage_users.php',
        ];

        $url = 'manage_reservations.php';
        if (array_key_exists($lastAdminPageId, $pageIdToUrl)) {
            $url = $pageIdToUrl[$lastAdminPageId];
        }

        $this->Redirect($this->path . 'admin/' . $url);
    }
}
