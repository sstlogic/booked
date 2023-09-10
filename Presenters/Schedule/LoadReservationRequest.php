<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class LoadReservationRequest
{
    /**
     * @var DateRange
     */
    private $dateRange;
    /**
     * @var int
     */
    private $scheduleId;
    /**
     * @var int[]
     */
    private $resourceIds;
    /**
     * @var Date[]
     */
    private $specificDates;

    /**
     * @var int|null
     */
    private $userId;

    /**
     * @var int|null
     */
    private $userLevel;

    /**
     * @param DateRange $dateRange
     * @param int $scheduleId
     * @param int[] $resourceIds
     * @param Date[] $specificDates
     * @param int|null $userId
     * @param int|null $userLevel
     */
    public function __construct($dateRange, $scheduleId, $resourceIds, $specificDates, $userId, $userLevel)
    {
        $this->dateRange = $dateRange;
        $this->scheduleId = $scheduleId;
        $this->resourceIds = $resourceIds;
        $this->specificDates = $specificDates;
        $this->userId = $userId;
        $this->userLevel = empty($userId) ? null : $userLevel;
    }

    /**
     * @return DateRange
     */
    public function DateRange()
    {
        return $this->dateRange;
    }

    /**
     * @return int
     */
    public function ScheduleId()
    {
        return $this->scheduleId;
    }

    /**
     * @return int[]
     */
    public function ResourceIds()
    {
        return $this->resourceIds;
    }

    /**
     * @return Date[]
     */
    public function SpecificDates()
    {
        return $this->specificDates;
    }

    /**
     * @return int
     */
    public function UserId()
    {
        return $this->userId;
    }

    public function UserLevel()
    {
        return $this->userLevel;
    }
}

class LoadReservationRequestBuilder
{
    /**
     * @var Date
     */
    private $start;
    /**
     * @var Date
     */
    private $end;
    /**
     * @var int[]
     */
    private $resourceIds = [];
    /**
     * @var int
     */
    private $scheduleId;
    /**
     * @var Date[]
     */
    private $specificDates = [];
    /**
     * @var int|null
     */
    private $userId;
    /**
     * @var int|null
     */
    private $userLevel;

    public function WithRange(Date $start, Date $end)
    {
        $this->start = $start;
        $this->end = $end->AddDays(1);
        return $this;
    }

    /**
     * @param $resourceIds int[]
     * @return LoadReservationRequestBuilder
     */
    public function WithResources($resourceIds)
    {
        $this->resourceIds = $resourceIds;
        return $this;
    }


    public function WithScheduleId($scheduleId)
    {
        $this->scheduleId = $scheduleId;
        return $this;
    }

    /**
     * @param Date[] $dates
     * @return LoadReservationRequestBuilder
     */
    public function WithSpecificDates($dates)
    {
        $this->specificDates = $dates;
        return $this;
    }

    /**
     * @param $userId string
     * @return LoadReservationRequestBuilder
     */
    public function WithUser($userId)
    {
        if (!empty($userId)) {
            $this->userId = intval($userId);
        }

        return $this;
    }

    /**
     * @param $userLevel string
     * @return LoadReservationRequestBuilder
     */
    public function WithUserLevel($userLevel)
    {
        if (!empty($userLevel)) {
            $this->userLevel = intval($userLevel);
        }

        return $this;
    }

    public function Build()
    {
        return new LoadReservationRequest(new DateRange($this->start, $this->end), $this->scheduleId, $this->resourceIds, $this->specificDates, $this->userId, $this->userLevel);
    }
}

