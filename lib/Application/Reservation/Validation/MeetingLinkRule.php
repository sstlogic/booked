<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

class MeetingLinkRule implements IReservationValidationRule
{
    public function __construct()
    {
    }

    public function Validate($reservationSeries, $retryParameters)
    {
        $meetingLink = $reservationSeries->MeetingLink();
        if ($meetingLink == null || $meetingLink->Type() != ReservationMeetingLinkType::Link) {
            return new ReservationRuleResult();
        }

        $url = $meetingLink->Url();
        return new ReservationRuleResult($url != "" && filter_var($url, FILTER_VALIDATE_URL) !== false, Resources::GetInstance()->GetString('InvalidMeetingUrlError'));
    }
}
