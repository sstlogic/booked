<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ReservationGenericResponseApiDto {
    /**
     * @var boolean
     */
    public $success;
    /**
     * @var string[]
     */
    public $errors = [];

    public static function Create($success = true, $errors = []): ReservationGenericResponseApiDto {
        $dto = new ReservationGenericResponseApiDto();
        $dto->success = $success;
        $dto->errors = !is_array($errors) ? [$errors] : $errors;
        return $dto;
    }
}