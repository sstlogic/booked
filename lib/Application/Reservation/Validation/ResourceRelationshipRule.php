<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ResourceRelationshipRule implements IReservationValidationRule
{
    /**
     * @var IResourceRepository
     */
    private $resourceRepository;
    /**
     * @var IReservationViewRepository
     */
    private $reservationViewRepository;

    public function __construct(IResourceRepository $resourceRepository, IReservationViewRepository $reservationViewRepository)
    {
        $this->resourceRepository = $resourceRepository;
        $this->reservationViewRepository = $reservationViewRepository;
    }

    public function Validate($reservationSeries, $retryParameters)
    {
        $resources = $this->Indexed($reservationSeries->AllResources());
        $ids = $reservationSeries->AllResourceIds();

        $missingRequired = [];
        $includedExcluded = [];
        $includedExcludedAtTime = [];

        foreach ($resources as $resource) {
            $missing = $this->GetMissingRequiredResources($resource, $ids);
            if (!empty($missing)) {
                $missingRequired[$resource->GetId()] = $missing;
            }

            $excluded = $this->GetExcludedResources($resource, $ids);
            if (!empty($excluded)) {
                $includedExcluded[$resource->GetId()] = $excluded;
            }

            $excludedAtTime = $this->GetExcludedAtTime($reservationSeries, $resource);
            if (!empty($excludedAtTime)) {
                $includedExcludedAtTime[$resource->GetId()] = $excludedAtTime;
            }
        }

        $isValid = empty($missingRequired) && empty($includedExcluded) && empty($includedExcludedAtTime);
        $message = "";
        if (!$isValid) {
            $message = $this->BuildMessage($missingRequired, $includedExcluded, $includedExcludedAtTime, $resources);
        }
        return new ReservationRuleResult($isValid, $message, false);
    }

    /**
     * @param BookableResource $resource
     * @param int[] $ids
     * @return int[]
     */
    private function GetMissingRequiredResources(BookableResource $resource, $ids)
    {
        $missing = [];
        $requiredIds = array_merge($resource->GetRequiredRelationships(), $resource->GetRequiredOneWayRelationships());
        foreach ($requiredIds as $id) {
            if (!in_array($id, $ids)) {
                $missing[] = $id;
            }
        }

        return $missing;
    }

    /**
     * @param BookableResource $resource
     * @param int[] $ids
     * @return int[]
     */
    private function GetExcludedResources(BookableResource $resource, $ids)
    {
        $excluded = [];
        foreach ($resource->GetExcludedRelationships() as $id) {
            if (in_array($id, $ids)) {
                $excluded[] = $id;
            }
        }
        foreach ($resource->GetExcludedTimeRelationships() as $id) {
            if (in_array($id, $ids)) {
                $excluded[] = $id;
            }
        }

        return $excluded;
    }

    private function GetExcludedAtTime(ReservationSeries $series, BookableResource $resource)
    {
        $excluded = [];
        $excludedIds = $resource->GetExcludedTimeRelationships();
        if (empty($excludedIds)) {
            return [];
        }

        foreach ($series->Instances() as $instance) {
            $range = new DateRange($instance->StartDate()->SubtractInterval($resource->GetBufferTime()), $instance->EndDate()->AddInterval($resource->GetBufferTime()));
            $reservations = $this->reservationViewRepository->GetConflicts($range, $excludedIds);

            foreach ($reservations as $reservation) {
                $excluded[] = $reservation->GetResourceId();
            }
        }

        return array_unique($excluded);
    }

    /**
     * @param int[][] $missingRequired
     * @param int[][] $includedExcluded
     * @param int[][] $includedExcludedAtTime
     * @param BookableResource[] $resources
     * @return string
     */
    private function BuildMessage($missingRequired, $includedExcluded, $includedExcludedAtTime, $resources)
    {
        $lang = Resources::GetInstance();
        $messages = [];

        foreach ($missingRequired as $id => $missingIds) {
            foreach ($missingIds as $mid) {
                $missingResource = $this->resourceRepository->LoadById($mid);
                $currentResource = $resources[$id];
                $messages[] = $lang->GetString('ResourceMustBeBookedWith', [$currentResource->GetName(), $missingResource->GetName()]);
            }
        }

        $excludedCombos = [];
        foreach ($includedExcluded as $id => $excludedIds) {
            foreach ($excludedIds as $eid) {
                $key1 = "$id|$eid";
                $key2 = "$eid|$id";
                if (!in_array($key1, $excludedCombos) && !in_array($key2, $excludedCombos)) {
                    $excludedCombos[] = $key1;
                    $excludedCombos[] = $key2;

                    $excludedResource = $resources[$eid];
                    $currentResource = $resources[$id];
                    $messages[] = $lang->GetString('ResourceCannotBeBookedWith', [$currentResource->GetName(), $excludedResource->GetName()]);
                }
            }
        }

        foreach ($includedExcludedAtTime as $id => $excludedAtTime) {
            foreach ($excludedAtTime as $mid) {
                $excludedResource = $this->resourceRepository->LoadById($mid);
                $currentResource = $resources[$id];
                $messages[] = $lang->GetString('ResourceCannotBeBookedAtSameTimeAs', [$currentResource->GetName(), $excludedResource->GetName()]);
            }
        }

        return implode("\n", $messages);
    }

    /**
     * @param BookableResource[] $resources
     * @return BookableResource[]
     */
    private function Indexed($resources)
    {
        $indexed = [];
        foreach ($resources as $r) {
            $indexed[$r->GetId()] = $r;
        }

        return $indexed;
    }


}