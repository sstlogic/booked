<?php

/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

interface IQuotaRepository
{
    /**
     * @return array|Quota[]
     */
    public function LoadAll();

    /**
     * @param int $id
     * @return Quota|null
     */
    public function LoadById($id);

    /**
     * @param Quota $quota
     * @return void
     */
    public function Add(Quota $quota);

    /**
     * @param Quota $quota
     * @return void
     */
    public function Update(Quota $quota);

    /**
     * @param $quotaId
     * @return void
     */
    public function DeleteById($quotaId);
}

interface IQuotaViewRepository
{
    /**
     * @return array|QuotaItemView[]
     */
    public function GetAll();
}

class QuotaRepository implements IQuotaRepository, IQuotaViewRepository
{
    public function LoadAll()
    {
        $quotas = [];

        $command = new GetAllQuotasCommand();
        $reader = ServiceLocator::GetDatabase()->Query($command);

        while ($row = $reader->GetRow()) {
            $quotas[] = Quota::FromRow($row);
        }
        $reader->Free();
        return $quotas;
    }

    public function LoadById($id)
    {
        $command = new GetQuotaByIdCommand($id);
        $reader = ServiceLocator::GetDatabase()->Query($command);

        if ($row = $reader->GetRow()) {
            return Quota::FromRow($row);
        }
        $reader->Free();

        return null;
    }

    public function GetAll()
    {
        $quotas = [];

        $command = new GetAllQuotasCommand();
        $reader = ServiceLocator::GetDatabase()->Query($command);

        while ($row = $reader->GetRow()) {
            $quotaId = $row[ColumnNames::QUOTA_ID];

            $limit = $row[ColumnNames::QUOTA_LIMIT];
            $unit = $row[ColumnNames::QUOTA_UNIT];
            $duration = $row[ColumnNames::QUOTA_DURATION];
            $groupName = $row['group_name'];
            $resourceName = $row['resource_name'];
            $scheduleName = $row['schedule_name'];
            $enforcedStartTime = $row[ColumnNames::ENFORCED_START_TIME];
            $enforcedEndTime = $row[ColumnNames::ENFORCED_END_TIME];
            $enforcedDays = empty($row[ColumnNames::ENFORCED_DAYS]) ? [] : explode(',', $row[ColumnNames::ENFORCED_DAYS]);
            $scope = $row[ColumnNames::QUOTA_SCOPE];
            $interval = $row[ColumnNames::QUOTA_INTERVAL];
            $stopMinutesPrior = $row[ColumnNames::QUOTA_STOP_ENFORCEMENT_MINUTES_PRIOR];

            $quotas[] = new QuotaItemView($quotaId, $limit, $unit, $duration, $groupName, $resourceName, $scheduleName, $enforcedStartTime, $enforcedEndTime, $enforcedDays, $scope, $interval, $stopMinutesPrior);
        }

        $reader->Free();
        return $quotas;
    }

    public function Add(Quota $quota)
    {
        $command = new AddQuotaCommand($quota->GetDuration()->Name(),
            $quota->GetLimit()->Amount(),
            $quota->GetLimit()->Name(),
            $quota->ResourceId(),
            $quota->GroupId(),
            $quota->ScheduleId(),
            $quota->EnforcedStartTime(),
            $quota->EnforcedEndTime(),
            $quota->EnforcedDays(),
            $quota->GetScope()->Name(),
            $quota->Interval(),
            $quota->StopEnforcement()->AsMinutes());

        $id = ServiceLocator::GetDatabase()->ExecuteInsert($command);

        $quota->WithId($id);

        return $id;
    }

    public function Update(Quota $quota)
    {
        $command = new UpdateQuotaCommand(
            $quota->Id(),
            $quota->GetDuration()->Name(),
            $quota->GetLimit()->Amount(),
            $quota->GetLimit()->Name(),
            $quota->ResourceId(),
            $quota->GroupId(),
            $quota->ScheduleId(),
            $quota->EnforcedStartTime(),
            $quota->EnforcedEndTime(),
            $quota->EnforcedDays(),
            $quota->GetScope()->Name(),
            $quota->Interval(),
            $quota->StopEnforcement()->AsMinutes());

        ServiceLocator::GetDatabase()->Execute($command);
    }

    public function DeleteById($quotaId)
    {
        //TODO:  Make this delete a quota instead of the id
        $command = new DeleteQuotaCommand($quotaId);
        ServiceLocator::GetDatabase()->Execute($command);
    }
}

class QuotaItemView
{
    public $Id;
    public $Limit;
    public $Unit;
    public $Duration;
    public $GroupName;
    public $ResourceName;
    public $ScheduleName;
    public $AllDay;
    public $Everyday;
    public $EnforcedStartTime;
    public $EnforcedEndTime;
    public $EnforcedDays;
    public $Scope;
    public $Interval;
    public $StopAmount;
    public $StopUnit;

    /**
     * @param int $quotaId
     * @param float $limit
     * @param string $unit
     * @param string $duration
     * @param string $groupName
     * @param string $resourceName
     * @param string $scheduleName
     * @param string|null $enforcedStartTime
     * @param string|null $enforcedEndTime
     * @param array|int[] $enforcedDays
     * @param string $scope
     * @param int $interval
     * @param int|null $stopMinutesPrior
     */
    public function __construct($quotaId, $limit, $unit, $duration, $groupName, $resourceName, $scheduleName, $enforcedStartTime, $enforcedEndTime,
                                $enforcedDays, $scope, $interval, $stopMinutesPrior)
    {
        $this->Id = $quotaId;
        $this->Limit = $limit;
        $this->Unit = $unit;
        $this->Duration = $duration;
        $this->GroupName = $groupName;
        $this->ResourceName = $resourceName;
        $this->ScheduleName = $scheduleName;
        $this->EnforcedStartTime = empty($enforcedStartTime) ? null : Time::Parse($enforcedStartTime);
        $this->EnforcedEndTime = empty($enforcedEndTime) ? null : Time::Parse($enforcedEndTime);
        $this->EnforcedDays = empty($enforcedDays) ? array() : $enforcedDays;
        $this->AllDay = empty($enforcedStartTime) || empty($enforcedEndTime);
        $this->Everyday = empty($enforcedDays);
        $this->Scope = empty($scope) ? QuotaScope::IncludeCompleted : $scope;
        $this->Interval = empty($interval) ? 1 : intval($interval);
        $enforcement = QuotaEnforcementLimit::FromMinutes($stopMinutesPrior);
        $this->StopAmount = $enforcement->Amount();
        $this->StopUnit = $enforcement->Unit();
    }
}