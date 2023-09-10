<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once ROOT_DIR . 'lib/SMS/SmsMessage.php';

class SeriesEndingSms extends SmsMessage
{
    public function __construct(User $user, ReservationSeries $series)
    {
        $resources = Resources::GetInstance();
        $resources->SetLanguage($user->Language());

        $format = $resources->GetDateFormat('sms_datetime');
        $url = ReservationUrl::Create($series->CurrentInstance()->ReferenceNumber());
        $message = $resources->GetString('SMSMessageReservationSeriesEndingReminder', [$series->Resource()->GetName(), $series->CurrentInstance()->StartDate()->ToTimezone($user->Timezone())->Format($format), $url]);
        parent::__construct($user->PhoneWithCountryCode(), $message);
    }
}