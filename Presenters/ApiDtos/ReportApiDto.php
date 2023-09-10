<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

class ReportApiDto
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string|null
     */
    public $dateLastSent;
    /**
     * @var ReportScheduleDetailsApiDto
     */
    public $scheduleDetails;

    public static function FromReport(SavedReport $report, $defaultEmail, $timezone): ReportApiDto
    {
        $dto = new ReportApiDto();
        $dto->id = $report->Id();
        $dto->name = apidecode($report->ReportName());
        $dto->dateLastSent = $report->LastSentDate()->ToTimezone($timezone)->Format(Resources::GetInstance()->GeneralDateTimeFormat());
        $dto->scheduleDetails = ReportScheduleDetailsApiDto::FromReport($report->ReportSchedule());
        if (empty($dto->scheduleDetails->emails)) {
            $dto->scheduleDetails->emails = [$defaultEmail];
        }
        return $dto;
    }
}