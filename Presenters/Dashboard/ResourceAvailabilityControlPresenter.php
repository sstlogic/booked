<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Controls/Dashboard/ResourceAvailabilityControl.php');

class ResourceAvailabilityControlPresenter
{
    /**
     * @var IResourceAvailabilityControl
     */
    private $control;

    /**
     * @var IResourceService
     */
    private $resourceService;

    /**
     * @var IReservationViewRepository
     */
    private $reservationViewRepository;

    public function __construct(IResourceAvailabilityControl $control,
                                IResourceService $resourceService,
                                IReservationViewRepository $reservationViewRepository)
    {
        $this->control = $control;
        $this->resourceService = $resourceService;
        $this->reservationViewRepository = $reservationViewRepository;
    }

    public function PageLoad(UserSession $user)
    {
        $now = Date::Now();

        $recentlyUsed = $this->resourceService->GetRecentlyUsedAvailableResources(false, $user);
        $favoriteResources = $this->resourceService->GetFavoriteResources($user);
        $allAccessibleResources = $this->resourceService->GetAllResources(false, $user);

        /** @var ResourceDto $resources */
        $resources = [];

        $favoriteIds = [];
        $resourceIds = [];
        foreach ($favoriteResources as $resource) {
            $favoriteIds[] = $resource->Id;
            $resourceIds[] = $resource->Id;
            $resources[] = $resource;
        }

        foreach ($recentlyUsed as $resource) {
            if (!in_array($resource->Id, $resourceIds)) {
                $resourceIds[] = $resource->Id;
                $resources[] = $resource;
            }
        }

        $reservations = $this->GetReservations($this->reservationViewRepository->GetReservations($now, $now->AddDays(7), null, null, null, $resourceIds));

        $available = array();
        $unavailable = array();
        $allday = array();

        $totalAvailable = 0;
        $totalUnavailable = 0;
        $totalUnavailableAllDay = 0;

        /** @var ResourceDto $resource */
        foreach ($resources as $resource) {
            $ongoingReservations = $this->GetOngoingReservations($resource, $reservations, Date::Now());

            if (!$resource->GetAllowConcurrentReservations()) {
                if (!empty($ongoingReservations)) {

                    $reservation = $ongoingReservations[0];
                    $lastReservationBeforeOpening = $this->GetLastReservationBeforeAnOpening($resource, $reservations);

                    if ($lastReservationBeforeOpening == null) {
                        $lastReservationBeforeOpening = $reservation;
                    }

                    if (!$reservation->EndDate->DateEquals($now)) {
                        $totalUnavailableAllDay++;
                        $allday[] = new UnavailableDashboardItem($resource, $lastReservationBeforeOpening);
                    }
                    else {
                        $totalUnavailable++;
                        $unavailable[] = new UnavailableDashboardItem($resource, $lastReservationBeforeOpening);
                    }
                }
                else {
                    $totalAvailable++;
                    $resourceId = $resource->GetId();
                    if (array_key_exists($resourceId, $reservations)) {
                        $available[] = new AvailableDashboardItem($resource, $reservations[$resourceId][0]);
                    }
                    else {
                        $available[] = new AvailableDashboardItem($resource);
                    }
                }
            }
            else {
                if (count($ongoingReservations) >= $resource->MaxConcurrentReservations) {
                    $lastReservationBeforeOpening = $this->GetLastReservationBeforeOpenConcurrentSlot($resource, $reservations);

                    if ($lastReservationBeforeOpening == null) {
                        $lastReservationBeforeOpening = $ongoingReservations[0];
                    }

                    if (!$lastReservationBeforeOpening->EndDate->DateEquals($now)) {
                        $totalUnavailableAllDay++;
                        $allday[] = new UnavailableDashboardItem($resource, $lastReservationBeforeOpening);
                    }
                    else {
                        $totalUnavailable++;
                        $unavailable[] = new UnavailableDashboardItem($resource, $lastReservationBeforeOpening);
                    }
                }
                else {
                    $totalAvailable++;
                    $next = $this->GetNextBlockedSlot($resource, $reservations);
                    $available[] = new AvailableDashboardItem($resource, $next);
                }
            }
        }

        $this->control->SetAvailable($available, $totalAvailable);
        $this->control->SetUnavailable($unavailable, $totalUnavailable);
        $this->control->SetUnavailableAllDay($allday, $totalUnavailableAllDay);
        $this->control->SetFavorites($favoriteResources, $favoriteIds);
        $this->control->SetAllResources($allAccessibleResources);
    }

    /**
     * @param ResourceDto $resource
     * @param ReservationItemView[][] $reservations
     * @return ReservationItemView[]
     */
    private function GetOngoingReservations($resource, $reservations, $time)
    {
        $resourceId = $resource->GetId();
        if (!array_key_exists($resourceId, $reservations)) {
            return [];
        }

        $ongoing = [];

        foreach ($reservations[$resourceId] as $r) {
            $times = $r->BufferedTimes();
            if ($times->GetBegin()->LessThanOrEqual($time) && $times->GetEnd()->GreaterThan($time)) {
                $ongoing[] = $r;
            }
        }

        return $ongoing;
    }

    /**
     * @param ReservationItemView[] $reservations
     * @return ReservationItemView[][]
     */
    private function GetReservations($reservations)
    {
        $indexed = array();
        foreach ($reservations as $reservation) {
            $indexed[$reservation->ResourceId][] = $reservation;
        }

        return $indexed;
    }

    /**
     * @param ResourceDto $resource
     * @param ReservationItemView[][] $reservations
     * @return null|ReservationItemView
     */
    private function GetLastReservationBeforeAnOpening($resource, $reservations)
    {
        $resourceId = $resource->GetId();
        if (!array_key_exists($resourceId, $reservations)) {
            return null;
        }

        $resourceReservations = $reservations[$resourceId];
        for ($i = 0; $i < count($resourceReservations) - 1; $i++) {
            $current = $resourceReservations[$i];
            $next = $resourceReservations[$i + 1];

            if ($current->EndDate->Equals($next->StartDate)) {
                continue;
            }

            return $current;
        }

        return $resourceReservations[count($resourceReservations) - 1];
    }

    /**
     * @param ResourceDto $resource
     * @param ReservationItemView[][] $reservations
     * @return null|ReservationItemView
     */
    private function GetLastReservationBeforeOpenConcurrentSlot($resource, $reservations)
    {
        $resourceId = $resource->GetId();
        if (!array_key_exists($resourceId, $reservations)) {
            return null;
        }

        $resourceReservations = $reservations[$resourceId];
        foreach ($resourceReservations as $r1) {
            $concurrent = $this->GetOngoingReservations($resource, $reservations, $r1->EndDate);
            if (count($concurrent) < $resource->GetMaxConcurrentReservations()) {
                return $r1;
            }
        }

        return null;
    }

    /**
     * @param ResourceDto $resource
     * @param ReservationItemView[][] $reservations
     * @return null|ReservationItemView
     */
    private function GetNextBlockedSlot($resource, $reservations)
    {
        $resourceId = $resource->GetId();
        if (!array_key_exists($resourceId, $reservations)) {
            return null;
        }

        $resourceReservations = $reservations[$resourceId];
        foreach ($resourceReservations as $r1) {
            if ($r1->BufferedTimes()->GetBegin()->GreaterThan(Date::Now())) {
                $concurrent = $this->GetOngoingReservations($resource, $reservations, $r1->StartDate);
                if (count($concurrent) >= $resource->GetMaxConcurrentReservations()) {
                    return $r1;
                }
            }
        }

        return null;
    }
}
