<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

class BlackoutFilter
{
	private $startDate = null;
	private $endDate = null;
	private $scheduleId = null;
	private $resourceId = null;

	/**
	 * @param Date $startDate
	 * @param Date $endDate
	 * @param int $scheduleId
	 * @param int $resourceId
	 */
	public function __construct($startDate = null, $endDate = null, $scheduleId = null, $resourceId = null)
	{
		$this->startDate = $startDate;
		$this->endDate = $endDate;
		$this->scheduleId = $scheduleId;
		$this->resourceId = $resourceId;
	}

	public function GetFilter()
	{
		$filter = new SqlFilterNull();
        $surroundFilter = null;
        $startFilter = null;
        $endFilter = null;

        if (!empty($this->startDate) && !empty($this->endDate)) {
            $surroundFilter = new SqlFilterLessThan(new SqlRepeatingFilterColumn(TableNames::BLACKOUT_INSTANCES_ALIAS, ColumnNames::RESERVATION_START, 1), $this->startDate->ToDatabase(), true);
            $surroundFilter->_And(new SqlFilterGreaterThan(new SqlRepeatingFilterColumn(TableNames::BLACKOUT_INSTANCES_ALIAS, ColumnNames::RESERVATION_END, 1), $this->endDate->AddDays(1)->ToDatabase(), true));
        }
        if (!empty($this->startDate)) {
            $startFilter = new SqlFilterGreaterThan(new SqlRepeatingFilterColumn(TableNames::BLACKOUT_INSTANCES_ALIAS, ColumnNames::RESERVATION_START, 2), $this->startDate->ToDatabase(), true);
            $endFilter = new SqlFilterGreaterThan(new SqlRepeatingFilterColumn(TableNames::BLACKOUT_INSTANCES_ALIAS, ColumnNames::RESERVATION_END, 2), $this->startDate->ToDatabase(), true);
        }
        if (!empty($this->endDate)) {
            if ($startFilter == null) {
                $startFilter = new SqlFilterLessThan(new SqlRepeatingFilterColumn(TableNames::BLACKOUT_INSTANCES_ALIAS, ColumnNames::RESERVATION_START, 3), $this->endDate->AddDays(1)->ToDatabase(), true);
            }
            else {
                $startFilter->_And(new SqlFilterLessThan(new SqlRepeatingFilterColumn(TableNames::BLACKOUT_INSTANCES_ALIAS, ColumnNames::RESERVATION_START, 4), $this->endDate->AddDays(1)->ToDatabase(), true));
            }
            if ($endFilter == null) {
                $endFilter = new SqlFilterLessThan(new SqlRepeatingFilterColumn(TableNames::BLACKOUT_INSTANCES_ALIAS, ColumnNames::RESERVATION_END, 3), $this->endDate->AddDays(1)->ToDatabase(), true);
            }
            else {
                $endFilter->_And(new SqlFilterLessThan(new SqlRepeatingFilterColumn(TableNames::BLACKOUT_INSTANCES_ALIAS, ColumnNames::RESERVATION_END, 4), $this->endDate->AddDays(1)->ToDatabase(), true));
            }
        }

		if (!empty($this->scheduleId))
		{
			$filter->_And(new SqlFilterEquals(new SqlFilterColumn(TableNames::SCHEDULES, ColumnNames::SCHEDULE_ID), $this->scheduleId));
		}
		if (!empty($this->resourceId))
		{
			$filter->_And(new SqlFilterEquals(new SqlFilterColumn(TableNames::RESOURCES_ALIAS, ColumnNames::RESOURCE_ID), $this->resourceId));
		}

        if ($surroundFilter != null || $startFilter != null || $endFilter != null) {
            $dateFilter = new SqlFilterNull(true);
            if ($surroundFilter != null) {
                $dateFilter = $dateFilter->_Or($surroundFilter);
            }
            if ($startFilter != null) {
                $dateFilter = $dateFilter->_Or($startFilter);
            }
            if ($endFilter != null) {
                $dateFilter = $dateFilter->_Or($endFilter);
            }
            $filter->_And($dateFilter);
        }

		return $filter;
	}

    public function IsFiltered()
    {
        return !empty($this->startDate) ||
            !empty($this->endDate) ||
            !empty($this->scheduleId) ||
            !empty($this->resourceId);
    }
}