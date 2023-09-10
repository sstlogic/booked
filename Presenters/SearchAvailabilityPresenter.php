<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'Pages/SearchAvailabilityPage.php');

class SearchAvailabilityPresenter extends ActionPresenter
{
    /**
     * @var ISearchAvailabilityPage
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
     * @var UserSession
     */
    private $user;
    /**
     * @var IScheduleService
     */
    private $scheduleService;
    /**
     * @var ScheduleLayout[]
     */
    private $_layouts = array();

    public function __construct(ISearchAvailabilityPage $page,
                                UserSession             $user,
                                IResourceService        $resourceService,
                                IReservationService     $reservationService,
                                IScheduleService        $scheduleService)
    {
        parent::__construct($page);

        $this->page = $page;
        $this->user = $user;
        $this->resourceService = $resourceService;
        $this->reservationService = $reservationService;
        $this->scheduleService = $scheduleService;

        $this->AddAction('search', 'SearchAvailability');
        $this->AddAction('joinWaitlist', 'JoinWaitlist');
    }

    public function PageLoad()
    {
        $this->page->SetResources($this->resourceService->GetAllResources(false, $this->user));
        $this->page->SetResourceTypes($this->resourceService->GetResourceTypes());
        $this->page->SetResourceAttributes($this->resourceService->GetResourceAttributes($this->user));
        $this->page->SetResourceTypeAttributes($this->resourceService->GetResourceTypeAttributes($this->user));
        $this->page->SetSchedules($this->scheduleService->GetAll(false, $this->user));
    }

    public function SearchAvailability()
    {
        $openings = array();
        $dateRange = $this->GetSearchRange();
        $specificTime = $this->page->SearchingSpecificTime();

        $timezone = $this->user->Timezone;
        if (!$specificTime) {
            $requestedLength = $this->GetRequestedLength();
            $startTime = null;
        } else {
            $startTime = Time::Parse($this->page->GetStartTime(), $timezone);
            $endTime = Time::Parse($this->page->GetEndTime(), $timezone);

            $now = Date::Now()->ToTimezone($timezone);
            $requestedLength = DateDiff::BetweenDates($now->SetTimeString($startTime), $now->SetTimeString($endTime));
        }

        $specificRange = $this->page->SearchingWithinRange();
        $rangeStart = $specificRange ? Time::Parse($this->page->GetRangeStartTime(), $timezone) : null;
        $rangeEnd = $specificRange ? Time::Parse($this->page->GetRangeEndTime(), $timezone) : null;

        $resources = $this->resourceService->GetAllResources(false, $this->user, $this->GetFilter(), null, 100);
        $roFactory = new RepeatOptionsFactory();

        $repeatOptions = $roFactory->CreateFromComposite($this->page, $timezone);
        $repeatDates = $repeatOptions->GetDates($dateRange);
        $searchingForMoreThan24Hours = false;

        /** @var ResourceDto $resource */
        foreach ($resources as $resource) {
            if (count($openings) >= 20) {
                break;
            }
            $scheduleId = $resource->GetScheduleId();
            $resourceId = $resource->GetResourceId();
            $searchingForMoreThan24Hours = $requestedLength->TotalSeconds() > 86400;
            $reservations = $this->reservationService->Search($dateRange, $scheduleId, [$resourceId]);

            $targetTimezone = $timezone;
            $layout = $this->GetLayout($dateRange, $scheduleId, $targetTimezone, $resourceId);

            foreach ($dateRange->Dates() as $date) {
                if (count($openings) >= 20) {
                    break;
                }

                if ($searchingForMoreThan24Hours) {
                    $endDate = $date->ApplyDifference($requestedLength);
                    if ($endDate->LessThanOrEqual($dateRange->GetEnd())) {
                        $slotRange = new DateRange($date, $endDate);
                        $slots = [];
                        /** @var PotentialSlot[] $potentialSlots */
                        $potentialSlots = [];
                        foreach ($slotRange->Dates() as $slotDate) {
                            $slots = array_merge($slots, $layout->GetLayout($slotDate));
                        }

                        foreach ($slots as $slot) {
                            $potentialSlot = new PotentialSlot($slot);
                            foreach ($reservations as $reservation) {
                                $potentialSlot->AddReservedItem($reservation);
                            }

                            $potentialSlots[] = $potentialSlot;
                        }

                        for ($i = 0; $i < count($potentialSlots); $i++) {
                            $opening = $this->GetSlot($i, $i, $potentialSlots, $requestedLength, $resource);

                            if ($opening != null) {
                                $openings[] = $opening;
                            }
                        }
                    }
                } else {
                    /** @var PotentialSlot[] $potentialSlots */
                    $potentialSlots = [];
                    $slots = $layout->GetLayout($date);
                    foreach ($slots as $slot) {
                        $potentialSlot = new PotentialSlot($slot);
                        foreach ($reservations as $reservation) {
                            $potentialSlot->AddReservedItem($reservation);
                        }

                        $potentialSlots[] = $potentialSlot;
                    }

                    for ($i = 0; $i < count($potentialSlots); $i++) {
                        if (is_null($startTime) || $startTime->Equals($potentialSlots[$i]->BeginDate()->GetTime())) {
                            $opening = $this->GetSlot($i, $i, $potentialSlots, $requestedLength, $resource);
                            $withinRange = $this->WithinRange($opening, $rangeStart, $rangeEnd);

                            if ($opening != null && $withinRange) {
                                if ($this->AllDaysAreOpen($opening, $repeatDates, $resource, $requestedLength, $rangeStart, $rangeEnd)) {
                                    $openings[] = $opening;
                                }
                            }
                        }
                    }
                }
            }
        }

        Log::Debug('Searching for available openings', ['openings' => count($openings)]);

        $waitlistEnabled = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_ALLOW_WAITLIST, new BooleanConverter());
        if ($waitlistEnabled && $specificTime && $repeatOptions->RepeatType() == RepeatType::None && $dateRange->ToTimezone($this->user->Timezone)->NumberOfDays() == 1 && !empty($this->page->GetResources())) {
            $begin = $dateRange->GetBegin()->ToTimezone($this->user->Timezone)->SetTime($startTime);
            $end = $dateRange->GetBegin()->ToTimezone($this->user->Timezone)->SetTime($endTime);
            $this->page->AllowWaitlist(new DateRange($begin, $end), $this->page->GetResources());
        }

        $this->page->ShowOpenings($openings);
    }

    public function JoinWaitlist() {
        $waitlistRepo = new ReservationWaitlistRepository();
        $resourceIds = $this->page->GetWaitlistResourceIds();
        $startDate = Date::Parse($this->page->GetWaitlistStart(), $this->user->Timezone);
        $endDate = Date::Parse($this->page->GetWaitlistEnd(), $this->user->Timezone);
        foreach($resourceIds as $id) {
            $request = new ReservationWaitlistRequest(0, $this->user->UserId, $startDate, $endDate, $id);
            $waitlistRepo->Add($request);
        }
    }

    /**
     * @param int $startIndex
     * @param int $currentIndex
     * @param PotentialSlot[] $potentialSlots
     * @param DateDiff $requestedLength
     * @param ResourceDto $resource
     * @return AvailableOpeningView|null
     */
    private function GetSlot($startIndex, $currentIndex, $potentialSlots, $requestedLength, $resource)
    {
        if ($currentIndex >= count($potentialSlots)) {
            return null;
        }

        $startSlot = $potentialSlots[$startIndex];
        $currentSlot = $potentialSlots[$currentIndex];

        if ($currentSlot == null || !$currentSlot->IsReservable() || $this->ViolatesStartConstraint($currentSlot)) {
            return null;
        }

        $length = DateDiff::BetweenDates($startSlot->BeginDate(), $currentSlot->EndDate());
        if ($length->GreaterThanOrEqual($requestedLength) && $this->SlotRangeHasAvailability($startIndex, $currentIndex, $potentialSlots, $resource)) {
            return new AvailableOpeningView($resource, $startSlot->BeginDate(), $currentSlot->EndDate());
        }

        return $this->GetSlot($startIndex, $currentIndex + 1, $potentialSlots, $requestedLength, $resource);
    }

    private function ViolatesStartConstraint(PotentialSlot $currentSlot)
    {
        $constraint = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_START_TIME_CONSTRAINT);

        if (ReservationStartTimeConstraint::IsNone($constraint)) {
            return false;
        }

        if (ReservationStartTimeConstraint::IsCurrent($constraint)) {
            return $currentSlot->EndDate()->LessThan(Date::Now());
        }

        return $currentSlot->BeginDate()->LessThan(Date::Now());
    }

    /**
     * @return DateRange
     */
    private function GetSearchRange()
    {
        $range = $this->page->GetRequestedRange();
        $timezone = $this->user->Timezone;

        $today = Date::Now()->ToTimezone($timezone);

        if ($range == 'tomorrow') {
            return new DateRange($today->AddDays(1)->GetDate(), $today->AddDays(2)->GetDate());
        }

        $weekoffset = Configuration::Instance()->GetKey(ConfigKeys::FIRST_DAY_OF_WEEK, new IntConverter());

        if ($range == 'thisweek') {
            $weekday = $today->Weekday();
            $adjustedDays = (0 - $weekday);

            if ($weekday < 0) {
                $adjustedDays = $adjustedDays - 7;
            }

            $startDate = $today->AddDays($adjustedDays + $weekoffset)->GetDate();

            return new DateRange($startDate, $startDate->AddDays(7));
        }

        if ($range == 'nextweek') {
            $weekday = $today->Weekday();
            $adjustedDays = (0 - $weekday);

            if ($weekday < 0) {
                $adjustedDays = $adjustedDays - 7;
            }

            $startDate = $today->AddDays($adjustedDays + 7 + $weekoffset)->GetDate();

            return new DateRange($startDate, $startDate->AddDays(7));
        }

        if ($range == 'daterange') {
            $start = $this->page->GetRequestedStartDate();
            $end = $this->page->GetRequestedEndDate();

            if (empty($start)) {
                $start = Date::Now()->ToTimezone($timezone);
            }
            if (empty($end)) {
                $end = Date::Now()->ToTimezone($timezone);
            }

            $range = new DateRange(Date::Parse($start, $timezone), Date::Parse($end, $timezone)->AddDays(1));
            if ($range->NumberOfDays() > 14) {
                return new DateRange(Date::Parse($start, $timezone), Date::Parse($start, $timezone)->AddDays(15));
            }

            return $range;
        }

        return new DateRange($today->GetDate(), $today->AddDays(1)->GetDate());
    }

    /**
     * @return DateDiff
     */
    private function GetRequestedLength()
    {
        $hourSeconds = 3600 * $this->page->GetRequestedHours();
        $minuteSeconds = 60 * $this->page->GetRequestedMinutes();
        return new DateDiff($hourSeconds + $minuteSeconds);
    }

    /**
     * @return ScheduleResourceFilter
     */
    private function GetFilter()
    {
        return new ScheduleResourceFilter($this->page->GetScheduleId(),
            $this->page->GetResourceType(),
            $this->page->GetMaxParticipants(),
            $this->AsAttributeValues($this->page->GetResourceAttributeValues()),
            $this->AsAttributeValues($this->page->GetResourceTypeAttributeValues()),
            $this->page->GetResources());
    }

    /**
     * @param $attributeFormElements AttributeFormElement[]
     * @return AttributeValue[]
     */
    private function AsAttributeValues($attributeFormElements)
    {
        $vals = array();
        foreach ($attributeFormElements as $e) {
            if (!empty($e->Value) || (is_numeric($e->Value) && $e->Value == 0)) {
                $vals[] = new AttributeValue($e->Id, $e->Value);
            }
        }
        return $vals;
    }

    /**
     * @param AvailableOpeningView $availableOpening
     * @param DateRange[] $repeatDates
     * @param ResourceDto $resource
     * @param DateDiff $requestedLength
     * @return bool
     */
    private function AllDaysAreOpen(AvailableOpeningView $availableOpening, $repeatDates, ResourceDto $resource, $requestedLength, $rangeStart, $rangeEnd)
    {
        if (empty($repeatDates)) {
            return true;
        }

        $targetTimezone = $this->user->Timezone;
        $resourceId = $resource->GetResourceId();
        $scheduleId = $resource->GetScheduleId();

        foreach ($repeatDates as $dateRange) {
            $layout = $this->GetLayout($dateRange, $scheduleId, $targetTimezone, $resourceId);
            $foundMatch = false;
            $reservations = $this->reservationService->Search($dateRange, $scheduleId, array($resourceId));

            foreach ($dateRange->Dates() as $date) {
                /** @var PotentialSlot[] $potentialSlots */
                $potentialSlots = array();
                $slots = $layout->GetLayout($date);
                foreach ($slots as $slot) {
                    $potentialSlot = new PotentialSlot($slot);
                    foreach ($reservations as $reservation) {
                        $potentialSlot->AddReservedItem($reservation);
                    }

                    $potentialSlots[] = $potentialSlot;
                }

                for ($i = 0; $i < count($potentialSlots); $i++) {

                    $opening = $this->GetSlot($i, $i, $potentialSlots, $requestedLength, $resource);
                    $withinRange = $this->WithinRange($opening, $rangeStart, $rangeEnd);
                    if ($opening != null && $withinRange) {
                        $foundMatch = true;
                    }
                }

                if (!$foundMatch) {
                    return false;
                }
            }
        }

        return true;

    }

    /**
     * @param $dateRange
     * @param $scheduleId
     * @param $targetTimezone
     * @param $resourceId
     * @return IScheduleLayout
     */
    private function GetLayout($dateRange, $scheduleId, $targetTimezone, $resourceId)
    {
        $layout = $this->GetCachedLayout($dateRange, $scheduleId, $resourceId);
        if ($layout == null) {
            $layout = $this->scheduleService->GetLayout($scheduleId, new ScheduleLayoutFactory($targetTimezone));
            $this->SetCachedLayout($dateRange, $scheduleId, $resourceId, $layout);
        }

        return $layout;
    }

    /**
     * @param DateRange $dateRange
     * @param int $scheduleId
     * @param int $resourceId
     * @return IScheduleLayout|null
     */
    private function GetCachedLayout($dateRange, $scheduleId, $resourceId)
    {
        $key = $dateRange->ToString() . $scheduleId . $resourceId;
        if (array_key_exists($key, $this->_layouts)) {
            return $this->_layouts[$key];
        }

        return null;
    }

    /**
     * @param DateRange $dateRange
     * @param int $scheduleId
     * @param int $resourceId
     * @param IScheduleLayout $layout
     */
    private function SetCachedLayout($dateRange, $scheduleId, $resourceId, $layout)
    {
        $this->_layouts[$dateRange->ToString() . $scheduleId . $resourceId] = $layout;
    }

    /**
     * @param int $startIndex
     * @param int $currentIndex
     * @param PotentialSlot[] $potentialSlots
     * @param ResourceDto $resource
     * @return bool
     */
    private function SlotRangeHasAvailability($startIndex, $currentIndex, $potentialSlots, $resource)
    {
        for ($i = $startIndex; $i <= $currentIndex; $i++) {
            if (!$potentialSlots[$i]->IsReservable() || $potentialSlots[$i]->ReservationCount() >= $resource->GetMaxConcurrentReservations()) {
                return false;
            }
        }

        return true;
    }

    private function WithinRange(?AvailableOpeningView $opening, ?Time $rangeStart, ?Time $rangeEnd)
    {
        if (empty($opening) || empty($rangeStart) || empty($rangeEnd)) {
            return true;
        }

        if ($opening->Start()->Hour() == 0 || $opening->Start()->Hour() == 24) {
            $startOk = true;
        } else {
            $startOk = $rangeStart->Compare($opening->Start()->GetTime()) <= 0;
        }
        if ($opening->End()->Hour() == 0 || $opening->End()->Hour() == 24) {
            $endOk = true;
        } else {
            $endOk = $rangeEnd->Compare($opening->End()->GetTime()) >= 0;
        }

        return $startOk && $endOk;
    }
}

class PotentialSlot
{
    /**
     * @var SchedulePeriod
     */
    private $slot;
    /**
     * @var int
     */
    private $reservationCount = 0;
    /**
     * @var bool
     */
    private $isBlackout = false;

    public function __construct(SchedulePeriod $slot)
    {
        $this->slot = $slot;
        $this->isBlackout = !$slot->IsReservable();
    }

    public function AddReservedItem(ReservationListItem $item)
    {
        if (!$item->IsReservation()) {
            $this->isBlackout = true;
            return;
        }

        if ($item->CollidesWithRange(new DateRange($this->slot->BeginDate(), $this->slot->EndDate()))) {
            $this->reservationCount++;
        }
    }

    public function IsReservable()
    {
        return !$this->isBlackout;
    }

    public function BeginDate()
    {
        return $this->slot->BeginDate();
    }

    public function EndDate()
    {
        return $this->slot->EndDate();
    }

    public function ReservationCount()
    {
        return $this->reservationCount;
    }
}

class AvailableOpeningView
{
    /**
     * @var ResourceDto
     */
    private $resource;
    /**
     * @var Date
     */
    private $start;
    /**
     * @var Date
     */
    private $end;

    public function __construct(ResourceDto $resource, Date $start, Date $end)
    {
        $this->resource = $resource;
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * @return ResourceDto
     */
    public function Resource()
    {
        return $this->resource;
    }

    /**
     * @return Date
     */
    public function Start()
    {
        return $this->start;
    }

    /**
     * @return Date
     */
    public function End()
    {
        return $this->end;
    }

    /**
     * @return bool
     */
    public function SameDate()
    {
        return $this->Start()->DateEquals($this->End());
    }
}