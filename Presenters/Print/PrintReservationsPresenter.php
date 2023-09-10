<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once ROOT_DIR . 'Pages/Print/PrintReservationsPage.php';
require_once ROOT_DIR . 'Presenters/ActionPresenter.php';
require_once ROOT_DIR . 'Domain/namespace.php';
require_once ROOT_DIR . 'lib/Application/Schedule/namespace.php';

class PrintReservationsPresenter extends ActionPresenter
{
    /**
     * @var ResourceService
     */
    private $resourceService;

    /**
     * @var ReservationService
     */
    private $reservationService;

    /**
     * @var IPrintReservationsPage
     */
    private $page;

    /**
     * @var ScheduleService
     */
    private $scheduleService;
    /**
     * @var IPrivacyFilter
     */
    private $privacyFilter;

    public function __construct(IPrintReservationsPage $page, IResourceService $resourceService, IReservationService $reservationService, IScheduleService $scheduleService, IPrivacyFilter $privacyFilter)
    {
        parent::__construct($page);
        $this->page = $page;
        $this->resourceService = $resourceService;
        $this->reservationService = $reservationService;
        $this->scheduleService = $scheduleService;
        $this->privacyFilter = $privacyFilter;
    }

    public function PageLoad(UserSession $user)
    {
        $timezone = $user->Timezone;
        $requestedResourceIds = array_filter(explode(',', $this->page->GetResourceId() . ''), function ($id) {
            return is_numeric($id);
        });
        $requestedDate = $this->page->GetDate();
        $requestedScheduleId = $this->page->GetScheduleId();

        $selectedDate = Date::Now()->ToTimezone($timezone);
        if (!empty($requestedDate)) {
            try {
                $selectedDate = Date::Parse($requestedDate, $timezone);
            } catch (Exception $ex) {
                $selectedDate = Date::Now()->ToTimezone($timezone);
            }
        }
        $schedules = $this->scheduleService->GetAll(false, $user);

        foreach ($schedules as $schedule) {
            if ($schedule->GetIsDefault()) {
                $selectedSchedule = $schedule;
            }
            if ($schedule->GetId() == $requestedScheduleId) {
                $selectedSchedule = $schedule;
                break;
            }
        }

        if (empty($selectedSchedule)) {
            $this->page->SetNoResources(true);
            return;
        }

        $showInaccessible = Configuration::Instance()->GetSectionKey(ConfigSection::SCHEDULE, ConfigKeys::SCHEDULE_SHOW_INACCESSIBLE_RESOURCES, new BooleanConverter());
        $resources = $this->resourceService->GetScheduleResources($selectedSchedule->GetId(), $showInaccessible, $user);

        $selectedResources = [];
        $resourceIds = [];

        if (count($resources) == 0) {
            $this->page->SetNoResources(true);
            return;
        }

        if (!empty($requestedResourceIds)) {
            foreach ($resources as $resource) {
                if (in_array($resource->GetId(), $requestedResourceIds)) {
                    $selectedResources[] = $resource;
                    $resourceIds[] = $resource->GetId();
                }
            }
        }


        $first = null;
        $last = null;
        $layout = $this->scheduleService->GetLayout($selectedSchedule->GetId(), new ScheduleLayoutFactory($timezone));
        $schedulePeriods = $layout->GetLayout($selectedDate);
        foreach ($schedulePeriods as $period) {
            if (empty($first) && $period->IsReservable()) {
                $first = $period;
            }
            if (empty($last) && !empty($first) && !$period->IsReservable()) {
                $last = $period;
            }
        }

        if (empty($first)) {
            $first = $schedulePeriods[0];
        }

        if (empty($last)) {
            $last = $schedulePeriods[count($schedulePeriods) - 1];
        }

        $reservations = $this->GetIndexedReservationsByHour($resourceIds, $selectedSchedule->GetId(), $selectedDate, $timezone, $first, $last, $user);
        $this->page->BindResources($resources);
        $this->page->SetDate($selectedDate);
        $this->page->SetVisibleHours($first->Begin()->Hour(), $last->Begin()->Hour());
        $this->page->SetReservations($reservations);
        $this->page->SetResources($selectedResources, $resourceIds);
        $this->page->SetSchedules($schedules, $selectedSchedule);
    }

    /**
     * @param int[] $selectedResourceIds
     * @param int $scheduleId
     * @param Date $selectedDate
     * @param $timezone
     * @param SchedulePeriod $first
     * @param SchedulePeriod $last
     * @param UserSession $currentUser
     * @return array|ReservationListItem[]
     */
    private function GetIndexedReservationsByHour($selectedResourceIds, $scheduleId, Date $selectedDate, $timezone, SchedulePeriod $first, SchedulePeriod $last, UserSession $currentUser)
    {
        $indexed = [];
        for ($h = $first->Begin()->Hour(); $h < $last->Begin()->Hour(); $h++) {
            $indexed[intval($h)] = [];
        }

        $listing = $this->reservationService->GetReservations(new DateRange($selectedDate->GetDate(), $selectedDate->GetDate()->AddDays(1)), $scheduleId, $timezone, $selectedResourceIds, true);

        foreach ($listing->Reservations() as $reservation) {
            $view = $this->AsView($reservation);
            if (!$this->privacyFilter->CanViewUser($currentUser, $view)) {
                $reservation = new CensorUser($reservation);
            }
            if (!$this->privacyFilter->CanViewDetails($currentUser, $view)) {
                $reservation = new CensorDetails($reservation);
            }
            $localStartTime = $reservation->StartDate()->ToTimezone($timezone);
            if ($localStartTime->LessThanOrEqual($first->BeginDate())) {
                $indexed[$first->Begin()->Hour()][] = $reservation;
                continue;
            }

            if ($localStartTime->GreaterThanOrEqual($last->BeginDate())) {
                $indexed[$last->Begin()->Hour()][] = $reservation;
                continue;
            }

            $indexed[intval($localStartTime->Hour())][] = $reservation;
        }

        return $indexed;
    }

    /**
     * @param ReservationListItem $reservation
     * @return ReservationView
     */
    private function AsView(ReservationListItem $reservation)
    {
        $v = new ReservationView();
        $v->StartDate = $reservation->StartDate();
        $v->EndDate = $reservation->EndDate();
        $v->CoOwners = $reservation->CoOwnerIds();
        $v->OwnerId = $reservation->OwnerId();
        $v->ResourceId = $reservation->ResourceId();
        $v->ReferenceNumber = $reservation->ReferenceNumber();

        return $v;
    }
}

class CensorUser extends ReservationListItem {
    /**
     * @var ReservationListItem
     */
    private $r;

    public function __construct(ReservationListItem $r) {

        $this->r = $r;
    }

    public function StartDate()
    {
        return $this->r->StartDate();
    }

    public function EndDate()
    {
        return $this->r->EndDate();
    }

    public function BufferedStartDate()
    {
      return $this->r->BufferedStartDate();
    }

    public function BufferedEndDate()
    {
        return $this->r->BufferedEndDate();
    }

    public function OccursOn(Date $date)
    {
        return $this->r->OccursOn($date);
    }

    public function BuildSlot(SchedulePeriod $start, SchedulePeriod $end, Date $displayDate, $span)
    {
        return $this->r->BuildSlot($start, $end, $displayDate, $span);
    }

    public function ResourceId()
    {
        return $this->r->ResourceId();
    }

    public function Id()
    {
        return $this->r->Id();
    }

    public function IsReservation()
    {
        return $this->r->IsReservation();
    }

    public function ReferenceNumber()
    {
        return $this->r->ReferenceNumber();
    }

    public function BufferTime()
    {
        return $this->r->BufferTime();
    }

    public function HasBufferTime()
    {
        return $this->r->HasBufferTime();
    }

    public function CollidesWith(Date $date)
    {
        return $this->r->CollidesWith($date);
    }


    public function CollidesWithRange(DateRange $dateRange)
    {
        return $this->r->CollidesWithRange($dateRange);
    }

    public function GetColor()
    {
        return $this->r->GetColor();
    }

    public function GetTextColor()
    {
        return $this->r->GetTextColor();
    }

    public function GetBorderColor()
    {
        return $this->r->GetBorderColor();
    }

    public function GetTitle()
    {
        return $this->r->GetTitle();
    }

    public function GetResourceName()
    {
        return $this->r->GetResourceName();
    }

    public function GetResourceNames()
    {
        return $this->r->GetResourceNames();
    }

    public function GetUserName()
    {
        return "";
    }

    public function RequiresCheckin()
    {
        return $this->r->RequiresCheckin();
    }

    public function AsDto($currentUser, $displayRange)
    {
        return $this->r->AsDto($currentUser, $displayRange);
    }

    public function GetAttributeValue($id)
    {
        return $this->r->GetAttributeValue($id);
    }
}

class CensorDetails extends ReservationListItem {
    /**
     * @var ReservationListItem
     */
    private $r;

    public function __construct(ReservationListItem $r) {

        $this->r = $r;
    }

    public function StartDate()
    {
        return $this->r->StartDate();
    }

    public function EndDate()
    {
        return $this->r->EndDate();
    }

    public function BufferedStartDate()
    {
        return $this->r->BufferedStartDate();
    }

    public function BufferedEndDate()
    {
        return $this->r->BufferedEndDate();
    }

    public function OccursOn(Date $date)
    {
        return $this->r->OccursOn($date);
    }

    public function BuildSlot(SchedulePeriod $start, SchedulePeriod $end, Date $displayDate, $span)
    {
        return $this->r->BuildSlot($start, $end, $displayDate, $span);
    }

    public function ResourceId()
    {
        return $this->r->ResourceId();
    }

    public function Id()
    {
        return $this->r->Id();
    }

    public function IsReservation()
    {
        return $this->r->IsReservation();
    }

    public function ReferenceNumber()
    {
        return $this->r->ReferenceNumber();
    }

    public function BufferTime()
    {
        return $this->r->BufferTime();
    }

    public function HasBufferTime()
    {
        return $this->r->HasBufferTime();
    }

    public function CollidesWith(Date $date)
    {
        return $this->r->CollidesWith($date);
    }

    public function CollidesWithRange(DateRange $dateRange)
    {
        return $this->r->CollidesWithRange($dateRange);
    }

    public function GetColor()
    {
        return $this->r->GetColor();
    }

    public function GetTextColor()
    {
        return $this->r->GetTextColor();
    }

    public function GetBorderColor()
    {
        return $this->r->GetBorderColor();
    }

    public function GetTitle()
    {
        return "";
    }

    public function GetResourceName()
    {
        return $this->r->GetResourceName();
    }

    public function GetResourceNames()
    {
        return $this->r->GetResourceNames();
    }

    public function GetUserName()
    {
        return $this->r->GetUserName();
    }

    public function RequiresCheckin()
    {
        return $this->r->RequiresCheckin();
    }

    public function AsDto($currentUser, $displayRange)
    {
        return $this->r->AsDto($currentUser, $displayRange);
    }

    public function GetAttributeValue($id)
    {
        return "";
    }
}