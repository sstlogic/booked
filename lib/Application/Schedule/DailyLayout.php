<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Common/Helpers/StopWatch.php');
require_once(ROOT_DIR . 'Domain/ScheduleLayout.php');
require_once(ROOT_DIR . 'Domain/SchedulePeriod.php');

interface IDailyLayout
{
    /**
     * @param Date $date
     * @param int $resourceId
     * @return array|IReservationSlot[]
     */
    function GetLayout(Date $date, $resourceId);

    /**
     * @param Date $date
     * @return bool
     */
    function IsDateReservable(Date $date);

    /**
     * @param Date $displayDate
     * @return string[]
     */
    function GetLabels(Date $displayDate);

    /**
     * @param Date $displayDate
     * @param bool $fitToHours
     * @return SchedulePeriod[]
     */
    function GetPeriods(Date $displayDate, $fitToHours = false);

    /**
     * @param Date $date
     * @param int $resourceId
     * @return DailyReservationSummary
     */
    function GetSummary(Date $date, $resourceId);

    /**
     * @return string
     */
    public function Timezone();
}

class DailyLayout implements IDailyLayout
{
    /**
     * @var IReservationListing
     */
    private $_reservationListing;

    /**
     * @var IScheduleLayout
     */
    private $_scheduleLayout;

    /**
     * @param IReservationListing $listing
     * @param IScheduleLayout $layout
     */
    public function __construct(IReservationListing $listing, IScheduleLayout $layout)
    {
        $this->_reservationListing = $listing;
        $this->_scheduleLayout = $layout;
    }

    public function Timezone()
    {
        return $this->_scheduleLayout->Timezone();
    }

    public function GetLayout(Date $date, $resourceId)
    {
        try {
            $hideBlocked = Configuration::Instance()->GetSectionKey(ConfigSection::SCHEDULE, ConfigKeys::SCHEDULE_HIDE_BLOCKED_PERIODS, new BooleanConverter());
            $sw = new StopWatch();
            $sw->Start();

            $items = $this->_reservationListing->OnDateForResource($date, $resourceId);
            $sw->Record('listing');

            $list = new ScheduleReservationList($items, $this->_scheduleLayout, $date, $hideBlocked);
            $slots = $list->BuildSlots();
            $sw->Record('slots');
            $sw->Stop();

            Log::Debug(sprintf('DailyLayout::GetLayout - For resourceId %s on date %s, took %s seconds to get reservation listing, %s to build the slots, %s total seconds for %s reservations. Memory consumed=%sMB',
                $resourceId,
                $date->ToString(),
                $sw->GetRecordSeconds('listing'),
                $sw->TimeBetween('slots', 'listing'),
                $sw->GetTotalSeconds(),
                count($items),
                round(memory_get_usage() / 1048576, 2)));

            return $slots;
        } catch (Exception $ex) {
            Log::Error('Error getting layout', ['date' => $date->ToString(), 'resourceId' => $resourceId, 'exception' => $ex]);
            throw($ex);
        }
    }

    public function GetSummary(Date $date, $resourceId)
    {
        $summary = new DailyReservationSummary();

        $items = $this->_reservationListing->OnDateForResource($date, $resourceId);
        if (count($items) > 0) {
            foreach ($items as $item) {
                $summary->AddReservation($item);
            }
        }

        return $summary;
    }

    public function IsDateReservable(Date $date)
    {
        return $date->DateCompare(Date::Now()) >= 0;
    }

    public function GetLabels(Date $displayDate)
    {
        $hideBlocked = Configuration::Instance()->GetSectionKey(ConfigSection::SCHEDULE, ConfigKeys::SCHEDULE_HIDE_BLOCKED_PERIODS, new BooleanConverter());

        $labels = array();

        $periods = $this->_scheduleLayout->GetLayout($displayDate, $hideBlocked);

        if ($periods[0]->BeginsBefore($displayDate)) {
            $labels[] = $periods[0]->Label($displayDate->GetDate());
        } else {
            $labels[] = $periods[0]->Label();
        }

        for ($i = 1; $i < count($periods); $i++) {
            $labels[] = $periods[$i]->Label();
        }

        return $labels;
    }

    public function GetPeriods(Date $displayDate, $fitToHours = false)
    {
        $hideBlocked = Configuration::Instance()->GetSectionKey(ConfigSection::SCHEDULE, ConfigKeys::SCHEDULE_HIDE_BLOCKED_PERIODS, new BooleanConverter());

        $periods = $this->_scheduleLayout->GetLayout($displayDate, $hideBlocked);

        if (!$fitToHours || !$this->_scheduleLayout->FitsToHours()) {
            return $periods;
        }

        /** @var $periodsToReturn SpanablePeriod[] */
        $periodsToReturn = array();
        for ($i = 0; $i < count($periods); $i++) {
            $currentPeriod = $periods[$i];
            $periodStart = $currentPeriod->BeginDate();
            $periodLength = $periodStart->GetDifference($currentPeriod->EndDate())->TotalMinutes();

            if (!$currentPeriod->IsLabelled() && ($periodStart->Minute() == 0 && $periodLength <= 30)) {
                $span = 1;

                $nextHour = $currentPeriod->Begin()->Hour() + 1;

                if ($nextHour < 24) {
                    $tempPeriod = $currentPeriod;
                    while ($tempPeriod->BeginDate()->Hour() < $nextHour) {
                        $span++;
                        $nextIndex = $span + $i;
                        if (count($periods) > $nextIndex) {
                            $tempPeriod = $periods[$nextIndex];
                        } else {
                            break;
                            $tempPeriod = $periods[count($periods) - 1];
                        }
                    }
                } else {
                    $span = count($periods) - $i;
                }

                $i += $span - 1;

                $periodsToReturn[] = new SpanablePeriod($currentPeriod, $span);
            } else {
                $periodsToReturn[] = new SpanablePeriod($currentPeriod);
            }
        }

        return $periodsToReturn;
    }
}

interface IDailyLayoutFactory
{
    /**
     * @param IReservationListing $listing
     * @param IScheduleLayout $layout
     * @return IDailyLayout
     */
    function Create(IReservationListing $listing, IScheduleLayout $layout);
}

class DailyLayoutFactory implements IDailyLayoutFactory
{
    public function Create(IReservationListing $listing, IScheduleLayout $layout)
    {
        return new DailyLayout($listing, $layout);
    }
}

class SpanablePeriod extends SchedulePeriod
{
    private $span = 1;
    private $period;

    public function __construct(SchedulePeriod $period, $span = 1)
    {
        $this->span = $span;
        $this->period = $period;
        parent::__construct($period->BeginDate(), $period->EndDate(), $period->_label);

    }

    public function Span()
    {
        return $this->span;
    }

    public function SetSpan($span)
    {
        $this->span = $span;
    }

    public function IsReservable()
    {
        return $this->period->IsReservable();
    }
}