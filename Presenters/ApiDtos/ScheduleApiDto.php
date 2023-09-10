<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ScheduleApiDto
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
	 * @var bool
	 */
    public $isDefault;

    /**
     * @param $schedules Schedule[]
     * @return ScheduleApiDto[]
     */
    public static function FromList(array $schedules): array
    {
        $dtos = [];
        foreach ($schedules as $s)
        {
            $dtos[] = self::FromSchedule($s);
        }

        return $dtos;
    }

    /**
     * @param Schedule $schedule
     * @return ScheduleApiDto
     */
    public static function FromSchedule(Schedule $schedule): ScheduleApiDto
    {
        $dto = new ScheduleApiDto();
        $dto->id = intval($schedule->GetId());
        $dto->name = apidecode($schedule->GetName());
        $dto->isDefault = BooleanConverter::ConvertValue($schedule->GetIsDefault());
        return $dto;
    }
}