<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/ApiDtos/ReservationApiDto.php');

class ReservationSaveRequestApiDto
{
    /**
     * @var ReservationApiDto
     */
    public $reservation;
    /**
     * @var ReservationRetryParameterApiDto[]
     */
    public $retryParameters = [];
    /**
     * @var SeriesUpdateScope|string
     */
    public $updateScope;
}