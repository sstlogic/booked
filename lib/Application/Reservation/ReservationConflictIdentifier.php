<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

interface IReservationConflictIdentifier
{
    /**
     * @param $reservationSeries ReservationSeries
     * @return ReservationConflictResult
     */
    public function GetConflicts($reservationSeries);

    /**
     * @param array|BookableResource[] $resources
     * @param DateRange $duration
     * @param ?string $referenceNumber
     * @param IReservationRepository $reservationRepository
     * @param UserSession $userSession
     * @return array|int[]
     */
    public function GetUnavailableResourceIds(array $resources, DateRange $duration, ?string $referenceNumber, IReservationRepository $reservationRepository, UserSession $userSession);
}

class IdentifiedConflict
{
    /**
     * @var Reservation
     */
    public $Reservation;
    /**
     * @var ReservationConflictView
     */
    public $Conflict;

    /**
     * @param Reservation $reservation
     * @param ReservationConflictView $conflict
     */
    public function __construct(Reservation $reservation, ReservationConflictView $conflict)
    {
        $this->Reservation = $reservation;
        $this->Conflict = $conflict;
    }
}

class ReservationConflictIdentifier implements IReservationConflictIdentifier
{
    /**
     * @var IResourceAvailabilityStrategy
     */
    private $strategy;

    public function __construct(IResourceAvailabilityStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * @param $reservationSeries ReservationSeries
     * @return ReservationConflictResult
     */
    public function GetConflicts($reservationSeries)
    {
        /** @var IdentifiedConflict[][] $conflicts */
        $conflicts = [];

        $reservations = $reservationSeries->Instances();

        $bufferTime = $reservationSeries->MaxBufferTime();

        $keyedResources = [];
        $maxConcurrentConflicts = [];
        $anyConflictsAreBlackouts = false;

        foreach ($reservationSeries->AllResources() as $resource) {
            $keyedResources[$resource->GetId()] = $resource;
            $maxConcurrentConflicts[$resource->GetId()] = 0;
            $conflicts[$resource->GetId()] = [];
        }

        /** @var Reservation $reservation */
        foreach ($reservations as $reservation) {
            $instanceConflicts = [];
            Log::Debug('Checking for reservation conflicts', ['referenceNumber' => $reservation->ReferenceNumber(), 'startDate' => $reservation->StartDate()]);

            $startDate = $reservation->StartDate();
            $endDate = $reservation->EndDate();

            if ($bufferTime != null && !$reservationSeries->BookedBy()->IsAdmin) {
                $startDate = $startDate->SubtractInterval($bufferTime);
                $endDate = $endDate->AddInterval($bufferTime);
            }

            $existingItems = $this->strategy->GetItemsBetween($startDate, $endDate, array_keys($keyedResources));

            /** @var ReservationConflictView $existingItem */
            foreach ($existingItems as $existingItem) {
                $existingResourceId = $existingItem->GetResourceId();

                if (
                    ($bufferTime == null || $reservationSeries->BookedBy()->IsAdmin) &&
                    ($existingItem->GetStartDate()->Equals($reservation->EndDate()) || $existingItem->GetEndDate()->Equals($reservation->StartDate()))
                ) {
                    continue;
                }

                if ($this->IsInConflict($reservation, $reservationSeries, $existingItem, $keyedResources)) {
                    Log::Debug('Reference number conflicts with existing reservation',
                        ['referenceNumber' => $reservation->ReferenceNumber(),
                            'conflictingType' => get_class($existingItem),
                            'conflictingId' => $existingItem->GetId(),
                            'conflictingReferenceNumber' => $existingItem->GetReferenceNumber(),
                            'myStartDate' => $reservation->StartDate()]);


                    $instanceConflicts[$existingResourceId][] = new IdentifiedConflict($reservation, $existingItem);
                }
                $anyConflictsAreBlackouts = $anyConflictsAreBlackouts || $existingItem->GetReferenceNumber() == "";
            }

            /**
             * @var int $resourceId
             * @var BookableResource $resource
             */
            foreach ($keyedResources as $resourceId => $resource) {
                if (!array_key_exists($resourceId, $instanceConflicts)) {
                    continue;
                }
                $totalConflicts = $this->GetMaxConcurrentConflicts($instanceConflicts[$resourceId]);
                if ($totalConflicts > $maxConcurrentConflicts[$resourceId]) {
                    $maxConcurrentConflicts[$resourceId] = $totalConflicts;
                }


                $conflicts[$resourceId] = array_merge($conflicts[$resourceId], $instanceConflicts[$resourceId]);
            }
        }

        return new ReservationConflictResult($conflicts, $maxConcurrentConflicts, $anyConflictsAreBlackouts, $keyedResources);
    }

    protected function IsInConflict(Reservation $instance, ReservationSeries $series, ReservationConflictView $existingItem, $keyedResources)
    {
        if ($existingItem->GetId() == $instance->ReservationId() ||
            $series->IsMarkedForDelete($existingItem->GetId()) ||
            $series->IsMarkedForUpdate($existingItem->GetId())
        ) {
            return false;
        }

        if (array_key_exists($existingItem->GetResourceId(), $keyedResources)) {
            return $existingItem->BufferedTimes()->Overlaps($instance->Duration());
        }

        return false;
    }

    /**
     * @param IdentifiedConflict[] $instanceConflicts
     * @return int
     */
    private function GetMaxConcurrentConflicts($instanceConflicts)
    {
        if (count($instanceConflicts) <= 1) {
            return count($instanceConflicts);
        }

        if (count($instanceConflicts) == 2) {
            $c1 = $instanceConflicts[0];
            $c2 = $instanceConflicts[1];
            if ($c1->Conflict->GetReferenceNumber() != $c2->Conflict->GetReferenceNumber() && ($c1->Conflict->BufferedTimes()->Overlaps($c2->Conflict->BufferedTimes()))) {
                return 2;
            }
            return 1;
        }

        $conflicts = 0;

        $conflictsReference = array();
        foreach ($instanceConflicts as $c1) {
            $conflictsReference[$c1->Conflict->GetReferenceNumber()] = [$c1->Conflict->GetReferenceNumber()];
            foreach ($instanceConflicts as $c2) {
                if ($c1->Conflict->GetReferenceNumber() == $c2->Conflict->GetReferenceNumber()) {
                    continue;
                }
                if ($c1->Conflict->BufferedTimes()->Overlaps($c2->Conflict->BufferedTimes()) && !in_array($c2->Conflict->GetReferenceNumber(),
                        $conflictsReference[$c1->Conflict->GetReferenceNumber()])) {
                    $conflictsReference[$c1->Conflict->GetReferenceNumber()][] = $c2->Conflict->GetReferenceNumber();
                }
            }
        }

        foreach ($conflictsReference as $ref => $conflictList) {
            $maxConflicts = 0;
            foreach ($conflictList as $otherRef) {
                $maxConflicts = count(array_intersect($conflictsReference[$ref], $conflictsReference[$otherRef]));
            }

            if ($maxConflicts > $conflicts) {
                $conflicts = $maxConflicts;
            }
        }

        return $conflicts;
    }

    public function GetUnavailableResourceIds(array $resources, DateRange $duration, ?string $referenceNumber, IReservationRepository $reservationRepository, UserSession $userSession)
    {
        $unavailable = [];

        foreach ($resources as $resource) {
            if ($resource->GetAllowConcurrentReservations()) {
                $series = null;
                $existingSeries = false;
                if (!empty($referenceNumber)) {
                    $series = $reservationRepository->LoadByReferenceNumber($referenceNumber);
                    $series->UpdateDuration($duration);
                    $existingSeries = true;
                }

                if (!$existingSeries) {
                    $series = ReservationSeries::Create($userSession->UserId, $resource, "", "", $duration, new RepeatNone(), $userSession);
                }
                $conflict = $this->GetConflicts($series);

                if (!$conflict->AllowReservation()) {
                    $unavailable[] = intval($resource->GetId());
                }
            } else {
                $reserved = $this->strategy->GetItemsBetween($duration->GetBegin(), $duration->GetEnd(), [$resource->GetId()]);
                foreach ($reserved as $reservation) {
                    if (!empty($reservation->GetReferenceNumber()) && $reservation->GetReferenceNumber() == $referenceNumber) {
                        continue;
                    }

                    if ($reservation->BufferedTimes()->Overlaps($duration)) {
                        $unavailable[] = intval($reservation->GetResourceId());
                    }
                }
            }
        }

        return array_values(array_unique($unavailable));
    }
}

class ReservationConflictResult
{
    /**
     * @var IdentifiedConflict[][[]
     */
    private $conflicts;
    /**
     * @var int[]
     */
    private $maxConcurrentConflicts;
    /**
     * @var bool
     */
    private $areAnyConflictsBlackouts;
    /**
     * @var BookableResource[]
     */
    private $resourcesById;

    /**
     * @param IdentifiedConflict[][] $conflicts
     * @param int[] $maxConcurrentConflicts
     * @param bool $areAnyConflictsBlackouts
     * @param BookableResource[] $resourcesById
     */
    public function __construct($conflicts, $maxConcurrentConflicts, $areAnyConflictsBlackouts, $resourcesById)
    {
        $this->conflicts = $conflicts;
        $this->maxConcurrentConflicts = $maxConcurrentConflicts;
        $this->areAnyConflictsBlackouts = $areAnyConflictsBlackouts;
        $this->resourcesById = $resourcesById;
    }

    /**
     * @return IdentifiedConflict[]
     */
    public function Conflicts()
    {
        /** @var IdentifiedConflict[] $allConflicts */
        $allConflicts = [];
        foreach ($this->conflicts as $conflicts) {
            foreach ($conflicts as $conflict) {
                $allConflicts[] = $conflict;
            }
        }
        return $allConflicts;
    }

    /**
     * @return bool
     */
    public function AllowReservation($numberOfConflictsSkipped = 0)
    {
        if ($this->areAnyConflictsBlackouts) {
            return false;
        }

        foreach ($this->maxConcurrentConflicts as $resourceId => $conflictList) {
            if ($this->maxConcurrentConflicts[$resourceId] - $numberOfConflictsSkipped >= $this->resourcesById[$resourceId]->GetMaxConcurrentReservations()) {
                return false;
            }
        }

        return true;
    }
}