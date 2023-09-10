<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

class ReportScheduleDetailsApiDto extends SavedReportSchedule
{
    /**
     * @param SavedReportSchedule|null|stdClass $details
     * @return ReportScheduleDetailsApiDto|null
     */
    public static function FromReport($details): ?ReportScheduleDetailsApiDto
    {
        if (empty($details)) {
            $details = new SavedReportSchedule();
        }

        $dto = new ReportScheduleDetailsApiDto();
        $dto->daysOfWeek = empty($details->daysOfWeek) ? [] : $details->daysOfWeek;
        $dto->frequency = $details->frequency;
        $dto->timeOfDay = empty($details->timeOfDay) ? "00:00" : $details->timeOfDay;
        $dto->emails = apidecode($details->emails);
        $dto->timezone = $details->timezone;
        $dto->interval = empty($details->interval) ? 1 : intval($details->interval);
        $dto->dayOfMonth = empty($details->dayOfMonth) ? 1 : intval($details->dayOfMonth);
        return $dto;
    }

    /**
     * @param ReportScheduleDetailsApiDto|stdClass $scheduleDetails
     * @return SavedReportSchedule
     */
    public static function FromRequest($scheduleDetails, $timezone)
    {
        $s = new SavedReportSchedule();
        $s->interval = $scheduleDetails->interval;
        $s->emails = apiencode($scheduleDetails->emails);
        $s->timeOfDay = $scheduleDetails->timeOfDay;
        $s->timezone = $timezone;
        $s->daysOfWeek = [];
        $s->dayOfMonth = null;
        $s->frequency = $scheduleDetails->frequency;
        if ($scheduleDetails->frequency === ReportFrequency::Daily) {
            $s->frequency = ReportFrequency::Daily;
        }
        if ($scheduleDetails->frequency === ReportFrequency::Weekly) {
            $s->daysOfWeek = $scheduleDetails->daysOfWeek;
        }
        if ($scheduleDetails->frequency === ReportFrequency::Monthly) {
            $s->dayOfMonth = $scheduleDetails->dayOfMonth;
        }

        return $s;
    }
}