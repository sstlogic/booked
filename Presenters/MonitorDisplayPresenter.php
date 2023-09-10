<?php
/**
 * Copyright 2018-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'lib/Application/Schedule/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Authorization/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Authentication/namespace.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'Pages/MonitorDisplayPage.php');
require_once(ROOT_DIR . 'Presenters/Schedule/LoadReservationRequest.php');

class MonitorDisplayPresenter extends ActionPresenter
{
    /**
     * @var IMonitorDisplayPage
     */
    private $page;
    /**
     * @var IResourceService
     */
    private $resourceService;
    /**
     * @var IReservationService
     */
    private $reservationService;
    /**
     * @var IScheduleService
     */
    private $scheduleService;
    /**
     * @var IDailyLayoutFactory
     */
    private $layoutFactory;
    /**
     * @var IMonitorViewRepository
     */
    private $monitorViewRepository;
    /**
     * @var IReservationViewRepository
     */
    private $reservationViewRepository;

    public function __construct(IMonitorDisplayPage        $page,
                                IResourceService           $resourceService,
                                IReservationService        $reservationService,
                                IScheduleService           $scheduleService,
                                IMonitorViewRepository     $monitorViewRepository,
                                ILayoutFactory             $layoutFactory,
                                IReservationViewRepository $reservationViewRepository)
    {
        parent::__construct($page);
        $this->page = $page;
        $this->resourceService = $resourceService;
        $this->reservationService = $reservationService;
        $this->scheduleService = $scheduleService;
        $this->layoutFactory = $layoutFactory;
        $this->monitorViewRepository = $monitorViewRepository;
        $this->reservationViewRepository = $reservationViewRepository;
    }

    public function PageLoad()
    {
        $id = $this->page->GetMonitorViewId();
        $view = $this->monitorViewRepository->LoadByPublicId($id);
        if ($view) {
            $this->page->Init($view->PublicId());
        } else {
            $this->page->Init(null);
        }
    }

    private function GetResources($scheduleId)
    {
        return $this->resourceService->GetScheduleResources($scheduleId, true, new NullUserSession());
    }

    public function ProcessDataRequest($dataRequest)
    {
        if ($dataRequest == 'load') {
            $this->LoadView();
        } elseif ($dataRequest == 'reservations') {
            $this->LoadReservations();
        }
    }

    private function BindGrid(MonitorView $view)
    {
        $settings = $view->Settings();
        $scheduleId = $settings->scheduleId;
        $layout = $this->scheduleService->GetLayout($scheduleId, $this->layoutFactory);
        $schedule = $this->scheduleService->GetSchedule($scheduleId);
        $resources = $this->GetResources($scheduleId);
        $displayResources = $this->GetDisplayResources($resources, $settings);

        $timezone = $layout->Timezone();

        $startDate = $this->GetStart($settings, $timezone, $schedule->GetWeekdayStart());
        $endDate = $this->GetEnd($settings, $startDate);

        $reservationListing = new EmptyReservationListing();
        $dailyLayout = $this->scheduleService->GetDailyLayout($scheduleId, $this->layoutFactory, $reservationListing);
        $this->page->BindGrid(new DateRange($startDate, $endDate), $dailyLayout, $displayResources, $view);
    }

    public function BindCalendar(MonitorView $view) {
        $settings = $view->Settings();
        $scheduleId = $settings->scheduleId;
        $schedule = $this->scheduleService->GetSchedule($scheduleId);
        $weekdayStart = $schedule->GetWeekdayStart();
        $layout = $this->scheduleService->GetLayout($scheduleId, $this->layoutFactory);
        $timezone = $layout->Timezone();

        $resources = $this->GetResources($scheduleId);
        $displayResources = $this->GetDisplayResources($resources, $settings);

        $resourceIds = null;
        foreach ($displayResources as $r) {
            $resourceIds[] = $r->GetId();
        }

        $startDate = $this->GetStart($settings, $timezone, $schedule->GetWeekdayStart());
        $endDate = $this->GetEnd($settings, $startDate);

        $reservationListing = $this->reservationService->GetReservations(new DateRange($startDate, $endDate), $scheduleId, $timezone, $resourceIds, $settings->consolidateReservations);

        $this->page->BindWeek(new DateRange($startDate, $endDate), $weekdayStart, $reservationListing);
    }

    public function BindList(MonitorView $view)
    {
        $lastPage = $this->page->GetLastMonitorPage();
        $settings = $view->Settings();
        $scheduleId = $settings->scheduleId;
        $layout = $this->scheduleService->GetLayout($scheduleId, $this->layoutFactory);
        $timezone = $layout->Timezone();
        $schedule = $this->scheduleService->GetSchedule($scheduleId);
        $resources = $this->GetResources($scheduleId);
        $displayResources = $this->GetDisplayResources($resources, $settings);

        $resourceIds = null;
        foreach ($displayResources as $r) {
            $resourceIds[] = $r->GetId();
        }

        $startDate = $this->GetStart($settings, $timezone, $schedule->GetWeekdayStart());
        $endDate = $this->GetEnd($settings, $startDate);

        if ($settings->reservationsToShow == MonitorViewReservations::SpecificReservations) {
            $reservations = [];
            foreach ($settings->referenceNumbers as $referenceNumber) {
                $reservation = $this->reservationViewRepository->GetReservationForEditing($referenceNumber);
                $reservations[] = $this->ReservationViewAsListItem($reservation);
            }
        } else {
            $reservations = $this->reservationService->GetReservations(new DateRange($startDate, $endDate), $scheduleId, $timezone, $resourceIds, $settings->consolidateReservations)->Reservations();

            if ($settings->reservationsToShow == MonitorViewReservations::MatchingAttribute) {
                $filteredReservations = [];
                foreach ($reservations as $r) {
                    $val = $r->GetAttributeValue($settings->attributeId);
                    if ((empty($settings->attributeValue) && empty($val)) || (strtolower($val) == strtolower($settings->attributeValue))) {
                        $filteredReservations[] = $r;
                    }
                }
                $reservations = $filteredReservations;
            }
        }

        if ($settings->reservationsToShow == MonitorViewReservations::Count && count($reservations) > $settings->count) {
            $reservations = array_slice($reservations, 0, $settings->count);
        }

        $slice = $this->GetSlice($reservations, $lastPage, $settings->pageSize);

        $this->page->BindList($slice->reservations, $view, empty($slice->page) ? "" : $slice->page, $timezone);
    }

    /**
     * @param MonitorViewSettings $settings
     * @param string $timezone
     * @param int $firstDayOfWeek
     * @return Date
     */
    private function GetStart($settings, $timezone, $firstDayOfWeek)
    {
        if ($firstDayOfWeek == Schedule::Today) {
            $firstDayOfWeek = 0;
        }
        $today = Date::Now()->ToTimezone($timezone)->GetDate();
        $currentWeekday = $today->Weekday();

        if ($settings->reservationsToShow == MonitorViewReservations::DateRange) {
            return Date::Parse($settings->startDate, $timezone);
        }

        if ($settings->reservationsToShow == MonitorViewReservations::ThisWeek) {
            $adjustedDays = ($firstDayOfWeek - $currentWeekday);

            if ($currentWeekday < $firstDayOfWeek) {
                $adjustedDays = $adjustedDays - 7;
            }

            return $today->AddDays($adjustedDays);
        }

        return Date::Now()->ToTimezone($timezone);
    }

    /**
     * @param MonitorViewSettings $settings
     * @param Date $startDate
     * @return Date
     */
    private function GetEnd($settings, Date $startDate)
    {
        if ($settings->reservationsToShow == MonitorViewReservations::DateRange) {
            return Date::Parse($settings->endDate, $startDate->Timezone());
        }

        if ($settings->reservationsToShow === MonitorViewReservations::Days) {
            return $startDate->GetDate()->AddDays($settings->days);
        }

        if ($settings->reservationsToShow == MonitorViewReservations::ThisWeek) {
            return $startDate->AddDays(7);
        }

        return $startDate->GetDate()->AddDays(7);
    }

    public function LoadReservations()
    {
        $filter = $this->page->GetReservationRequest();
        $items = $this->reservationService->Search($filter->DateRange(), $filter->ScheduleId(), $filter->ResourceIds());
        $this->page->BindReservations($items, $filter->DateRange());
    }

    public function LoadView()
    {
        $id = $this->page->GetMonitorViewId();
        $lastPage = $this->page->GetLastMonitorPage();
        $view = $this->monitorViewRepository->LoadByPublicId($id);

        if (empty($view)) {
            $this->page->BindError();
            return;
        }

        $settings = $view->Settings();
        $this->page->SetViewSettings($settings);

        if (!empty($settings->announcement) && ($lastPage == "" || !$settings->showReservations)) {
            $this->page->BindAnnouncement($view);
            return;
        }

        if ($settings->showReservations) {
            if ($settings->style == MonitorViewStyle::Grid && $settings->reservationsToShow != MonitorViewReservations::SpecificReservations) {
                $this->BindGrid($view);
            }
            elseif($settings->style == MonitorViewStyle::Calendar) {
                $this->BindCalendar($view);
            }
            else {
                $this->BindList($view);
            }
        }
    }

    /**
     * @param $reservations
     * @param string $lastPage
     * @param int $pageSize
     * @return ReservationListSlice
     */
    private function GetSlice($reservations, string $lastPage, $pageSize)
    {
        $start = 0;

        if ($lastPage != "" && $lastPage != "announcement") {
            $pageNumber = intval($lastPage) + 1;
            $start = intval($lastPage) * $pageSize;
        } else {
            $pageNumber = 1;
        }

        $reservationCount = count($reservations);
        if ($start >= $reservationCount) {
            return new ReservationListSlice([], 0);
        }

        if ($start + $pageSize >= $reservationCount) {
            return new ReservationListSlice(array_slice($reservations, $start, $pageSize), 0);
        }

        return new ReservationListSlice(array_slice($reservations, $start, $pageSize), $pageNumber);
    }

    /**
     * @return ReservationListItem
     */
    public function ReservationViewAsListItem(ReservationView $view)
    {
        return new ReservationListItem(
            new ReservationItemView(
                $view->ReferenceNumber,
                $view->StartDate,
                $view->EndDate,
                $view->ResourceName,
                $view->ResourceId,
                $view->ReservationId,
                ReservationUserLevel::OWNER,
                $view->Title,
                $view->Description,
                $view->ScheduleId,
                $view->OwnerFirstName,
                $view->OwnerLastName,
                $view->OwnerId,
                $view->OwnerPhone,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
            )
        );
    }

    /**
     * @param array $resources
     * @param MonitorViewSettings|null $settings
     * @return BookableResource[]
     */
    private function GetDisplayResources(array $resources, $settings): array
    {
        $displayResources = $resources;

        if ((empty($settings->resourcesToShow) || $settings->resourcesToShow == MonitorViewResources::Resources) && !empty($settings->resourceIds)) {
            $displayResources = [];
            foreach ($resources as $r) {
                if (in_array($r->GetResourceId(), $settings->resourceIds)) {
                    $displayResources[] = $r;
                }
            }
        }

        if ($settings->resourcesToShow == MonitorViewResources::Types) {
            $displayResources = [];
            foreach ($resources as $r) {
                if (in_array($r->GetResourceTypeId(), $settings->resourceTypeIds)) {
                    $displayResources[] = $r;
                }
            }
        }

        if ($settings->resourcesToShow == MonitorViewResources::Groups) {
            $displayResources = [];
            foreach ($resources as $r) {
                if (count(array_intersect($r->GetResourceGroupIds(), $settings->resourceGroupIds)) > 0) {
                    $displayResources[] = $r;
                }
            }
        }
        return $displayResources;
    }
}

class ReservationListSlice
{
    public $reservations = [];
    public $page = 0;

    public function __construct($reservations, $page)
    {
        $this->reservations = $reservations;
        $this->page = $page;
    }
}