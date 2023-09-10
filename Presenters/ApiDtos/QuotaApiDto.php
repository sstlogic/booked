<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

class QuotaApiDto
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var int|null
     */
    public $resourceId;
    /**
     * @var int|null
     */
    public $groupId;
    /**
     * @var int|null
     */
    public $scheduleId;
    /**
     * @var int
     */
    public $interval;
    /**
     * @var int[]
     */
    public $enforcedDays;
    /**
     * @var string|null
     */
    public $enforcedStartTime;
    /**
     * @var string|null
     */
    public $enforcedEndTime;
    /**
     * @var string
     */
    public $duration;
    /**
     * @var string
     */
    public $limitUnit;
    /**
     * @var float
     */
    public $limitAmount;
    /**
     * @var string
     */
    public $scope;
    /**
     * @var int|null
     */
    public $stopEnforcementAmount;
    /**
     * @var string|null
     */
    public $stopEnforcementUnit;
    /**
     * @var Date
     */
    public $dateCreated;

    /**
     * @param Quota[] $quotas
     * @return QuotaApiDto[]
     */
    public static function FromList(array $quotas): array
    {
        return array_map(function($q) {return self::FromQuota($q);}, $quotas);
    }

    /**
     * @param Quota $quota
     * @return QuotaApiDto
     */
    public static function FromQuota(Quota $quota): QuotaApiDto
    {
        $dto = new QuotaApiDto();
        $dto->id = intval($quota->Id());
        $dto->resourceId = empty($quota->ResourceId()) ? null : intval($quota->ResourceId());
        $dto->scheduleId = empty($quota->ScheduleId()) ? null : intval($quota->ScheduleId());
        $dto->groupId = empty($quota->GroupId()) ? null : intval($quota->GroupId());
        $dto->duration = $quota->GetDuration()->Name();
        $dto->limitUnit = $quota->GetLimit()->Name();
        $dto->limitAmount = floatval($quota->GetLimit()->Amount());
        $dto->interval = $quota->Interval();
        $dto->enforcedDays = array_map('intval', $quota->EnforcedDays());
        $dto->enforcedStartTime = empty($quota->EnforcedStartTime()) ? null : $quota->EnforcedStartTime()->Format("H:i");
        $dto->enforcedEndTime = empty($quota->EnforcedEndTime()) ? null : $quota->EnforcedEndTime()->Format("H:i");
        $dto->dateCreated = $quota->DateCreated()->IsNull() ? Date::Min()->ToSystem() : $quota->DateCreated()->ToSystem();
        $dto->scope = $quota->GetScope()->Name();
        $dto->stopEnforcementUnit = $quota->StopEnforcement()->Unit();
        $dto->stopEnforcementAmount = $quota->StopEnforcement()->Amount();
        return $dto;

    }
}