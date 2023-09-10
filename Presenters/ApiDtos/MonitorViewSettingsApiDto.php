<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class MonitorViewSettingsApiDto extends MonitorViewSettings
{
    public function __construct()
    {
    }

    /**
     * @param MonitorViewSettings $view
     * @return MonitorViewSettingsApiDto
     */
    public static function Create($view): MonitorViewSettingsApiDto {
        $dto = new MonitorViewSettingsApiDto();
        $dto->style = $view->style;
        $dto->count = $view->count;
        $dto->attributeValue = $view->attributeValue;
        $dto->attributeId = $view->attributeId;
        $dto->scheduleId = $view->scheduleId;
        $dto->resourceIds = $view->resourceIds;
        $dto->reservationsToShow = $view->reservationsToShow;
        $dto->title = $view->title;
        $dto->showDateTime = $view->showDateTime;
        $dto->showReservations = $view->showReservations;
        $dto->showLogo = $view->showLogo;
        $dto->days = $view->days;
        $dto->scrollInterval = max($view->scrollInterval, 30);
        $dto->announcement = $view->announcement;
        $dto->startDate = $view->startDate ? Date::Parse($view->startDate)->ToSystem(): null;
        $dto->endDate = $view->endDate ? Date::Parse($view->endDate)->ToSystem() : null;
        $dto->resourcesToShow = $view->resourcesToShow;
        $dto->resourceTypeIds = $view->resourceTypeIds;
        $dto->resourceGroupIds = $view->resourceGroupIds;
        $dto->consolidateReservations = $view->consolidateReservations;
        $dto->pageSize = $view->pageSize;
        return $dto;
    }
}