<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once ROOT_DIR . 'lib/SMS/SmsMessage.php';

class MissedCheckinSms extends SmsMessage
{
    public function __construct(User $user, ReservationItemView $reservation)
    {
        $resources = Resources::GetInstance();
        $resources->SetLanguage($user->Language());

        $format = $resources->GetDateFormat('sms_datetime');
        $url = ReservationUrl::Create($reservation->ReferenceNumber);
        $message = $resources->GetString('SMSMessageReservationMissedCheckin', [$reservation->GetResourceName(), $reservation->GetStartDate()->ToTimezone($user->Timezone())->Format($format), $url]);
        parent::__construct($user->PhoneWithCountryCode(), $message);
    }
}