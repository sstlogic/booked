<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once ROOT_DIR . 'Pages/ActionPage.php';
require_once ROOT_DIR . 'Pages/SecurePage.php';
require_once ROOT_DIR . 'Pages/Print/IPrintReservationsPage.php';
require_once ROOT_DIR . 'Presenters/Print/PrintReservationsPresenter.php';

class PrintReservationsPage extends ActionPage implements IPrintReservationsPage
{
    /**
     * @var PrintReservationsPresenter
     */
    private $presenter;

    public function __construct()
    {
        parent::__construct("PrintReservations", 1);
        $resourceService = ResourceService::Create();
        $reservationService = new ReservationService(new ReservationViewRepository(), new ReservationListingFactory());
        $scheduleService = new ScheduleService(new ScheduleRepository(), $resourceService, new DailyLayoutFactory());
        $privacyFilter = new PrivacyFilter(new ReservationAuthorization(PluginManager::Instance()->LoadAuthorization()));
        $this->presenter = new PrintReservationsPresenter($this, $resourceService, $reservationService, $scheduleService, $privacyFilter);
    }

    public function ProcessAction()
    {
    }

    public function ProcessDataRequest($dataRequest)
    {
    }

    public function ProcessPageLoad()
    {
        $this->Set('NoResources', false);
        $this->presenter->PageLoad($this->server->GetUserSession());
        $this->Display('Print/reservation-list.tpl');
    }

    public function BindResources($resources)
    {
        $this->Set('resources', $resources);
    }

    public function SetDate(Date $date)
    {
        $this->Set('selectedDate', $date);
    }

    public function SetVisibleHours($start, $end)
    {
        $hours = [];
        for ($h = $start; $h < $end; $h++) {
            $hours[] = $h;
        }
        $this->Set('visibleHours', $hours);
    }

    /**
     * @param ReservationListItem[] $reservations
     * @return void
     */
    public function SetReservations(array $reservations)
    {
        $this->Set('reservations', $reservations);
    }

    /**
     * @return string
     */
    public function GetResourceId()
    {
        return $this->GetQuerystring(QueryStringKeys::RESOURCE_ID);
    }

    /**
     * @return string
     */
    public function GetScheduleId()
    {
        return $this->GetQuerystring(QueryStringKeys::SCHEDULE_ID);
    }

    /**
     * @return string
     */
    public function GetDate()
    {
        return $this->GetQuerystring(QueryStringKeys::RESERVATION_DATE);
    }

    public function SetNoResources($noResources)
    {
        $this->Set('noResources', $noResources);
    }

    public function SetResources($selectedResources, $ids)
    {
        $this->Set('selectedResources', $selectedResources);
        $this->Set('selectedResourceIds', $ids);
    }

    /**
     * @param Schedule[] $schedules
     * @param Schedule $selectedSchedule
     */
    public function SetSchedules($schedules, $selectedSchedule)
    {
        $this->Set('schedules', $schedules);
        $this->Set('selectedSchedule', $selectedSchedule);
    }
}