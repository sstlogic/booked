<?php
/**
 * Copyright 2018-2023 Twinkle Toes Software, LLC
 */

class DescriptionRequiredRule implements IReservationValidationRule
{
    public function __construct()
    {
    }

    public function Validate($reservationSeries, $retryParameters)
    {
        $description = $reservationSeries->Description();
        return new ReservationRuleResult(!empty($description), Resources::GetInstance()->GetString('DescriptionRequiredRule'));
    }
}