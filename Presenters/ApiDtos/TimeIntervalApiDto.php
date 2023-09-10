<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class TimeIntervalApiDto
{
    public $hours;
    public $minutes;
    public $days;

    public static function Create(TimeInterval $interval): ?TimeIntervalApiDto
    {
    	if ($interval->TotalSeconds() == 0) {
    		return null;
		}
        $dto = new TimeIntervalApiDto();
        $dto->days = $interval->Days();
        $dto->hours = $interval->Hours();
        $dto->minutes = $interval->Minutes();

        return $dto;
    }

    /**
     * @param TimeIntervalApiDto|null $interval
     * @return TimeInterval|null
     */
    public static function FromApi($interval): ?TimeInterval {
        if (empty($interval)) {
            return null;
        }

        return TimeInterval::Create($interval->days, $interval->hours, $interval->minutes);
    }
}