<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

class ReportFilterDto
{
    public $resourceIds = [];
    public $resourceTypeIds = [];
    public $scheduleIds = [];
    public $accessoryIds = [];
    public $groupIds = [];
    public $ownerIds = [];
    public $coOwnerIds = [];
    public $participantIds = [];
    public $includeDeleted = false;
    public $rangeStart;
    public $rangeEnd;
    public $reportRange = Report_Range::CURRENT_WEEK;
    public $reportSelection = Report_ResultSelection::FULL_LIST;
    public $reportUsage = Report_Usage::RESOURCES;
    public $reportGroupBy = Report_GroupBy::NONE;
    public $attributes = [];

    public static function FromJson($json): ReportFilterDto {
        $dto = new ReportFilterDto();
        $dto->resourceIds = isset($json->resourceIds) ? array_map('intval', $json->resourceIds) : [];
        $dto->resourceTypeIds = isset($json->resourceTypeIds) ? array_map('intval', $json->resourceTypeIds) : [];
        $dto->scheduleIds = isset($json->scheduleIds) ? array_map('intval', $json->scheduleIds) : [];
        $dto->accessoryIds = isset($json->accessoryIds) ? array_map('intval', $json->accessoryIds) : [];
        $dto->groupIds = isset($json->groupIds) ? array_map('intval', $json->groupIds) : [];
        $dto->ownerIds = isset($json->ownerIds) ? array_map('intval', $json->ownerIds) : [];
        $dto->coOwnerIds = isset($json->coOwnerIds) ? array_map('intval', $json->coOwnerIds) : [];
        $dto->participantIds = isset($json->participantIds) ? array_map('intval', $json->participantIds) : [];
        $dto->includeDeleted = isset($json->includeDeleted) ? BooleanConverter::ConvertValue($json->includeDeleted) : false;
        $dto->rangeStart = $json->rangeStart ?? "";
        $dto->rangeEnd = $json->rangeEnd ?? "";
        $dto->reportRange = $json->reportRange;
        $dto->reportSelection = $json->reportSelection;
        $dto->reportUsage = $json->reportUsage;
        $dto->reportGroupBy = $json->reportGroupBy;
        $dto->attributes = [];
        if (isset($json->attributes)) {
            /** @var $a AttributeValueApiDto */
            foreach($json->attributes as $a) {
                $dto->attributes[] = AttributeValueApiDto::Create($a->id, $a->value);
            }
        }

        return $dto;
    }
}