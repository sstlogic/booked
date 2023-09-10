<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/ActionPage.php');
require_once(ROOT_DIR . 'Pages/SecurePage.php');
require_once(ROOT_DIR . 'Presenters/Api/ReservationNavListApiPresenter.php');

interface IReservationNavListApiPage extends IActionPage
{
    /**
     * @param ReservationItemView[] $reservationsToday
     * @param ReservationItemView[] $reservationsTomorrow
     * @param int $numberWithinAnHour
     */
    public function BindReservations(array $reservationsToday, array $reservationsTomorrow, int $numberWithinAnHour, Date $dateToday, Date $dateTomorrow);
}

class ReservationNavListApiPage extends ActionPage implements IReservationNavListApiPage
{
    /**
     * @var ReservationNavListApiPresenter
     */
    private $presenter;

    public function __construct()
    {
        parent::__construct('');
        $this->presenter = new ReservationNavListApiPresenter($this, new ReservationViewRepository());
    }

    public function ProcessAction()
    {
    }

    public function ProcessDataRequest($dataRequest)
    {
    }

    public function ProcessPageLoad()
    {
        $this->presenter->PageLoad($this->server->GetUserSession());
    }

    public function BindReservations(array $reservationsToday, array $reservationsTomorrow, int $numberWithinAnHour, Date $dateToday, Date $dateTomorrow)
    {
        $this->Set('Today', $reservationsToday);
        $this->Set('Tomorrow', $reservationsTomorrow);
        $this->Set('WithinTheHour', $numberWithinAnHour);
        $this->Set('TotalUpcoming', count($reservationsToday) + count($reservationsTomorrow));
        $this->Set('DateToday', $dateToday);
        $this->Set('DateTomorrow', $dateTomorrow);
        $this->Display('Ajax/reservation-nav-list.tpl');
    }
}