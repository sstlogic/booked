<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/ApiDtos/namespace.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'lib/Application/Authentication/namespace.php');

class ManageQuotasPresenter extends ActionPresenter
{
    /**
     * @var IManageQuotasPage
     */
    private $page;
    /**
     * @var IResourceRepository
     */
    private $resourceRepository;
    /**
     * @var IGroupViewRepository
     */
    private $groupRepository;
    /**
     * @var IQuotaRepository
     */
    private $quotaRepository;
    /**
     * @var IScheduleRepository
     */
    private $scheduleRepository;

    /**
     * @param IManageQuotasPage $page
     * @param IResourceRepository $resourceRepository
     * @param IGroupViewRepository $groupRepository
     * @param IScheduleRepository $scheduleRepository
     * @param IQuotaRepository $quotaRepository
     */
    public function __construct(IManageQuotasPage    $page,
                                IResourceRepository  $resourceRepository,
                                IGroupViewRepository $groupRepository,
                                IScheduleRepository  $scheduleRepository,
                                IQuotaRepository     $quotaRepository)
    {
        parent::__construct($page);

        $this->page = $page;
        $this->resourceRepository = $resourceRepository;
        $this->groupRepository = $groupRepository;
        $this->scheduleRepository = $scheduleRepository;
        $this->quotaRepository = $quotaRepository;

        $this->AddApi("load", 'InitialLoad');
        $this->AddApi("add", 'AddQuota');
        $this->AddApi("update", 'UpdateQuota');
        $this->AddApi('delete', 'DeleteQuota');
    }

    public function InitialLoad(): ApiActionResult
    {
        Log::Debug("Load quotas");
        return new ApiActionResult(true, [
            'quotas' => QuotaApiDto::FromList($this->quotaRepository->LoadAll()),
            'resources' => ResourceApiDto::FromList($this->resourceRepository->GetResourceList()),
            'schedules' => ScheduleApiDto::FromList($this->scheduleRepository->GetAll()),
            'groups' => GroupApiDto::FromList($this->groupRepository->GetList()->Results()),
        ]);
    }

    public function AddQuota($json): ApiActionResult
    {
        /** @var QuotaApiDto $dto */
        $dto = $json;
        $duration = trim($dto->duration);
        $limitAmount = floatVal($dto->limitAmount);
        $unit = trim($dto->limitUnit);
        $interval = intval($dto->interval);
        $resourceId = empty($dto->resourceId) ? null : intval($dto->resourceId);
        $groupId = empty($dto->groupId) ? null : intval($dto->groupId);
        $scheduleId = empty($dto->scheduleId) ? null : intval($dto->scheduleId);
        $startTime = empty($dto->enforcedStartTime) ? null : trim($dto->enforcedStartTime);
        $endTime = empty($dto->enforcedEndTime) ? null : trim($dto->enforcedEndTime);
        $enforcedDays = empty($dto->enforcedDays) ? [] : array_map('intval', $dto->enforcedDays);
        $scope = trim($dto->scope);
        $stopAmount = empty($dto->stopEnforcementAmount) ? null : intval($dto->stopEnforcementAmount);
        $stopUnit = empty($dto->stopEnforcementUnit) ? null : $dto->stopEnforcementUnit;

        Log::Debug('Adding new quota.',
            ['duration' => $duration,
                'limitAmount' => $limitAmount,
                'unit' => $unit,
                'resourceId' => $resourceId,
                'groupId' => $groupId,
                'scheduleId' => $scheduleId,
                'startTime' => $startTime,
                'endTime' => $endTime,
                'enforcedDays' => $enforcedDays,
                'scope' => $scope]);

        $quota = Quota::Create($duration,
            $limitAmount,
            $unit,
            $resourceId,
            $groupId,
            $scheduleId,
            $startTime,
            $endTime,
            $enforcedDays,
            $scope,
            $interval,
            Date::Now());
        $quota->WithStopEnforcement($stopAmount, $stopUnit);

        $this->quotaRepository->Add($quota);

        return new ApiActionResult(true, QuotaApiDto::FromQuota($quota));
    }

    public function UpdateQuota($json): ApiActionResult
    {
        /** @var QuotaApiDto $dto */
        $dto = $json;
        $id = intval($dto->id);
        $duration = trim($dto->duration);
        $limitAmount = floatVal($dto->limitAmount);
        $unit = trim($dto->limitUnit);
        $interval = intval($dto->interval);
        $resourceId = empty($dto->resourceId) ? null : intval($dto->resourceId);
        $groupId = empty($dto->groupId) ? null : intval($dto->groupId);
        $scheduleId = empty($dto->scheduleId) ? null : intval($dto->scheduleId);
        $startTime = empty($dto->enforcedStartTime) ? null : trim($dto->enforcedStartTime);
        $endTime = empty($dto->enforcedEndTime) ? null : trim($dto->enforcedEndTime);
        $enforcedDays = empty($dto->enforcedDays) ? [] : array_map('intval', $dto->enforcedDays);
        $scope = trim($dto->scope);
        $stopAmount = empty($dto->stopEnforcementAmount) ? null : intval($dto->stopEnforcementAmount);
        $stopUnit = empty($dto->stopEnforcementUnit) ? null : $dto->stopEnforcementUnit;

        $quota = $this->quotaRepository->LoadById($id);
        if (empty($quota)) {
            Log::Error("Could not update quota. Not found.", ['quotaId' => $id]);
            return new ApiActionResult(false, null, new ApiErrorList(['Quota not found']));
        }

        $quota->ChangeDuration($duration);
        $quota->ChangeLimit($limitAmount, $unit);
        $quota->ChangeScope($scope);
        $quota->ChangeInterval($interval);
        $quota->ChangeResource($resourceId);
        $quota->ChangeGroup($groupId);
        $quota->ChangeSchedule($scheduleId);
        $quota->ChangeEnforcedTimes($startTime, $endTime);
        $quota->ChangeEnforcedDays($enforcedDays);
        $quota->ChangeStopEnforcement($stopAmount, $stopUnit);

        $this->quotaRepository->Update($quota);

        return new ApiActionResult(true, QuotaApiDto::FromQuota($quota));
    }

    public function DeleteQuota($json): ApiActionResult
    {
        $quotaId = intval($json->id);
        Log::Debug('Deleting quota', ['id' => $quotaId]);

        $this->quotaRepository->DeleteById($quotaId);

        return new ApiActionResult(true, ["id" => $quotaId]);
    }
}

