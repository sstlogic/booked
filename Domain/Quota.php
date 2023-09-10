<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

interface IQuota
{
    /**
     * @param ReservationSeries $reservationSeries
     * @param User $user
     * @param Schedule $schedule
     * @param IReservationViewRepository $reservationViewRepository
     * @return bool
     */
    public function ExceedsQuota($reservationSeries, $user, $schedule, IReservationViewRepository $reservationViewRepository);

    /**
     * @return int
     */
    public function Id();

    public function ToString();
}

class Quota implements IQuota
{
    /**
     * @var int
     */
    private $quotaId;
    /**
     * @var \IQuotaDuration
     */
    private $duration;
    /**
     * @var \IQuotaLimit
     */
    private $limit;
    /**
     * @var int
     */
    private $resourceId;
    /**
     * @var int
     */
    private $groupId;
    /**
     * @var int
     */
    private $scheduleId;
    /**
     * @var null|Time
     */
    private $enforcedStartTime;
    /**
     * @var null|Time
     */
    private $enforcedEndTime;
    /**
     * @var array|int[]|string[]
     */
    private $enforcedDays = array();
    /**
     * @var IQuotaScope
     */
    private $scope;
    /**
     * @var Schedule
     */
    private $schedule;
    /**
     * @var int
     */
    private $interval = 1;
    /**
     * @var Date
     */
    private $dateCreated;
    /**
     * @var int|null
     */
    private $stopEnforcementAmount;
    /**
     * @var string|QuotaDuration
     */
    private $stopEnforcementUnit;

    /**
     * @param int $quotaId
     * @param IQuotaDuration $duration
     * @param IQuotaLimit $limit
     * @param int|null $resourceId
     * @param int|null $groupId
     * @param int|null $scheduleId
     * @param string|null $enforcedStartTime
     * @param string|null $enforcedEndTime
     * @param array $enforcedDays
     * @param int $interval
     * @param IQuotaScope $scope
     * @param Date|null $dateCreated
     */
    public function __construct($quotaId, $duration, $limit, $resourceId = null, $groupId = null, $scheduleId = null, $enforcedStartTime = null,
                                $enforcedEndTime = null, $enforcedDays = array(), $scope = null, $interval = 1, $dateCreated = null)
    {
        $this->quotaId = $quotaId;
        $this->duration = $duration;
        $this->limit = $limit;
        $this->resourceId = empty($resourceId) ? null : $resourceId;
        $this->groupId = empty($groupId) ? null : $groupId;
        $this->scheduleId = empty($scheduleId) ? null : $scheduleId;
        $this->enforcedStartTime = empty($enforcedStartTime) ? null : Time::Parse($enforcedStartTime);
        $this->enforcedEndTime = empty($enforcedEndTime) ? null : Time::Parse($enforcedEndTime);
        $this->enforcedDays = empty($enforcedDays) ? array() : $enforcedDays;
        $this->scope = empty($scope) ? new QuotaScopeIncluded() : $scope;
        $this->interval = empty($interval) ? 1 : intval($interval);
        $this->dateCreated = empty($dateCreated) ? Date::Now() : $dateCreated;
    }

    /**
     * @static
     * @param string $duration
     * @param float $limit
     * @param string $unit
     * @param int $resourceId
     * @param int $groupId
     * @param int $scheduleId
     * @param string $enforcedStartTime
     * @param string $enforcedEndTime
     * @param array|int[]|string[] $enforcedDays
     * @param string $scope
     * @param int $interval
     * @param Date|null $dateCreated
     * @return Quota
     */
    public static function Create($duration, $limit, $unit, $resourceId, $groupId, $scheduleId, $enforcedStartTime, $enforcedEndTime, $enforcedDays, $scope, $interval, $dateCreated)
    {
        return new Quota(0, self::CreateDuration($duration), self::CreateLimit($limit, $unit), $resourceId, $groupId, $scheduleId, $enforcedStartTime,
            $enforcedEndTime, $enforcedDays, self::CreateScope($scope), $interval, $dateCreated);
    }

    /**
     * @static
     * @param float $limit
     * @param string|QuotaUnit $unit
     * @param int $interval
     * @param string|QuotaDuration $duration
     * @return QuotaBuilder
     */
    public static function Builder($limit, $unit, $interval, $duration): QuotaBuilder
    {
        return (new QuotaBuilder())->WithDuration($interval, $duration)->WithLimit($limit, $unit);
    }

    /**
     * @param array $row
     * @return Quota
     */
    public static function FromRow($row)
    {
        $quotaId = $row[ColumnNames::QUOTA_ID];

        $limit = Quota::CreateLimit($row[ColumnNames::QUOTA_LIMIT], $row[ColumnNames::QUOTA_UNIT]);
        $duration = Quota::CreateDuration($row[ColumnNames::QUOTA_DURATION]);

        $resourceId = $row[ColumnNames::RESOURCE_ID];
        $groupId = $row[ColumnNames::GROUP_ID];
        $scheduleId = $row[ColumnNames::SCHEDULE_ID];
        $enforcedStartTime = $row[ColumnNames::ENFORCED_START_TIME];
        $enforcedEndTime = $row[ColumnNames::ENFORCED_END_TIME];
        $enforcedDays = empty($row[ColumnNames::ENFORCED_DAYS]) ? [] : explode(',', $row[ColumnNames::ENFORCED_DAYS]);
        $scope = Quota::CreateScope($row[ColumnNames::QUOTA_SCOPE]);
        $interval = $row[ColumnNames::QUOTA_INTERVAL];
        $dateCreated = Date::FromDatabase($row[ColumnNames::DATE_CREATED]);

        $quota = new Quota($quotaId, $duration, $limit, $resourceId, $groupId, $scheduleId, $enforcedStartTime, $enforcedEndTime, $enforcedDays, $scope, $interval, $dateCreated);

        $stop = QuotaEnforcementLimit::FromMinutes($row[ColumnNames::QUOTA_STOP_ENFORCEMENT_MINUTES_PRIOR]);
        $quota->WithStopEnforcement($stop->Amount(), $stop->Unit());

        return $quota;
    }

    /**
     * @static
     * @param float $limit
     * @param string $unit QuotaUnit
     * @return IQuotaLimit
     */
    public static function CreateLimit($limit, $unit)
    {
        if ($unit == QuotaUnit::Reservations) {
            return new QuotaLimitCount($limit);
        }

        return new QuotaLimitHours($limit);
    }

    /**
     * @static
     * @param string $duration QuotaDuration
     * @return IQuotaDuration
     */
    public static function CreateDuration($duration)
    {
        if ($duration == QuotaDuration::Hour) {
            return new QuotaDurationHour();
        }

        if ($duration == QuotaDuration::Day) {
            return new QuotaDurationDay();
        }

        if ($duration == QuotaDuration::Week) {
            return new QuotaDurationWeek();
        }

        if ($duration == QuotaDuration::Month) {
            return new QuotaDurationMonth();
        }
        return new QuotaDurationYear();
    }

    /**
     * @param string|QuotaScope $scope
     * @return IQuotaScope
     */
    public static function CreateScope($scope)
    {
        if ($scope == QuotaScope::ExcludeCompleted) {
            return new QuotaScopeExcluded();
        }

        return new QuotaScopeIncluded();
    }

    /**
     * @param ReservationSeries $reservationSeries
     * @param User $user
     * @param Schedule $schedule
     * @param IReservationViewRepository $reservationViewRepository
     * @return bool
     */
    public function ExceedsQuota($reservationSeries, $user, $schedule, IReservationViewRepository $reservationViewRepository)
    {
        $timezone = $schedule->GetTimezone();
        $this->schedule = $schedule;

        if (!is_null($this->resourceId)) {
            $appliesToResource = false;

            foreach ($reservationSeries->AllResourceIds() as $resourceId) {
                if (!$appliesToResource && $this->AppliesToResource($resourceId)) {
                    $appliesToResource = true;
                }
            }

            if (!$appliesToResource) {
                return false;
            }
        }

        if (!is_null($this->groupId)) {
            $appliesToGroup = false;
            foreach ($user->Groups() as $group) {
                if (!$appliesToGroup && $this->AppliesToGroup($group->GroupId)) {
                    $appliesToGroup = true;
                }
            }

            if (!$appliesToGroup) {
                return false;
            }
        }

        if (!$this->AppliesToSchedule($reservationSeries->ScheduleId())) {
            return false;
        }

        if (count($reservationSeries->Instances()) == 0) {
            return false;
        }


        $dates = $this->duration->GetSearchDates($reservationSeries, $timezone, $this->GetFirstWeekday(), $this->Interval());
        $firstEnforcedDate = $this->GetScope()->GetSearchStartDate($dates->Start())->ToTimezone($timezone);
        $reservationsWithinRange = $reservationViewRepository->GetReservations($firstEnforcedDate, $dates->End(), $reservationSeries->UserId(), ReservationUserLevel::OWNER);

        $ranges = $this->duration->GetEnforcementRanges($firstEnforcedDate, $dates->End(), $this->Interval(), $this->GetFirstWeekday());

        try {
            foreach ($ranges as $range) {
                $this->CheckAll($reservationsWithinRange, $reservationSeries, $timezone, $range);
            }
        } catch (QuotaExceededException $ex) {
            return true;
        }

        return false;
    }

    public function ToString()
    {
        return $this->__toString();
    }

    public function __toString()
    {
        return sprintf('Quota Id=%s, ResourceId=%s, ScheduleId=%s, GroupId=%s, Limit=%s, Duration=%s', $this->quotaId,
            $this->ResourceId(), $this->ScheduleId(), $this->GroupId(), $this->GetLimit(), $this->GetDuration());
    }

    /**
     * @return IQuotaLimit
     */
    public function GetLimit()
    {
        return $this->limit;
    }

    /**
     * @return IQuotaDuration
     */
    public function GetDuration()
    {
        return $this->duration;
    }

    /**
     * @return IQuotaScope
     */
    public function GetScope()
    {
        return $this->scope;
    }

    /**
     * @return int
     */
    public function Interval()
    {
        return $this->interval;
    }

    /**
     * @param int $resourceId
     * @return bool
     */
    public function AppliesToResource($resourceId)
    {
        return is_null($this->resourceId) || $this->resourceId == $resourceId;
    }

    /**
     * @param int $groupId
     * @return bool
     */
    public function AppliesToGroup($groupId)
    {
        return is_null($this->groupId) || $this->groupId == $groupId;
    }

    /**
     * @param int $scheduleId
     * @return bool
     */
    public function AppliesToSchedule($scheduleId)
    {
        return is_null($this->scheduleId) || $this->scheduleId == $scheduleId;
    }

    /**
     * @return int
     */
    public function Id()
    {
        return $this->quotaId;
    }

    /**
     * @return int|null
     */
    public function ResourceId()
    {
        return $this->resourceId;
    }

    /**
     * @return int|null
     */
    public function GroupId()
    {
        return $this->groupId;
    }

    /**
     * @return int|null
     */
    public function ScheduleId()
    {
        return $this->scheduleId;
    }

    /**
     * @return null|Time
     */
    public function EnforcedStartTime()
    {
        return $this->enforcedStartTime;
    }

    /**
     * @return null|Time
     */
    public function EnforcedEndTime()
    {
        return $this->enforcedEndTime;
    }

    /**
     * @return array|int[]|string[]
     */
    public function EnforcedDays()
    {
        return $this->enforcedDays;
    }

    /**
     * @return int
     */
    protected function GetFirstWeekday()
    {
        if ($this->schedule != null) {
            $start = $this->schedule->GetWeekdayStart();

            if ($start == Schedule::Today) {
                return Date::Now()->ToTimezone($this->schedule->GetTimezone())->Weekday();
            }

            return $start;
        }

        return 0;
    }

    /**
     * @return bool
     */
    private function EnforcedAllDay()
    {
        return $this->enforcedStartTime == null || $this->enforcedEndTime == null;
    }

    /**
     * @return bool
     */
    private function EnforcedEveryDay()
    {
        return empty($this->enforcedDays);
    }

    /**
     * @param int $weekday
     * @return bool
     */
    private function EnforcedOnWeekday($weekday)
    {
        return in_array($weekday, $this->enforcedDays);
    }

    private function AddExisting(ReservationItemView $reservation, $timezone, DateRange $range)
    {
        $this->_breakAndAdd($reservation->StartDate, $reservation->EndDate, $timezone, $range);
    }

    private function AddInstance(Reservation $reservation, $timezone, DateRange $range)
    {
        if ($this->IsWithinEnforcementTimes($reservation->StartDate()))
        {
            $this->_breakAndAdd($reservation->StartDate(), $reservation->EndDate(), $timezone, $range);
        }
    }

    /**
     * @param ReservationItemView[] $reservationsWithinRange
     * @param ReservationSeries $series
     * @param string $timezone
     * @param DateRange $ranges
     * @throws QuotaExceededException
     */
    private function CheckAll($reservationsWithinRange, $series, $timezone, $range)
    {
        $toBeSkipped = array();

        foreach ($series->Instances() as $instance) {
            $toBeSkipped[$instance->ReferenceNumber()] = true;

            if (!is_null($this->scheduleId)) {
                foreach ($series->AllResources() as $resource) {
                    if ($this->AppliesToResource($resource->GetResourceId())) {
                        $this->AddInstance($instance, $timezone, $range);
                    }
                }
            } else {
                $this->AddInstance($instance, $timezone, $range);
            }
        }

        foreach ($reservationsWithinRange as $reservation) {
            if (!empty($this->resourceId)) {
                $applies = ($this->AppliesToResource($reservation->ResourceId) && $series->ContainsResource($reservation->ResourceId));
            } else {
                $applies = $series->ContainsResource($reservation->ResourceId) || ($series->ScheduleId() == $reservation->ScheduleId);
            }

            if ($applies &&
                !array_key_exists($reservation->ReferenceNumber, $toBeSkipped) &&
                !$this->willBeDeleted($series, $reservation->ReservationId)
            ) {
                $this->AddExisting($reservation, $timezone, $range);
            }
        }
    }

    /**
     * @param ReservationSeries $series
     * @param int $reservationId
     * @return bool
     */
    private function willBeDeleted($series, $reservationId)
    {
        if (method_exists($series, 'IsMarkedForDelete')) {
            return $series->IsMarkedForDelete($reservationId);
        }

        return false;
    }

    private function _breakAndAdd(Date $startDate, Date $endDate, $timezone, DateRange $enforcedRange)
    {
        $start = $startDate->ToTimezone($timezone);
        $end = $endDate->ToTimezone($timezone);

        $range = new DateRange($start, $end);

        $ranges = $this->duration->Split($range, $this->GetFirstWeekday());

        foreach ($ranges as $dr) {
            if ($enforcedRange->Contains($dr->GetBegin(), false)) {
                $this->_add($dr, $enforcedRange);
            }
        }
    }

    private function _add(DateRange $dateRange, DateRange $enforcedRange)
    {
        if (!$this->EnforcedEveryDay() && !$this->EnforcedOnWeekday($dateRange->GetBegin()->Weekday())) {
            return;
        }

        if (!$this->EnforcedAllDay()) {
            $enforcedStart = $dateRange->GetBegin()->SetTime($this->EnforcedStartTime());
            $enforcedEnd = $dateRange->GetBegin()->SetTime($this->EnforcedEndTime());
            $enforcedDayRange = new DateRange($enforcedStart, $enforcedEnd);
            if (!$enforcedDayRange->Overlaps($dateRange)) {
                return;
            }
            $newStart = $dateRange->GetBegin()->GreaterThan($enforcedStart) ? $dateRange->GetBegin() : $enforcedStart;
            $newEnd = $dateRange->GetEnd()->LessThan($enforcedEnd) ? $dateRange->GetEnd() : $enforcedEnd;
            $dateRange = new DateRange($newStart, $newEnd);

        }
        $durationKey = $this->duration->GetDurationKey($enforcedRange->GetBegin(), $this->GetFirstWeekday());

        $this->limit->TryAdd($dateRange->GetBegin(), $dateRange->GetEnd(), $durationKey);
    }

    public function DateCreated()
    {
        return $this->dateCreated;
    }

    public function ChangeDuration(string $duration)
    {
        $this->duration = self::CreateDuration($duration);
    }

    public function ChangeLimit(float $limitAmount, string $unit)
    {
        $this->limit = self::CreateLimit($limitAmount, $unit);
    }

    public function ChangeScope(string $scope)
    {
        $this->scope = self::CreateScope($scope);
    }

    public function ChangeInterval(int $interval)
    {
        $this->interval = $interval;
    }

    public function ChangeResource(?int $resourceId)
    {
        $this->resourceId = $resourceId;
    }

    public function ChangeGroup(?int $groupId)
    {
        $this->groupId = $groupId;
    }

    public function ChangeSchedule(?int $scheduleId)
    {
        $this->scheduleId = $scheduleId;
    }

    public function ChangeEnforcedTimes(?string $startTime, ?string $endTime)
    {
        $this->enforcedStartTime = empty($startTime) ? null : Time::Parse($startTime);
        $this->enforcedEndTime = empty($endTime) ? null : Time::Parse($endTime);
    }

    public function ChangeEnforcedDays(array $enforcedDays)
    {
        $this->enforcedDays = $enforcedDays;
    }

    public function WithId(int $id)
    {
        $this->quotaId = $id;
    }

    public function WithStopEnforcement($amount, $unit)
    {
        $this->stopEnforcementAmount = $amount;
        $this->stopEnforcementUnit = $unit;
    }

    public function ChangeStopEnforcement($amount, $unit)
    {
        $this->WithStopEnforcement($amount, $unit);
    }

    private function IsWithinEnforcementTimes(Date $date)
    {
        $enforcement = new QuotaEnforcementLimit($this->stopEnforcementAmount, $this->stopEnforcementUnit);
        return $enforcement->IsWithinEnforcementTimes($date);
    }

    /**
     * @return QuotaEnforcementLimit
     */
    public function StopEnforcement()
    {
        return new QuotaEnforcementLimit($this->stopEnforcementAmount, $this->stopEnforcementUnit);
    }
}

class QuotaEnforcementLimit
{
    /**
     * @var int|null
     */
    private $amount;
    /**
     * @var QuotaDuration|string|null
     */
    private $unit;

    /**
     * @return int|null
     */
    public function Amount()
    {
        return empty($this->amount) ? null : intval($this->amount);
    }

    /**
     * @return QuotaDuration|string|null
     */
    public function Unit()
    {
        return $this->unit;
    }

    /**
     * @param int|null $amount
     * @param string|QuotaDuration|null $unit
     */
    public function __construct($amount, $unit)
    {
        $this->amount = $amount;
        $this->unit = $unit;
    }

    public function IsWithinEnforcementTimes(Date $date): bool
    {
        if (empty($this->amount) || empty($this->unit)) {
            return true;
        }

        $minutes = self::ToMinutes(intval($this->amount), $this->unit);
        $lastEnforcedTime = Date::Now()->AddMinutes($minutes);

        return $date->GreaterThan($lastEnforcedTime);
    }

    public static function ToMinutes($amount, $unit): int
    {
        if ($unit == QuotaDuration::Minute) {
            return $amount;
        }

        if ($unit == QuotaDuration::Hour) {
            return $amount * 60;
        }

        if ($unit == QuotaDuration::Day) {
            return $amount * 1440;
        }

        if ($unit == QuotaDuration::Week) {
            return $amount * 1440 * 7;
        }

        return 0;
    }

    public static function FromMinutes($minutes): QuotaEnforcementLimit
    {
        if (empty($minutes)) {
            return new QuotaEnforcementLimit(null, null);
        }

        $week = 1440 * 7;
        $day = 1440;
        $hour = 60;

        if ($minutes % $week == 0) {
            return new QuotaEnforcementLimit($minutes / $week, QuotaDuration::Week);
        }

        if ($minutes % $day == 0) {
            return new QuotaEnforcementLimit($minutes / $day, QuotaDuration::Day);
        }

        if ($minutes % $hour == 0) {
            return new QuotaEnforcementLimit($minutes / $hour, QuotaDuration::Hour);
        }

        return new QuotaEnforcementLimit($minutes, QuotaDuration::Minute);
    }

    /**
     * @return int
     */
    public function AsMinutes()
    {
        return self::ToMinutes($this->Amount(), $this->Unit());
    }
}

class QuotaUnit
{
    const Hours = 'hours';
    const Reservations = 'reservations';
}

interface IQuotaDuration
{
    /**
     * @return string QuotaDuration
     */
    public function Name();

    /**
     * @param ReservationSeries $reservationSeries
     * @param string $timezone
     * @param int $firstWeekday
     * @param int $interval
     * @return QuotaSearchDates
     */
    public function GetSearchDates(ReservationSeries $reservationSeries, $timezone, $firstWeekday, $interval);

    /**
     * @param DateRange $dateRange
     * @param int $firstWeekday
     * @return array|DateRange[]
     */
    public function Split(DateRange $dateRange, $firstWeekday);

    /**
     * @param Date $date
     * @param int $firstWeekday
     * @return string
     */
    public function GetDurationKey(Date $date, $firstWeekday);

    /**
     * @param Date $firstEnforcedDate
     * @param Date $lastEnforcedDate
     * @param int $interval
     * @param int $firstWeekday
     * @return array|DateRange[]
     */
    public function GetEnforcementRanges(Date $firstEnforcedDate, Date $lastEnforcedDate, int $interval, int $firstWeekday);
}

class QuotaSearchDates
{
    /**
     * @var \Date
     */
    private $start;

    /**
     * @var \Date
     */
    private $end;

    public function __construct(Date $start, Date $end)
    {
        $this->start = $start;
        $this->end = $end;
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
}

abstract class QuotaDuration implements IQuotaDuration
{
    const Minute = 'minute';
    const Hour = 'hour';
    const Day = 'day';
    const Week = 'week';
    const Month = 'month';
    const Year = 'year';

    /**
     * @param ReservationSeries $reservationSeries
     * @return array|Date[]
     */
    protected function GetFirstAndLastReservationDates(ReservationSeries $reservationSeries)
    {
        /** @var $instances Reservation[] */
        $instances = $reservationSeries->Instances();
        usort($instances, array('Reservation', 'Compare'));

        return array($instances[0]->StartDate(), $instances[count($instances) - 1]->EndDate());
    }

    public function __toString()
    {
        return sprintf('QuotaDuration Name=%s', $this->Name());
    }
}

class QuotaDurationHour extends QuotaDuration
{

    public function Name()
    {
        return QuotaDuration::Hour;
    }

    public function GetSearchDates(ReservationSeries $reservationSeries, $timezone, $firstWeekday, $interval)
    {
        $dates = $this->GetFirstAndLastReservationDates($reservationSeries);

        $startDate = $dates[0]->ToTimezone($timezone)->SubtractHours($interval);
        $endDate = $dates[1]->ToTimezone($timezone)->AddHours($interval);

        return new QuotaSearchDates($startDate, $endDate);
    }

    public function Split(DateRange $dateRange, $firstWeekday)
    {
        return [$dateRange];
    }

    public function GetDurationKey(Date $date, $firstWeekday)
    {
        return sprintf("%s%s%s", $date->Year(), $date->Month(), $date->Day());
    }

    public function GetEnforcementRanges(Date $firstEnforcedDate, Date $lastEnforcedDate, int $interval, int $firstWeekday)
    {
        if ($firstEnforcedDate->GreaterThanOrEqual(Date::Now())) {
            return [new DateRange($firstEnforcedDate, $lastEnforcedDate)];
        }

        $ranges = [];
        for ($i = 0; $i < $interval; $i++) {
            $rangeStart = $firstEnforcedDate->AddHours($i);
            $rangeEnd = $rangeStart->AddHours($interval);
            $ranges[] = new DateRange($rangeStart, $rangeEnd);
        }

        return $ranges;
    }
}

class QuotaDurationDay extends QuotaDuration
{
    public function GetSearchDates(ReservationSeries $reservationSeries, $timezone, $firstWeekday, $interval)
    {
        $dates = $this->GetFirstAndLastReservationDates($reservationSeries);

        $startDate = $dates[0]->ToTimezone($timezone)->GetDate()->SubtractDays($interval - 1);
        $endDate = $dates[1]->ToTimezone($timezone)->AddDays(1)->GetDate()->AddDays($interval - 1);

        return new QuotaSearchDates($startDate, $endDate);
    }

    public function Split(DateRange $dateRange, $firstWeekday)
    {
        $start = $dateRange->GetBegin();
        $end = $dateRange->GetEnd();

        $ranges = array();

        if (!$start->DateEquals($end)) {
            $currentDate = $start;

            for ($i = 1; $currentDate->DateCompare($end) < 0; $i++) {
                $ranges[] = new DateRange($currentDate, $currentDate->AddDays(1)->GetDate());
                $currentDate = $start->AddDays($i)->GetDate();
            }

            if (!$currentDate->Equals($end)) {
                $ranges[] = new DateRange($currentDate, $end);
            }
        } else {
            $ranges[] = new DateRange($start, $end);
        }

        return $ranges;
    }

    public function GetDurationKey(Date $date, $firstWeekday)
    {
        return sprintf("%s%s%s", $date->Year(), $date->Month(), $date->Day());
    }

    /**
     * @return string QuotaDuration
     */
    public function Name()
    {
        return QuotaDuration::Day;
    }

    public function GetEnforcementRanges(Date $firstEnforcedDate, Date $lastEnforcedDate, int $interval, int $firstWeekday)
    {
        if ($firstEnforcedDate->GreaterThanOrEqual(Date::Now())) {
            return [new DateRange($firstEnforcedDate, $lastEnforcedDate)];
        }

        if ($interval == 1) {
            return $this->Split(new DateRange($firstEnforcedDate, $lastEnforcedDate), $firstWeekday);
        }

        $ranges = [];
        for ($i = 0; $i < $interval; $i++) {
            $rangeStart = $firstEnforcedDate->AddDays($i);
            $rangeEnd = $rangeStart->AddDays($interval)->GetDate();
            $ranges[] = new DateRange($rangeStart, $rangeEnd);
        }

        return $ranges;
    }
}

class QuotaDurationWeek extends QuotaDuration
{
    public function GetSearchDates(ReservationSeries $reservationSeries, $timezone, $firstWeekday, $interval)
    {
        $dates = $this->GetFirstAndLastReservationDates($reservationSeries);

        $startDate = $dates[0]->ToTimezone($timezone)->GetDate();
        $selectedWeekday = $startDate->Weekday();
        $adjustedDays = ($firstWeekday - $selectedWeekday);
        if ($selectedWeekday < $firstWeekday) {
            $adjustedDays = $adjustedDays - 7;
        }
        $startDate = $startDate->AddDays($adjustedDays);

        $endDate = $dates[1]->ToTimezone($timezone);
        $daysFromWeekEnd = 7 - $endDate->Weekday() + $firstWeekday;
        if ($daysFromWeekEnd > 7) {
            $daysFromWeekEnd = $daysFromWeekEnd - 7;
        }
        $endDate = $endDate->AddDays($daysFromWeekEnd)->GetDate();

        $startDate = $startDate->SubtractDays(($interval - 1) * 7);
        $endDate = $endDate->GetDate()->AddDays(($interval - 1) * 7);

        return new QuotaSearchDates($startDate, $endDate);
    }

    public function GetDurationKey(Date $date, $firstWeekday)
    {
        $daysFromWeekStart = $date->Weekday() - $firstWeekday;
        if ($daysFromWeekStart < 0) {
            $daysFromWeekStart = $daysFromWeekStart + 7;
        }
        $firstDayOfWeek = $date->AddDays(-$daysFromWeekStart)->GetDate();
        return sprintf("%s%s%s", $firstDayOfWeek->Year(), $firstDayOfWeek->Month(), $firstDayOfWeek->Day());
    }

    public function Split(DateRange $dateRange, $firstWeekday)
    {
        $start = $dateRange->GetBegin();
        $end = $dateRange->GetEnd();

        $ranges = array();

        if (!$start->DateEquals($end)) {
            $nextWeek = $this->GetStartOfNextWeek($start, $firstWeekday);

            if ($nextWeek->LessThan($end)) {
                $ranges[] = new DateRange($start, $nextWeek);
                while ($nextWeek->LessThan($end)) {
                    $thisEnd = $this->GetStartOfNextWeek($nextWeek, $firstWeekday);

                    if ($thisEnd->LessThan($end)) {
                        $ranges[] = new DateRange($nextWeek, $thisEnd);
                    } else {
                        $ranges[] = new DateRange($nextWeek, $end);
                    }

                    $nextWeek = $thisEnd;
                }
            } else {
                $ranges[] = new DateRange($start, $end);
            }
        } else {
            $ranges[] = new DateRange($start, $end);
        }


        return $ranges;
    }

    /**
     * @param Date $date
     * @param int $firstWeekday
     * @return Date
     */
    private function GetStartOfNextWeek(Date $date, $firstWeekday)
    {
        $daysFromWeekEnd = 7 - $date->Weekday() + $firstWeekday;
        return $date->AddDays($daysFromWeekEnd)->GetDate();
    }

    /**
     * @return string QuotaDuration
     */
    public function Name()
    {
        return QuotaDuration::Week;
    }

    public function GetEnforcementRanges(Date $firstEnforcedDate, Date $lastEnforcedDate, int $interval, int $firstWeekday)
    {
        if ($firstEnforcedDate->GreaterThanOrEqual(Date::Now())) {
            return [new DateRange($firstEnforcedDate, $lastEnforcedDate)];
        }

        if ($interval == 1) {
            return $this->Split(new DateRange($firstEnforcedDate, $lastEnforcedDate), $firstWeekday);
        }

        $ranges = [];
        for ($i = 0; $i < $interval; $i++) {
            $rangeStart = $firstEnforcedDate->AddWeeks($i);
            $rangeEnd = $rangeStart->AddWeeks($interval)->GetDate();
            $ranges[] = new DateRange($rangeStart, $rangeEnd);
        }

        return $ranges;
    }
}

class QuotaDurationMonth extends QuotaDuration
{
    public function GetSearchDates(ReservationSeries $reservationSeries, $timezone, $firstWeekday, $interval)
    {
        $minMax = $this->GetFirstAndLastReservationDates($reservationSeries);

        /** @var $start Date */
        $start = $minMax[0]->ToTimezone($timezone);
        /** @var $end Date */
        $end = $minMax[1]->ToTimezone($timezone);

        $searchStart = Date::Create($start->Year(), $start->Month(), 1, 0, 0, 0, $timezone);
        $searchEnd = Date::Create($end->Year(), $end->Month() + 1, 1, 0, 0, 0, $timezone);

        if ($interval != 1) {
            $searchStart = $searchStart->AddMonths(($interval - 1) * -1);
            $searchEnd = $searchEnd->AddMonths(($interval - 1));
        }
        return new QuotaSearchDates($searchStart, $searchEnd);
    }

    public function Split(DateRange $dateRange, $firstWeekday)
    {
        $ranges = array();

        $start = $dateRange->GetBegin();
        $end = $dateRange->GetEnd();

        if (!$this->SameMonth($start, $end)) {
            $current = $start;

            while (!$this->SameMonth($current, $end)) {
                $next = $this->GetFirstOfMonth($current, 1);

                $ranges[] = new DateRange($current, $next);

                $current = $next;

                if ($this->SameMonth($current, $end)) {
                    $ranges[] = new DateRange($current, $end);
                }
            }
        } else {
            $ranges[] = $dateRange;
        }

        return $ranges;
    }

    /**
     * @param Date $date
     * @param int $monthOffset
     * @return Date
     */
    private function GetFirstOfMonth(Date $date, $monthOffset = 0)
    {
        return Date::Create($date->Year(), $date->Month() + $monthOffset, 1, 0, 0, 0, $date->Timezone());
    }

    /**
     * @param Date $d1
     * @param Date $d2
     * @return bool
     */
    private function SameMonth(Date $d1, Date $d2)
    {
        return ($d1->Month() == $d2->Month()) && ($d1->Year() == $d2->Year());
    }

    public function GetDurationKey(Date $date, $firstWeekday)
    {
        return sprintf("%s%s", $date->Year(), $date->Month());
    }

    /**
     * @return string QuotaDuration
     */
    public function Name()
    {
        return QuotaDuration::Month;
    }

    public function GetEnforcementRanges(Date $firstEnforcedDate, Date $lastEnforcedDate, int $interval, int $firstWeekday)
    {
        if ($firstEnforcedDate->GreaterThanOrEqual(Date::Now())) {
            return [new DateRange($firstEnforcedDate, $lastEnforcedDate)];
        }

        if ($interval == 1) {
            return $this->Split(new DateRange($firstEnforcedDate, $lastEnforcedDate), $firstWeekday);
        }

        $ranges = [];
        for ($i = 0; $i < $interval; $i++) {
            $rangeStart = $firstEnforcedDate->AddMonths($i);
            $rangeEnd = $rangeStart->AddMonths($interval)->GetDate();
            $ranges[] = new DateRange($rangeStart, $rangeEnd);
        }

        return $ranges;
    }
}

class QuotaDurationYear extends QuotaDuration
{
    /**
     * @return string QuotaDuration
     */
    public function Name()
    {
        return QuotaDuration::Year;
    }

    public function GetSearchDates(ReservationSeries $reservationSeries, $timezone, $firstWeekday, $interval)
    {
        $minMax = $this->GetFirstAndLastReservationDates($reservationSeries);

        /** @var $start Date */
        $start = $minMax[0]->ToTimezone($timezone);
        /** @var $end Date */
        $end = $minMax[1]->ToTimezone($timezone);

        $searchStart = Date::Create($start->Year(), 1, 1, 0, 0, 0, $timezone);
        $searchEnd = Date::Create($end->Year() + 1, 1, 1, 0, 0, 0, $timezone);

        if ($interval != 1) {
            $searchStart = $searchStart->AddYears(($interval - 1) * -1);
            $searchEnd = $searchEnd->AddYears(($interval - 1));
        }
        return new QuotaSearchDates($searchStart, $searchEnd);
    }

    public function Split(DateRange $dateRange, $firstWeekday)
    {
        $ranges = array();

        $start = $dateRange->GetBegin();
        $end = $dateRange->GetEnd();

        if (!$this->SameYear($start, $end)) {
            $current = $start;

            while (!$this->SameYear($current, $end)) {
                $next = $this->GetFirstOfYear($current, 1);

                $ranges[] = new DateRange($current, $next);

                $current = $next;

                if ($this->SameYear($current, $end)) {
                    $ranges[] = new DateRange($current, $end);
                }
            }
        } else {
            $ranges[] = $dateRange;
        }

        return $ranges;
    }

    /**
     * @param Date $date
     * @param int $yearOffset
     * @return Date
     */
    private function GetFirstOfYear(Date $date, $yearOffset = 0)
    {
        return Date::Create($date->Year() + $yearOffset, 1, 1, 0, 0, 0, $date->Timezone());
    }

    /**
     * @param Date $d1
     * @param Date $d2
     * @return bool
     */
    private function SameYear(Date $d1, Date $d2)
    {
        return ($d1->Year() == $d2->Year());
    }

    public function GetDurationKey(Date $date, $firstWeekday)
    {
        return sprintf("Y%s", $date->Year());
    }

    public function GetEnforcementRanges(Date $firstEnforcedDate, Date $lastEnforcedDate, int $interval, int $firstWeekday)
    {
        if ($firstEnforcedDate->GreaterThanOrEqual(Date::Now())) {
            return [new DateRange($firstEnforcedDate, $lastEnforcedDate)];
        }

        if ($interval == 1) {
            return $this->Split(new DateRange($firstEnforcedDate, $lastEnforcedDate), $firstWeekday);
        }

        $ranges = [];
        for ($i = 0; $i < $interval; $i++) {
            $rangeStart = $firstEnforcedDate->AddYears($i);
            $rangeEnd = $rangeStart->AddYears($interval)->GetDate();
            $ranges[] = new DateRange($rangeStart, $rangeEnd);
        }

        return $ranges;
    }
}

interface IQuotaLimit
{
    /**
     * @param Date $start
     * @param Date $end
     * @param string $key
     */
    public function TryAdd($start, $end, $key);

    /**
     * @return float
     */
    public function Amount();

    /**
     * @return string|QuotaUnit
     */
    public function Name();
}

class QuotaLimitCount implements IQuotaLimit
{
    /**
     * @var array|int[]
     */
    private $aggregateCounts = array();

    /**
     * @var int
     */
    private $totalAllowed;

    /**
     * @param float $totalAllowed
     */
    public function __construct($totalAllowed)
    {
        $this->totalAllowed = $totalAllowed;
    }

    public function TryAdd($start, $end, $key)
    {
        if (array_key_exists($key, $this->aggregateCounts)) {
            $this->aggregateCounts[$key] = $this->aggregateCounts[$key] + 1;
        } else {
            $this->aggregateCounts[$key] = 1;
        }

        if ($this->aggregateCounts[$key] > $this->totalAllowed) {
            throw new QuotaExceededException("Only {$this->totalAllowed} reservations are allowed for this duration");
        }
    }

    /**
     * @return float
     */
    public function Amount()
    {
        return $this->totalAllowed;
    }

    /**
     * @return string|QuotaUnit
     */
    public function Name()
    {
        return QuotaUnit::Reservations;
    }

    public function __toString()
    {
        return sprintf('QuotaLimitCount Name=%s, Amount=%s', $this->Name(), $this->Amount());
    }
}

class QuotaLimitHours implements IQuotaLimit
{
    /**
     * @var array|DateDiff[]
     */
    private $aggregateCounts = array();

    /**
     * @var \DateDiff
     */
    private $allowedDuration;

    /**
     * @var float
     */
    private $allowedHours;

    /**
     * @param float $allowedHours
     */
    public function __construct($allowedHours)
    {
        $this->allowedHours = $allowedHours;
        $this->allowedDuration = new DateDiff($allowedHours * 3600);
    }

    /**
     * @param Date $start
     * @param Date $end
     * @param string $key
     * @throws QuotaExceededException
     */
    public function TryAdd($start, $end, $key)
    {
        $diff = $start->GetDifference($end);

        if (array_key_exists($key, $this->aggregateCounts)) {
            $this->aggregateCounts[$key] = $this->aggregateCounts[$key]->Add($diff);
        } else {
            $this->aggregateCounts[$key] = $diff;
        }

        if ($this->aggregateCounts[$key]->GreaterThan($this->allowedDuration)) {
            throw new QuotaExceededException("Cumulative reservation length cannot exceed {$this->allowedHours} hours for this duration");
        }
    }

    /**
     * @return float
     */
    public function Amount()
    {
        return $this->allowedHours;
    }

    /**
     * @return string|QuotaUnit
     */
    public function Name()
    {
        return QuotaUnit::Hours;
    }

    public function __toString()
    {
        return sprintf('QuotaLimitHours Name=%s Amount=%s', $this->Name(), $this->Amount());
    }
}

interface IQuotaScope
{
    /**
     * @return string|QuotaScope
     */
    public function Name();

    /**
     * @param Date $startDate
     * @return Date
     */
    public function GetSearchStartDate($startDate);
}

abstract class QuotaScope implements IQuotaScope
{
    const IncludeCompleted = 'IncludeCompleted';
    const ExcludeCompleted = 'ExcludeCompleted';

    public function __toString()
    {
        return sprintf('QuotaScope Name=%s', $this->Name());
    }
}

class QuotaScopeExcluded extends QuotaScope
{
    public function Name()
    {
        return QuotaScope::ExcludeCompleted;
    }

    public function GetSearchStartDate($startDate)
    {
        if ($startDate->GreaterThan(Date::Now())) {
            return $startDate;
        }
        return Date::Now();
    }
}

class QuotaScopeIncluded extends QuotaScope
{
    public function Name()
    {
        return QuotaScope::IncludeCompleted;
    }

    public function GetSearchStartDate($startDate)
    {
        return $startDate;
    }
}

class QuotaExceededException extends Exception
{
    /**
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}

class QuotaBuilder
{
    private float $interval;
    private string $duration;
    private float $limitAmount;
    private string $limitUnit;
    private ?int $resourceId = null;
    private ?int $scheduleId = null;
    private ?int $groupId = null;
    private ?string $enforcedStart = null;
    private ?string $enforcedEnd = null;
    private array $enforcedDays = [];
    private string $scope = QuotaScope::IncludeCompleted;
    private ?Date $dateCreated = null;
    private int $quotaId = 0;
    private ?int $stopEnforcementAmount = null;
    private ?string $stopEnforcementUnit = null;

    /**
     * @param float $interval
     * @param string|QuotaDuration $duration
     * @return $this
     */
    public function WithDuration($interval, $duration): QuotaBuilder
    {
        $this->interval = floatval($interval);
        $this->duration = $duration;
        return $this;
    }

    /**
     * @param float $limit
     * @param string|QuotaUnit $unit
     * @return $this
     */
    public function WithLimit($limit, $unit): QuotaBuilder
    {
        $this->limitAmount = intval($limit);
        $this->limitUnit = $unit;
        return $this;
    }

    /**
     * @param int|null $resourceId
     * @return $this
     */
    public function WithResource($resourceId): QuotaBuilder
    {
        $this->resourceId = empty($resourceId) ? null : $resourceId;
        return $this;
    }

    /**
     * @param int|null $groupId
     * @return $this
     */
    public function WithSchedule($scheduleId): QuotaBuilder
    {
        $this->scheduleId = empty($scheduleId) ? null : $scheduleId;
        return $this;
    }

    /**
     * @param int|null $groupId
     * @return $this
     */
    public function WithGroup($groupId): QuotaBuilder
    {
        $this->groupId = empty($groupId) ? null : $groupId;
        return $this;
    }

    /**
     * @param string|null $start
     * @param string|null $end
     * @return $this
     */
    public function WithEnforcedTimes($start, $end): QuotaBuilder
    {
        $this->enforcedStart = $start;
        $this->enforcedEnd = $end;
        return $this;
    }

    /**
     * @param int[] $days
     * @return $this
     */
    public function WithEnforcedDays($days): QuotaBuilder
    {
        $this->enforcedDays = empty($days) ? [] : array_map('intval', $days);
        return $this;
    }

    /**
     * @param string|QuotaScope $scope
     * @return $this
     */
    public function WithScope($scope): QuotaBuilder
    {
        $this->scope = $scope;
        return $this;
    }

    public function WithDateCreated(Date $dateCreated): QuotaBuilder
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
     * @param int $quotaId
     * @return $this
     */
    public function WithId($quotaId): QuotaBuilder
    {
        $this->quotaId = intval($quotaId);
        return $this;
    }

    /**
     * @param int|null $amount
     * @param string|QuotaDuration $unit
     * @return $this
     */
    public function WithStopEnforcement($amount, $unit): QuotaBuilder
    {
        $this->stopEnforcementAmount = empty($amount) ? null : intval($amount);
        $this->stopEnforcementUnit = empty($unit) ? null : $unit;
        return $this;
    }

    public function Build(): Quota
    {
        $quota = Quota::Create($this->duration, $this->limitAmount, $this->limitUnit, $this->resourceId, $this->groupId, $this->scheduleId, $this->enforcedStart, $this->enforcedEnd, $this->enforcedDays, $this->scope, $this->interval, $this->dateCreated);
        $quota->WithId($this->quotaId);
        $quota->WithStopEnforcement($this->stopEnforcementAmount, $this->stopEnforcementUnit);
        return $quota;
    }
}