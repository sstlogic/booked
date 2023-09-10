<?php
/**
 * Copyright 2013-2023 Twinkle Toes Software, LLC
 */

interface IScheduleResourceFilter
{
    /**
     * @param BookableResource[] $resources
     * @param IResourceRepository $resourceRepository
     * @param IAttributeService $attributeService
     * @param UserSession $userSession
     * @return int[] filtered resource ids
     */
    public function FilterResources($resources, IResourceRepository $resourceRepository,
                                    IAttributeService $attributeService, UserSession $userSession);
}

class ScheduleResourceFilter implements IScheduleResourceFilter
{
    public $ScheduleId;
    public $ResourceIds;
    public $ResourceTypeId;
    public $MinCapacity;
    public $ResourceAttributes;
    public $ResourceTypeAttributes;

    /**
     * @param int|null $scheduleId
     * @param int|null $resourceTypeId
     * @param int|null $minCapacity
     * @param AttributeValue[]|null $resourceAttributes
     * @param AttributeValue[]|null $resourceTypeAttributes
     * @param int[]|null $resourceIds
     */
    public function __construct($scheduleId = null,
                                $resourceTypeId = null,
                                $minCapacity = null,
                                $resourceAttributes = null,
                                $resourceTypeAttributes = null,
                                $resourceIds = null)
    {
        $this->ScheduleId = $scheduleId;
        $this->ResourceTypeId = $resourceTypeId;
        $this->MinCapacity = empty($minCapacity) ? null : $minCapacity;
        $this->ResourceAttributes = empty($resourceAttributes) ? [] : $resourceAttributes;
        $this->ResourceTypeAttributes = empty($resourceTypeAttributes) ? [] : $resourceTypeAttributes;
        if (empty($resourceIds)) {
            $resourceIds = [];
        }
        if (!is_array($resourceIds)) {
            $resourceIds = [$resourceIds];
        }
        $this->ResourceIds = array_filter($resourceIds, function ($r) {
            return !empty($r);
        });
    }

    /**
     * @return ISqlFilter
     */
    public function AsSqlFilter()
    {
        $filter = new SqlFilterNull();
        if (!empty($this->ScheduleId)) {
            $filter->_And(new SqlFilterEquals(new SqlFilterColumn(TableNames::RESOURCES_ALIAS, ColumnNames::SCHEDULE_ID), $this->ScheduleId));
        }
        if (!empty($this->ResourceIds)) {
            $filter->_And(new SqlFilterIn(new SqlFilterColumn(TableNames::RESOURCES_ALIAS, ColumnNames::RESOURCE_ID), $this->ResourceIds));
        }

        return $filter;
    }

    public static function FromCookie($val)
    {
        if (empty($val)) {
            return new ScheduleResourceFilter();
        }

        return new ScheduleResourceFilter($val->ScheduleId,
            $val->ResourceTypeId,
            $val->MinCapacity,
            $val->ResourceAttributes,
            $val->ResourceTypeAttributes,
            isset($val->ResourceIds) ? $val->ResourceIds : null);
    }

    public function HasFilter()
    {
        return !empty($this->ResourceIds) || !empty($this->ResourceTypeId) || !empty($this->MinCapacity) || !empty($this->ResourceAttributes) || !empty($this->ResourceTypeAttributes);
    }

    public function FilterResources($resources, IResourceRepository $resourceRepository, IAttributeService $attributeService, UserSession $user)
    {
        $resourceIds = [];

        if (!$this->HasFilter()) {
            foreach ($resources as $resource) {
                $resourceIds[] = $resource->GetId();
            }

            return $resourceIds;
        }

        $resourceAttributeValues = null;
        if (!empty($this->ResourceAttributes)) {
            $allResourceIds = array_map(function ($r) {
                return $r->GetId();
            }, $resources);
            $resourceAttributeValues = $attributeService->GetAttributes(CustomAttributeCategory::RESOURCE, $user, $allResourceIds);
        }

        $resourceTypeAttributeValues = null;
        if (!empty($this->ResourceTypeAttributes)) {
            $resourceTypeIds = array_map(function ($r) {
                return $r->Id();
            }, $resourceRepository->GetResourceTypes());

            $resourceTypeAttributeValues = $attributeService->GetAttributes(CustomAttributeCategory::RESOURCE_TYPE, $user, $resourceTypeIds);
        }

        foreach ($resources as $resource) {
            if (!empty($this->ResourceIds) && !in_array($resource->GetId(), $this->ResourceIds)) {
                continue;
            }

            if (!empty($this->MinCapacity) && $resource->GetMaxParticipants() < $this->MinCapacity) {
                continue;
            }

            if (!empty($this->ResourceTypeId) && $resource->GetResourceTypeId() != $this->ResourceTypeId) {
                continue;
            }

            $resourceAttributesPass = true;
            if (!empty($this->ResourceAttributes)) {
                $values = $resourceAttributeValues->GetAttributes($resource->GetId());

                /** var @attribute AttributeValue */
                foreach ($this->ResourceAttributes as $attribute) {
                    $value = $this->GetAttribute($values, $attribute->AttributeId);

                    if (!$this->AttributeValueMatches($attribute, $value)) {
                        $resourceAttributesPass = false;
                        break;
                    }
                }
            }

            if (!$resourceAttributesPass) {
                continue;
            }

            $resourceTypeAttributesPass = true;

            if (!empty($this->ResourceTypeAttributes)) {
                if (!$resource->HasResourceType()) {
                    // there's a filter but this resource doesn't have a resource type
                    continue;
                }
                $values = $resourceTypeAttributeValues->GetAttributes($resource->GetResourceTypeId());

                /** var @attribute AttributeValue */
                foreach ($this->ResourceTypeAttributes as $attribute) {
                    $value = $this->GetAttribute($values, $attribute->AttributeId);
                    if (!$this->AttributeValueMatches($attribute, $value)) {
                        $resourceTypeAttributesPass = false;
                        break;
                    }
                }
            }

            if (!$resourceTypeAttributesPass) {
                continue;
            }

            $resourceIds[] = $resource->GetId();
        }

        return $resourceIds;
    }

    /**
     * @param \Booked\Attribute[] $attributes
     * @param int $attributeId
     * @return null|\Booked\Attribute
     */
    private function GetAttribute($attributes, $attributeId)
    {
        foreach ($attributes as $attribute) {
            if ($attribute->Id() == $attributeId) {
                return $attribute;
            }
        }
        return null;
    }

    /**
     * @param AttributeValue $attribute
     * @param \Booked\Attribute $value
     * @return bool
     */
    private function AttributeValueMatches($attribute, $value)
    {
        if ($value == null) {
            return false;
        }

        if ($value->Type() == CustomAttributeTypes::SINGLE_LINE_TEXTBOX || $value->Type() == CustomAttributeTypes::MULTI_LINE_TEXTBOX) {
            return strripos(strtolower(trim($value->Value())), strtolower(trim($attribute->Value))) !== false;
        } elseif (is_numeric($value->Value())) {
            return floatval($value->Value()) == $attribute->Value;
        } else {
            return strtolower(trim($value->Value())) == strtolower(trim($attribute->Value));
        }
    }
}