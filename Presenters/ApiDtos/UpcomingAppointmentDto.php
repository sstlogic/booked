<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class UpcomingAppointmentDto {
    /**
     * @var string
     */
    public $start;
    /**
     * @var string
     */
    public $end;

    /**
     * @param SchedulePeriod[] $periods
     */
    public static function FromPeriods($periods)
    {
        $dtos = [];

        foreach ($periods as $p) {
            $dto = new UpcomingAppointmentDto();
            $dto->start = $p->BeginDate()->ToSystem();
            $dto->end = $p->EndDate()->ToSystem();
            $dtos[] = $dto;
        }
        return $dtos;
    }
}