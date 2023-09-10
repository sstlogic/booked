<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ReservationDeleteRequestApiDto
{
    /**
     * @var string
     */
    public $referenceNumber;
    /**
     * @var SeriesUpdateScope
     */
    public $scope;
    /**
     * @var string
     */
    public $reason;
}