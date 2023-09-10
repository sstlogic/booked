<?php

/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/MonitorDisplayPresenter.php');

interface IMonitorDisplayPage extends IPage, IActionPage
{
    /**
     * @param ReservationListItem[] $items
     */
    public function BindReservations($items, $displayRange);

    /**
     * @return LoadReservationRequest
     */
    public function GetReservationRequest();

    /**
     * @return string
     */
    public function GetMonitorViewId();

    /**
     * @return string
     */
    public function GetLastMonitorPage();

    /**
     * @param string $id
     */
    public function Init($id);

    public function BindGrid(DateRange $range, IDailyLayout $layout, $resources, MonitorView $view);

    /**
     * @param ReservationListItem[] $reservations
     * @param MonitorView $view
     * @param string $page
     * @param string $timezone
     */
    public function BindList($reservations, MonitorView $view, $page, $timezone);

    public function BindWeek(DateRange $range, $weekdayStart, IReservationListing $listing);

    public function BindAnnouncement(MonitorView $view);

    public function BindError();
}

class MonitorDisplayPage extends ActionPage implements IMonitorDisplayPage
{
    /**
     * @var MonitorDisplayPresenter
     */
    private $presenter;

    public function __construct()
    {
        parent::__construct('Schedule');
        $resourceService = new ResourceService(new ResourceRepository(), new GuestPermissionService(), new AttributeService(new AttributeRepository()), new UserRepository(), new AccessoryRepository());
        $reservationViewRepository = new ReservationViewRepository();
        $this->presenter = new MonitorDisplayPresenter($this,
            $resourceService,
            new ReservationService($reservationViewRepository, new ReservationListingFactory()),
            new ScheduleService(new ScheduleRepository(), $resourceService, new DailyLayoutFactory()),
            new MonitorViewRepository(),
            new ScheduleLayoutFactory(),
            $reservationViewRepository);

        $this->Set('ShouldLogout', false);
        $this->Set('ShowAnnouncement', false);
        $this->Set('CreateReservationPage', "");
    }

    public function ProcessAction()
    {
        $this->presenter->ProcessAction();
    }

    public function ProcessDataRequest($dataRequest)
    {
        $this->presenter->ProcessDataRequest($dataRequest);
    }

    public function ProcessPageLoad()
    {
        $this->presenter->PageLoad();
        $this->Display('MonitorDisplay/monitor-display.tpl');
    }

    /**
     * @param MonitorViewSettings $settings
     */
    public function SetViewSettings($settings)
    {
        $this->Set('ScheduleId', $settings->scheduleId);
        $this->Set('Format', $settings->style);
        $this->Set('Interval', $settings->scrollInterval);
        $this->Set('ShowHeaderDateTime', $settings->showDateTime);
        $this->Set('ShowHeaderLogo', $settings->showLogo);
        $this->Set('ShowTitle', !empty($settings->title));
        $this->Set('ShowHeader', $settings->showDateTime || $settings->showLogo || !empty($settings->title));
        $this->Set('MonitorTitle', $settings->title);
        $this->Set('LastPage', $this->GetLastMonitorPage());
        $this->Set('PageSize', $settings->pageSize);
        $now = Date::Now()->ToTimezone(Configuration::Instance()->GetDefaultTimezone());
        $this->Set('Now', $now);
    }

    public function BindReservations($items, $displayRange)
    {
        $itemsAsJson = [];
        foreach ($items as $item) {
            $dtos = $item->AsDto($this->server->GetUserSession(), $displayRange);
            foreach ($dtos as $dto) {
                $itemsAsJson[] = $dto;
            }
        }
        $this->SetJson($itemsAsJson);
    }

    public function GetReservationRequest()
    {
        $timezone = $this->server->GetUserSession()->Timezone;

        $resourceIds = [];
        $resourceIdsForm = $this->GetForm(FormKeys::RESOURCE_ID);
        if (!empty($resourceIdsForm) && is_array($resourceIdsForm)) {
            foreach ($resourceIdsForm as $id) {
                $resourceIds[] = intval($id);
            }
        }
        $builder = new LoadReservationRequestBuilder();
        return $builder
            ->WithRange(Date::Parse($this->GetForm(FormKeys::BEGIN_DATE), $timezone), Date::Parse($this->GetForm(FormKeys::END_DATE), $timezone))
            ->WithResources($resourceIds)
            ->WithScheduleId($this->GetForm(FormKeys::SCHEDULE_ID))
            ->Build();
    }

    public function GetMonitorViewId()
    {
        return $this->GetQuerystring('id');
    }

    public function Init($id)
    {
        $this->Set('Id', $id);
    }

    public function BindGrid(DateRange $range, IDailyLayout $layout, $resources, MonitorView $view)
    {
        $hasAnnouncement = false;
        if ($view->Settings() != null) {
            $hasAnnouncement = !empty($view->Settings()->announcement);
        }
        $now = Date::Now()->ToTimezone($range->Timezone());
        $this->Set('Now', $now);
        $this->Set('DisplaySlotFactory', new DisplaySlotFactory());
        $dates = $range->Dates();
        $this->Set('BoundDates', $dates);
        $this->Set('FirstDate', $dates[0]);
        $this->Set('LastDate', $dates[count($dates) - 1]);
        $this->Set('DailyLayout', $layout);
        $this->Set('Resources', $resources);
        $this->Set('ShowReservations', true);
        $this->Set('LastPage', "");
        $this->Set('ShowWeekNumbers', Configuration::Instance()->GetSectionKey(ConfigSection::SCHEDULE, ConfigKeys::SCHEDULE_SHOW_WEEK_NUMBERS, new BooleanConverter()));
        $this->Set('HasAnnouncement', $hasAnnouncement);
        $this->Display('MonitorDisplay/monitor-display-schedule.tpl');
    }

    public function BindList($reservations, MonitorView $view, $page, $timezone)
    {
        $now = Date::Now()->ToTimezone($timezone);
        $this->Set('Now', $now);
        $this->Set('Reservations', $reservations);
        $this->Set('LastPage', $page);
        $this->Set('ShowReservations', true);
        $this->Set('Timezone', $timezone);
        $this->Display('MonitorDisplay/monitor-display-schedule.tpl');
    }

    public function BindWeek(DateRange $range, $weekdayStart, IReservationListing $listing)
    {
        $now = Date::Now()->ToTimezone($range->Timezone());
        $this->Set('Now', $now);
        $this->Set('LastPage', "");
        $this->Set('DateRange', $range);
        $this->Set('WeekdayStart', $weekdayStart);
        $this->Set('ReservationListing', $listing);
        $this->Set('ShowReservations', true);
        $this->Set('DayNames', Resources::GetInstance()->GetDays('full'));
        $this->Display('MonitorDisplay/monitor-display-calendar.tpl');
    }

    public function BindAnnouncement(MonitorView $view)
    {
        $this->Set("ShowReservations", false);
        $this->Set("ShowAnnouncement", true);
        $this->Set("Announcement", $view->Settings()->announcement);
        $this->Set("LastPage", 'announcement');
        $this->Display('MonitorDisplay/monitor-display-schedule.tpl');
    }

    public function GetLastMonitorPage()
    {
        return $this->GetQuerystring('lastpage');
    }

    public function BindError()
    {
       $this->Display('MonitorDisplay/monitor-display-error.tpl');
    }
}