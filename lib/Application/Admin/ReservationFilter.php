<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

class ReservationFilter
{
    /**
     * @var Date|null
     */
    private $startDate = null;

    /**
     * @var Date|null
     */
    private $endDate = null;

    /**
     * @var null|string
     */
    private $referenceNumber = null;

    /**
     * @var int|null
     */
    private $scheduleId = null;

    /**
     * @var int|null
     */
    private $resourceId = null;

    /**
     * @var int|null
     */
    private $userId = null;

    /**
     * @var int|null
     */
    private $userType = null;

    /**
     * @var int|null
     */
    private $statusId = null;

    /**
     * @var int|null
     */
    private $resourceStatusId = null;

    /**
     * @var int|null
     */
    private $resourceStatusReasonId = null;

    /**
     * @var \Booked\Attribute[]|null
     */
    private $attributes = null;

    /**
     * @var array|ISqlFilter[]
     */
    private $_and = array();
    /**
     * @var null|string
     */
    private $title;
    /**
     * @var null|string
     */
    private $description;
    /**
     * @var bool
     */
    private $missedCheckin = false;
    /**
     * @var bool
     */
    private $missedCheckout = false;

    /**
     * @param Date $startDate
     * @param Date $endDate
     * @param string $referenceNumber
     * @param int $scheduleId
     * @param int $resourceId
     * @param int $userId
     * @param int $userType
     * @param int $statusId
     * @param int $resourceStatusId
     * @param int $resourceStatusReasonId
     * @param \Booked\Attribute[] $attributes
     * @param string $title
     * @param string $description
     * @param bool $missedCheckin
     * @param bool $missedCheckout
     */
    public function __construct($startDate = null,
                                $endDate = null,
                                $referenceNumber = null,
                                $scheduleId = null,
                                $resourceId = null,
                                $userId = null,
                                $userType = null,
                                $statusId = null,
                                $resourceStatusId = null,
                                $resourceStatusReasonId = null,
                                $attributes = null,
                                $title = null,
                                $description = null,
                                $missedCheckin = false,
                                $missedCheckout = false)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->referenceNumber = $referenceNumber;
        $this->scheduleId = $scheduleId;
        $this->resourceId = $resourceId;
        $this->userType = $userType;
        $this->userId = $userId;
        $this->statusId = $statusId;
        $this->resourceStatusId = $resourceStatusId;
        $this->resourceStatusReasonId = $resourceStatusReasonId;
        $this->attributes = $attributes;
        $this->title = $title;
        $this->description = $description;
        $this->missedCheckin = $missedCheckin;
        $this->missedCheckout = $missedCheckout;
    }

    /**
     * @param ISqlFilter $filter
     * @return ReservationFilter
     */
    public function _And(ISqlFilter $filter)
    {
        $this->_and[] = $filter;
        return $this;
    }

    public function GetFilter()
    {
        $filter = new SqlFilterNull();
        $surroundFilter = null;
        $startFilter = null;
        $endFilter = null;

        if (!empty($this->startDate) && !empty($this->endDate)) {
            $surroundFilter = new SqlFilterGreaterThan(new SqlRepeatingFilterColumn(TableNames::RESERVATION_INSTANCES_ALIAS, ColumnNames::RESERVATION_START, 1), $this->startDate->ToDatabase(), true);
            $surroundFilter->_And(new SqlFilterLessThan(new SqlRepeatingFilterColumn(TableNames::RESERVATION_INSTANCES_ALIAS, ColumnNames::RESERVATION_END, 1), $this->endDate->AddDays(1)->ToDatabase(), true));
        }
        if (!empty($this->startDate)) {
            $startFilter = new SqlFilterGreaterThan(new SqlRepeatingFilterColumn(TableNames::RESERVATION_INSTANCES_ALIAS, ColumnNames::RESERVATION_START, 2), $this->startDate->ToDatabase(), true);
            $endFilter = new SqlFilterGreaterThan(new SqlRepeatingFilterColumn(TableNames::RESERVATION_INSTANCES_ALIAS, ColumnNames::RESERVATION_END, 2), $this->startDate->ToDatabase(), true);
        }
        if (!empty($this->endDate)) {
            if ($endFilter == null) {
                $endFilter = new SqlFilterLessThan(new SqlRepeatingFilterColumn(TableNames::RESERVATION_INSTANCES_ALIAS, ColumnNames::RESERVATION_END, 3), $this->endDate->AddDays(1)->ToDatabase(), true);
            } else {
                $endFilter->_And(new SqlFilterLessThan(new SqlRepeatingFilterColumn(TableNames::RESERVATION_INSTANCES_ALIAS, ColumnNames::RESERVATION_END, 4), $this->endDate->AddDays(1)->ToDatabase(), true));
            }
        }
        if (!empty($this->referenceNumber)) {
            $filter->_And(new SqlFilterEquals(ColumnNames::REFERENCE_NUMBER, $this->referenceNumber));
        }
        if (!empty($this->scheduleId)) {
            $filter->_And(new SqlFilterEquals(new SqlFilterColumn(TableNames::RESOURCES, ColumnNames::SCHEDULE_ID), $this->scheduleId));
        }
        if (!empty($this->resourceId)) {
            $filter->_And(new SqlFilterEquals(new SqlFilterColumn(TableNames::RESOURCES, ColumnNames::RESOURCE_ID), $this->resourceId));
        }
        if (!empty($this->userId)) {
            if (empty($this->userType) || $this->userType == ReservationUserLevel::OWNER) {
                $filter->_And(new SqlFilterEquals(new SqlFilterColumn(TableNames::USERS, ColumnNames::USER_ID), $this->userId));
            } else {
                $filter->_And(new SqlFilterEquals(new SqlFilterColumn(TableNames::RESERVATION_USERS_ALIAS, ColumnNames::USER_ID), $this->userId));
            }
        }
        if (!empty($this->statusId)) {
            $filter->_And(new SqlFilterEquals(new SqlFilterColumn(TableNames::RESERVATION_SERIES_ALIAS, ColumnNames::RESERVATION_STATUS), $this->statusId));
        }
        if (!empty($this->resourceStatusId)) {
            $filter->_And(new SqlFilterEquals(new SqlFilterColumn(TableNames::RESOURCES, ColumnNames::RESOURCE_STATUS_ID), $this->resourceStatusId));
        }
        if (!empty($this->resourceStatusReasonId)) {
            $filter->_And(new SqlFilterEquals(new SqlFilterColumn(TableNames::RESOURCES, ColumnNames::RESOURCE_STATUS_REASON_ID), $this->resourceStatusReasonId));
        }
        if (!empty($this->attributes)) {
            $attributeFilter = AttributeFilter::Create(TableNames::RESERVATION_SERIES_ALIAS . '.' . ColumnNames::SERIES_ID, $this->attributes);

            if ($attributeFilter != null) {
                $filter->_And($attributeFilter);
            }
        }
        if (!empty($this->title)) {
            $filter->_And(new SqlFilterLike(ColumnNames::RESERVATION_TITLE, $this->title));
        }
        if (!empty($this->description)) {
            $filter->_And(new SqlFilterLike(new SqlFilterColumn(TableNames::RESERVATION_SERIES_ALIAS, ColumnNames::RESERVATION_DESCRIPTION), $this->description));
        }
        $requiresCheckIn = new SqlFilterEquals(new SqlFilterColumn(TableNames::RESOURCES, ColumnNames::ENABLE_CHECK_IN), 1);
        if ($this->missedCheckin) {
            $filter->_And($requiresCheckIn->_And(
                new SqlFilterEquals(new SqlFilterColumn(TableNames::RESERVATION_INSTANCES_ALIAS, ColumnNames::CHECKIN_DATE), null))->_Or(new SqlFilterGreaterThanColumn(new SqlFilterColumn(TableNames::RESERVATION_INSTANCES_ALIAS, ColumnNames::CHECKIN_DATE), new SqlFilterColumn(TableNames::RESERVATION_INSTANCES_ALIAS, ColumnNames::RESERVATION_START)))
            );
        }
        if ($this->missedCheckout) {
            $filter->_And($requiresCheckIn->_And(
                new SqlFilterEquals(new SqlFilterColumn(TableNames::RESERVATION_INSTANCES_ALIAS, ColumnNames::CHECKOUT_DATE), null))->_Or(new SqlFilterLessThanColumn(new SqlFilterColumn(TableNames::RESERVATION_INSTANCES_ALIAS, ColumnNames::CHECKOUT_DATE), new SqlFilterColumn(TableNames::RESERVATION_INSTANCES_ALIAS, ColumnNames::RESERVATION_END))));
        }

        if ($surroundFilter != null || $startFilter != null || $endFilter != null) {
            $dateFilter = new SqlFilterNull(true);
            if ($surroundFilter != null) {
                $dateFilter = $dateFilter->_Or($surroundFilter);
            } elseif ($startFilter != null) {
                $dateFilter = $dateFilter->_Or($startFilter);
            } elseif ($endFilter != null) {
                $dateFilter = $dateFilter->_Or($endFilter);
            }
            $filter->_And($dateFilter);
        }

        foreach ($this->_and as $and) {
            $filter->_And($and);
        }

        return $filter;
    }

    public function IsFiltered()
    {
        return !empty($this->startDate) ||
            !empty($this->endDate) ||
            !empty($this->referenceNumber) ||
            !empty($this->missedCheckout) ||
            !empty($this->missedCheckin) ||
            !empty($this->scheduleId) ||
            !empty($this->resourceId) ||
            !empty($this->statusId) ||
            !empty($this->resourceStatusId) ||
            !empty($this->resourceStatusReasonId) ||
            !empty($this->userId) ||
            !empty($this->description) ||
            !empty($this->title) ||
            !$this->AttributesAreEmpty();
    }

    public function GetUserLevel()
    {
        return empty($this->userType) ? null : $this->userType;
    }

    private function AttributesAreEmpty()
    {
        if (empty($this->attributes)) {
            return true;
        }

        foreach ($this->attributes as $a) {
            if (!empty($a->Value())) {
                return false;
            }
        }

        return true;
    }
}