<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Access/ReportCommandBuilder.php');

class Report_Filter
{
    /**
     * @var int[]|int
     */
    private $resourceIds = [];

    /**
     * @var int[]|int
     */
    private $scheduleIds = [];

    /**
     * @var int[]|int
     */
    private $userIds = [];

    /**
     * @var int[]|int
     */
    private $coOwnerIds = [];

    /**
     * @var int[]|int
     */
    private $participantIds = [];

    /**
     * @var int[]|int
     */
    private $groupIds = [];

    /**
     * @var int[]|int
     */
    private $accessoryIds = [];

    /**
     * @var bool
     */
    private $includeDeleted;

    /**
     * @var int[]|int
     */
    private $resourceTypeIds = [];

    /**
     * @var AttributeValue[]
     */
    private $attributes = [];

    /**
     * @param $resourceIds int[]|null
     * @param $scheduleIds int[]|null
     * @param $userIds int|null|int[]
     * @param $groupIds int[]|null
     * @param $accessoryIds int[]|null
     * @param $participantIds int|null|int[]
     * @param $includeDeleted bool
     * @param $resourceTypeIds int[]|null
     * @param $attributes AttributeValue[]|null
     * @param $coOwnerIds int[]|null
     */
    public function __construct($resourceIds, $scheduleIds, $userIds, $groupIds, $accessoryIds, $participantIds, $includeDeleted, $resourceTypeIds, $attributes, $coOwnerIds)
    {
        $removeEmpty = function ($value) {
            return !empty($value);
        };

        if (!is_array($resourceIds)) {
            $resourceIds = [$resourceIds];
        }
        if (!is_array($scheduleIds)) {
            $scheduleIds = [$scheduleIds];
        }
        if (!is_array($groupIds)) {
            $groupIds = [$groupIds];
        }
        if (!is_array($accessoryIds)) {
            $accessoryIds = [$accessoryIds];
        }
        if (!is_array($resourceTypeIds)) {
            $resourceTypeIds = [$resourceTypeIds];
        }
        if (!is_array($coOwnerIds)) {
            $coOwnerIds = [$coOwnerIds];
        }

        $nonEmptyAttributes = [];
        if (!empty($attributes)) {
            foreach ($attributes as $a) {
                if (!empty($a->Value)) {
                    $nonEmptyAttributes[] = $a;
                }
            }
        }

        $this->resourceIds = array_filter($resourceIds, $removeEmpty);
        $this->scheduleIds = array_filter($scheduleIds, $removeEmpty);
        $this->userIds = array_filter(is_array($userIds) ? $userIds : [$userIds], $removeEmpty);
        $this->coOwnerIds = array_filter($coOwnerIds, $removeEmpty);
        $this->groupIds = array_filter($groupIds, $removeEmpty);
        $this->accessoryIds = array_filter($accessoryIds, $removeEmpty);
        $this->participantIds = array_filter(is_array($participantIds) ? $participantIds : [$participantIds], $removeEmpty);
        $this->includeDeleted = $includeDeleted;
        $this->resourceTypeIds = array_filter($resourceTypeIds, $removeEmpty);
        $this->attributes = $nonEmptyAttributes;
    }

    /**
     * @param ReportCommandBuilder $builder
     * @param CustomAttribute[] $customAttributes
     */
    public function Add(ReportCommandBuilder $builder, $customAttributes)
    {
        if (!empty($this->resourceIds)) {
            $builder->WithResourceIds($this->resourceIds);
        }
        if (!empty($this->scheduleIds)) {
            $builder->WithScheduleIds($this->scheduleIds);
        }
        if (!empty($this->userIds)) {
            $builder->WithUserIds($this->userIds);
        }
        if (!empty($this->coOwnerIds)) {
            $builder->WithCoOwnerIds($this->coOwnerIds);
        }
        if (!empty($this->participantIds)) {
            $builder->WithParticipantIds($this->participantIds);
        }
        if (!empty($this->groupIds)) {
            $builder->WithGroupIds($this->groupIds);
        }
        if (!empty($this->accessoryIds)) {
            $builder->WithAccessoryIds($this->accessoryIds);
        }
        if ($this->includeDeleted) {
            $builder->WithDeleted();
        }
        if (!empty($this->resourceTypeIds)) {
            $builder->WithResourceTypeIds($this->resourceTypeIds);
        }
        if (!empty($this->attributes)) {
            $builder->WithReservationAttributes($this->attributes, $customAttributes);
        }
    }

    /**
     * @return int[]|null
     */
    public function ResourceIds()
    {
        return $this->resourceIds;
    }

    /**
     * @return int[]|null
     */
    public function ResourceTypeIds()
    {
        return $this->resourceTypeIds;
    }

    /**
     * @return int[]|null
     */
    public function ScheduleIds()
    {
        return $this->scheduleIds;
    }

    /**
     * @return int[]
     */
    public function UserIds()
    {
        return is_array($this->userIds) ? $this->userIds : [$this->userIds];
    }

    /**
     * @return int[]
     */
    public function CoOwnerIds()
    {
        return is_array($this->coOwnerIds) ? $this->coOwnerIds : [$this->coOwnerIds];
    }

    /**
     * @return int[]|null
     */
    public function ParticipantIds()
    {
        return is_array($this->participantIds) ? $this->participantIds : [$this->participantIds];

    }

    /**
     * @return int[]|null
     */
    public function GroupIds()
    {
        return $this->groupIds;
    }

    /**
     * @return int[]|null
     */
    public function AccessoryIds()
    {
        return $this->accessoryIds;
    }

    /**
     * @return bool
     */
    public function IncludeDeleted()
    {
        return $this->includeDeleted == true;
    }

    /**
     * @return AttributeValue[]
     */
    public function Attributes()
    {
        return $this->attributes;
    }
}
