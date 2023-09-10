<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Config/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Schedule/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Authorization/namespace.php');
require_once(ROOT_DIR . 'lib/Server/namespace.php');
require_once(ROOT_DIR . 'lib/Common/namespace.php');
require_once(ROOT_DIR . 'Domain/namespace.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'Presenters/Schedule/SchedulePageBuilder.php');
require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');

interface ISchedulePresenter
{
    public function PageLoad(UserSession $user);
}

class SchedulePresenter extends ActionPresenter implements ISchedulePresenter
{
    /**
     * @var ISchedulePage
     */
    private $page;

    /**
     * @var IScheduleService
     */
    private $scheduleService;

    /**
     * @var IResourceService
     */
    private $resourceService;

    /**
     * @var ISchedulePageBuilder
     */
    private $builder;

    /**
     * @var IReservationService
     */
    private $reservationService;

    /**
     * @param ISchedulePage $page
     * @param IScheduleService $scheduleService
     * @param IResourceService $resourceService
     * @param ISchedulePageBuilder $schedulePageBuilder
     * @param IReservationService $reservationService
     */
    public function __construct(
        ISchedulePage        $page,
        IScheduleService     $scheduleService,
        IResourceService     $resourceService,
        ISchedulePageBuilder $schedulePageBuilder,
        IReservationService  $reservationService
    )
    {
        parent::__construct($page);
        $this->page = $page;
        $this->scheduleService = $scheduleService;
        $this->resourceService = $resourceService;
        $this->builder = $schedulePageBuilder;
        $this->reservationService = $reservationService;
    }

    public function PageLoad(UserSession $user, $loadReservations = false)
    {
        $allowTallView = true;
        $showInaccessibleResources = $this->page->ShowInaccessibleResources();

        $schedules = $this->scheduleService->GetAll($showInaccessibleResources, $user);

        if (count($schedules) == 0) {
            $this->page->ShowPermissionError(true);
            return;
        }

        $this->page->ShowPermissionError(false);

        $currentSchedule = $this->builder->GetCurrentSchedule($this->page, $schedules, $user);
        $targetTimezone = $this->page->GetDisplayTimezone($user, $currentSchedule);
        $this->page->SetIsUsingAppointments($currentSchedule->HasCustomLayout());

        $activeScheduleId = $currentSchedule->GetId();
        $this->builder->BindSchedules($this->page, $schedules, $currentSchedule);

        $filter = $this->builder->GetResourceFilter($activeScheduleId, $this->page);

        $scheduleDates = $this->builder->GetScheduleDates($user, $currentSchedule, $this->page);
        $this->builder->BindDisplayDates($this->page, $scheduleDates, $currentSchedule);
        $this->builder->BindSpecificDates($user, $this->page, $this->page->GetSelectedDates(), $currentSchedule);

        $resourceTypes = $this->resourceService->GetResourceTypes();
        $this->builder->BindResourceTypes($this->page, $resourceTypes);

        $resources = $this->resourceService->GetScheduleResources($activeScheduleId, $showInaccessibleResources, $user, $filter);
        $rids = array();
        foreach ($resources as $resource) {
            $rids[] = $resource->Id;
            if ($resource->GetAllowConcurrentReservations()) {
                $allowTallView = false;
            }
        }

        $resourceAttributes = $this->resourceService->GetResourceAttributes($user, $rids);
        $resourceTypeAttributes = $this->resourceService->GetResourceTypeAttributes($user);

        $this->builder->BindResourceFilter($this->page, $filter, $resourceAttributes, $resourceTypeAttributes);

        $reservationListing = new EmptyReservationListing();

        if ($loadReservations) {
            $reservationListing = $this->reservationService->GetReservations($scheduleDates, $activeScheduleId, $targetTimezone, $rids);
        }

        $dailyLayout = $this->scheduleService->GetDailyLayout($activeScheduleId, new ScheduleLayoutFactory($targetTimezone), $reservationListing);

        $this->page->SetAllowTallView($allowTallView);
        $this->builder->BindReservations($this->page, $resources, $dailyLayout);
    }

    public function GetLayout(UserSession $user)
    {
        $scheduleId = $this->page->GetScheduleId();
        $layoutDate = $this->page->GetLayoutDate();

        $requestedDate = Date::Parse($layoutDate, $user->Timezone);

        $layout = $this->scheduleService->GetLayout($scheduleId, new ScheduleLayoutFactory($user->Timezone));
        $periods = $layout->GetLayout($requestedDate);

        $this->page->SetLayoutResponse(new ScheduleLayoutSerializable($periods));
    }

    public function LoadReservations()
    {
        $filter = $this->page->GetReservationRequest();
        $items = $this->reservationService->Search($filter->DateRange(), $filter->ScheduleId(), $filter->ResourceIds(), $filter->UserId(), $filter->UserLevel());
        $this->page->BindReservations($items, $filter->DateRange());
    }
}