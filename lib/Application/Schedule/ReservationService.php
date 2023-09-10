<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

class ReservationService implements IReservationService
{
    /**
     * @var IReservationViewRepository
     */
    private $_repository;

    /**
     * @var IReservationListingFactory
     */
    private $_coordinatorFactory;

    public function __construct(IReservationViewRepository $repository, IReservationListingFactory $listingFactory)
    {
        $this->_repository = $repository;
        $this->_coordinatorFactory = $listingFactory;
    }

    public function GetReservations(DateRange $dateRangeUtc, $scheduleId, $targetTimezone, $resourceIds = null, $consolidateByReferenceNumber = false)
    {
        $filterResourcesInCode = $resourceIds != null && is_array($resourceIds) && count($resourceIds) > 100;
        $resourceKeys = array();
        if ($filterResourcesInCode) {
            $resourceKeys = array_combine($resourceIds, $resourceIds);
        }
        $reservationListing = $this->_coordinatorFactory->CreateReservationListing($targetTimezone, $dateRangeUtc);

        $reservations = $this->_repository->GetReservations($dateRangeUtc->GetBegin(), $dateRangeUtc->GetEnd(), null, null, $scheduleId,
            ($filterResourcesInCode ? array() : $resourceIds), $consolidateByReferenceNumber);
        Log::Debug('Found reservations for schedule', ['count' => count($reservations), 'scheduleId' => $scheduleId, 'dateBeginUtc' => $dateRangeUtc->GetBegin(),
            'dateEndUtc' => $dateRangeUtc->GetEnd()]);

        foreach ($reservations as $reservation) {
            if ($filterResourcesInCode && array_key_exists($reservation->ResourceId, $resourceKeys)) {
                $reservationListing->Add($reservation);
            } else {
                $reservationListing->Add($reservation);
            }
        }

        $blackouts = $this->_repository->GetBlackoutsWithin($dateRangeUtc, $scheduleId);
        Log::Debug('Found blackouts for schedule', ['count' => count($blackouts), 'scheduleId' => $scheduleId, 'dateStartUtc' => $dateRangeUtc->GetBegin(), 'dateEndUtc' => $dateRangeUtc->GetEnd()]);

        foreach ($blackouts as $blackout) {
            $reservationListing->AddBlackout($blackout);
        }

        return $reservationListing;
    }

    public function Search(DateRange $dateRange, $scheduleId, $resourceIds = null, $userId = null, $userLevel = null)
    {
        $reservations = $this->_repository->GetReservations($dateRange->GetBegin(), $dateRange->GetEnd(), $userId, $userLevel, $scheduleId, $resourceIds, false);
        $blackouts = $this->_repository->GetBlackoutsWithin($dateRange, $scheduleId, $resourceIds);

        /** @var ReservationListItem[] $items */
        $items = [];
        foreach ($reservations as $i) {
            $items[] = new ReservationListItem($i);
        }
        foreach ($blackouts as $i) {
            $items[] = new BlackoutListItem($i);
        }

        return $items;
    }
}

interface IReservationService
{
    /**
     * @param DateRange $dateRangeUtc range of dates to search against in UTC
     * @param int $scheduleId
     * @param string $targetTimezone timezone to convert the results to
     * @param null|int $resourceIds
     * @param bool $consolidateByReferenceNumber
     * @return IReservationListing
     */
    public function GetReservations(DateRange $dateRangeUtc, $scheduleId, $targetTimezone, $resourceIds = null, $consolidateByReferenceNumber = false);

    /**
     * @param DateRange $dateRange
     * @param int $scheduleId
     * @param null|int[] $resourceIds
     * @param null|int $userId
     * @param null|int $userLevel
     * @return ReservationListItem[]
     */
    public function Search(DateRange $dateRange, $scheduleId, $resourceIds = null, $userId = null, $userLevel = null);
}