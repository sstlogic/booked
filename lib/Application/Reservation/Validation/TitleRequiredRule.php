<?php
/**
 * Copyright 2018-2023 Twinkle Toes Software, LLC
 */

class TitleRequiredRule implements IReservationValidationRule
{
    public function __construct()
    {
    }

    public function Validate($reservationSeries, $retryParameters)
    {
        $title = $reservationSeries->Title();
        return new ReservationRuleResult(!empty($title), Resources::GetInstance()->GetString('TitleRequiredRule'));
    }
}