<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

interface IResourceAvailabilityStrategy
{
    /**
     * @param Date $startDate
     * @param Date $endDate
	 * @param int[]|int|null $resourceIds
     * @return array|ReservationConflictView[]
     */
    public function GetItemsBetween(Date $startDate, Date $endDate, $resourceIds);
}

class ResourceAvailability implements IResourceAvailabilityStrategy
{
	/**
     * @var IReservationViewRepository
     */
    protected $_repository;

    public function __construct(IReservationViewRepository $repository)
    {
        $this->_repository = $repository;
    }

    public function GetItemsBetween(Date $startDate, Date $endDate, $resourceIds, $scheduleId = null)
    {
        return $this->_repository->GetConflicts(new DateRange($startDate, $endDate), $resourceIds, $scheduleId, true);
    }
}
