<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ReservationConflictView
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $seriesId;

    /**
     * @var string
     */
    protected $referenceNumber;

    /**
     * @var Date
     */
    protected $startDate;

    /**
     * @var Date
     */
    protected $endDate;

    /**
     * @var int
     */
    protected $resourceId;

    /**
     * @var string
     */
    protected $resourceName;

    /**
     * @var TimeInterval|null
     */
    protected $bufferTime;

    /**
     * @var Date
     */
    protected $bufferedStartDate;

    /**
     * @var Date
     */
    protected $bufferedEndDate;

    /**
     * @param int $id
     * @param int $seriesId
     * @param string $referenceNumber
     * @param Date $startDate
     * @param Date $endDate
     * @param int $resourceId
     * @param string $resourceName
     * @param TimeInterval|null $bufferTime
     */
    public function __construct($id, $seriesId, $referenceNumber, $startDate, $endDate, $resourceId, $resourceName, $bufferTime)
    {
        $this->id = $id;
        $this->seriesId = $seriesId;
        $this->referenceNumber = $referenceNumber;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->resourceId = $resourceId;
        $this->resourceName = $resourceName;
        $this->bufferTime = $bufferTime;
        $this->bufferedStartDate = $startDate;
        $this->bufferedEndDate = $endDate;

        if ($this->bufferTime != null) {
            $this->bufferedStartDate = $this->GetStartDate()->SubtractInterval($this->bufferTime);
            $this->bufferedEndDate = $this->GetEndDate()->AddInterval($this->bufferTime);
        }
    }

    /**
     * @return Date
     */
    public function GetStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return Date
     */
    public function GetEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return DateRange
     */
    public function GetDateRange()
    {
        return new DateRange($this->startDate, $this->endDate);
    }

    /**
     * @return Date
     */
    public function GetBufferedStartDate()
    {
        return $this->bufferedStartDate;
    }

    /**
     * @return Date
     */
    public function GetBufferedEndDate()
    {
        return $this->bufferedEndDate;
    }

    /**
     * @return int
     */
    public function GetResourceId()
    {
        return $this->resourceId;
    }

    /**
     * @return mixed
     */
    public function GetResourceName()
    {
        return $this->resourceName;
    }

    /**
     * @return int
     */
    public function GetId()
    {
        return $this->id;
    }


    /**
     * @return string
     */
    public function GetReferenceNumber()
    {
        return $this->referenceNumber;
    }

    /**
     * @return TimeInterval|null
     */
    public function GetBufferTime()
    {
        return $this->bufferTime;
    }

    /**
     * @return bool
     */
    public function HasBufferTime()
    {
        return $this->bufferTime != null;
    }

    /**
     * @return DateRange
     */
    public function BufferedTimes()
    {
        return new DateRange($this->bufferedStartDate, $this->bufferedEndDate);
    }
}