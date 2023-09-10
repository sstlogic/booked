<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'Presenters/ApiDtos/namespace.php');
class ResourcesApiPresenter extends ActionPresenter
{
    /**
     * @var IResourceService
     */
    private $resourceService;
    /**
     * @var IActionPage
     */
    private $page;
    /**
     * @var IReservationConflictIdentifier
     */
    private $reservationConflictIdentifier;
    /**
     * @var UserSession
     */
    private $userSession;
    /**
     * @var IReservationRepository
     */
    private $reservationRepository;

    public function __construct(
        IResourcesApiPage              $page,
        IResourceService               $resourceService,
        IReservationConflictIdentifier $reservationConflictIdentifier,
        UserSession                    $userSession,
        IReservationRepository         $reservationRepository)
    {
        parent::__construct($page);
        $this->resourceService = $resourceService;
        $this->page = $page;
        $this->reservationConflictIdentifier = $reservationConflictIdentifier;
        $this->userSession = $userSession;
        $this->reservationRepository = $reservationRepository;

        $this->AddApi('tree', 'GetResourceTree');
        $this->AddApi('check-unavailable', 'GetUnavailableResources');
        $this->AddApi('check-availability', 'CheckResourceAvailability');
        $this->AddApi('accessory-availability', 'GetAccessoryAvailability');
        $this->AddApi('list', 'GetResourceList');
        $this->AddApi('types', 'GetResourceTypes');
    }

    public function GetResourceTree(): ApiActionResult
    {
        $scheduleId = $this->page->GetScheduleId();
        $groups = $this->resourceService->GetResourceGroupList();
        $resources = $this->resourceService->GetScheduleBookableResources($scheduleId, false, $this->userSession);

        if (empty($resources)) {
            $groups = [];
        }

        return new ApiActionResult(true, ['groups' => ResourceGroupApiDto::FromList($groups), 'resources' => ResourceApiDto::FromList($resources, [])]);
    }

    public function GetUnavailableResources(): ApiActionResult
    {
        $resources = $this->resourceService->GetScheduleBookableResources($this->page->GetScheduleId(), false, $this->userSession);
        return $this->GetAvailability($resources);
    }

    public function CheckResourceAvailability(): ApiActionResult
    {
        $ids = $this->page->GetResourceIds();
        if (empty($ids)) {
            return new ApiActionResult(false, null, new ApiErrorList(["Invalid resources"]));
        }

        $resources = $this->resourceService->GetResources($ids);
        return $this->GetAvailability($resources);
    }

    /**
     * @param BookableResource[] $resources
     * @return ApiActionResult
     */
    private function GetAvailability($resources): ApiActionResult
    {
        if (count($resources) > 50) {
            return new ApiActionResult(true, ['ids' => []]);
        }

        $duration = DateRange::Create($this->page->GetStartDate(), $this->page->GetEndDate(), $this->userSession->Timezone);
        $referenceNumber = $this->page->GetReferenceNumber();

        $unavailable = $this->reservationConflictIdentifier->GetUnavailableResourceIds($resources, $duration, $referenceNumber, $this->reservationRepository, $this->userSession);

        return new ApiActionResult(true, ['ids' => $unavailable]);
    }

    public function GetAccessoryAvailability(): ApiActionResult
    {
        $checker = new AvailableAccessoriesCheck(new AccessoryRepository(), new ReservationViewRepository());
        $start = Date::Parse($this->page->GetStartDate(), $this->userSession->Timezone);
        $end = Date::Parse($this->page->GetEndDate(), $this->userSession->Timezone);

        $availability = $checker->Check($start, $end, $this->page->GetReferenceNumber());
        return new ApiActionResult(true, $availability);
    }

    public function GetResourceList(): ApiActionResult
    {
        $resources = $this->resourceService->GetScheduleBookableResources(null, false, $this->userSession);
        return new ApiActionResult(true, ResourceApiDto::FromList($resources, []));
    }

    public function GetResourceTypes(): ApiActionResult
    {
        $types = $this->resourceService->GetResourceTypes();
        return new ApiActionResult(true, ResourceTypeApiDto::FromList($types));
    }
}