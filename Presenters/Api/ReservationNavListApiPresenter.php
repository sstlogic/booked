<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'lib/Config/namespace.php');
require_once(ROOT_DIR . 'lib/Server/namespace.php');
require_once(ROOT_DIR . 'lib/Common/namespace.php');
require_once(ROOT_DIR . 'Domain/namespace.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'Pages/Api/ReservationNavListApiPage.php');

class ReservationNavListApiPresenter extends ActionPresenter
{
    /**
     * @var IReservationNavListApiPage
     */
    private $page;
    /**
     * @var IReservationViewRepository
     */
    private $reservationViewRepository;

    public function __construct(IReservationNavListApiPage $page, IReservationViewRepository $reservationViewRepository)
    {
        parent::__construct($page);
        $this->page = $page;
        $this->reservationViewRepository = $reservationViewRepository;
    }

    public function PageLoad(UserSession $userSession)
    {
        $tz = $userSession->Timezone;
        $ownReservations = $this->reservationViewRepository->GetReservations(Date::Now(), Date::Now()->AddDays(1), $userSession->UserId, ReservationUserLevel::OWNER, null, null, true);

        $now = Date::Now()->ToTimezone($tz);
        $oneHour = $now->AddHours(1);
        $today = Date::Now()->ToTimezone($tz);
        $tomorrow = $today->GetDate()->AddDays(1);

        $reservationsToday = [];
        $reservationsTomorrow = [];
        $numberWithinAnHour = 0;

        foreach ($ownReservations as $r) {

            if ($r->StartDate->LessThanOrEqual($oneHour)) {
                $numberWithinAnHour++;
            }
            if ($r->DateRange()->OccursOn($today)) {
                $reservationsToday[] = $r;
            }
            if ($r->DateRange()->OccursOn($tomorrow)) {
                $reservationsTomorrow[] = $r;
            }
        }

        $this->page->BindReservations($reservationsToday, $reservationsTomorrow, $numberWithinAnHour, $today, $tomorrow);
    }
}