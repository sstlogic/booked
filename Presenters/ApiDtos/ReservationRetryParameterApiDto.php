<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ReservationRetryParameterApiDto
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $value;

    public static function Create($name, $value): ReservationRetryParameterApiDto {
        $dto = new ReservationRetryParameterApiDto();
        $dto->name = $name;
        $dto->value = $value;
        return $dto;
    }
}
