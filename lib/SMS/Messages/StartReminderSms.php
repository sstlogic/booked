<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once ROOT_DIR . 'lib/SMS/SmsMessage.php';

class StartReminderSms extends SmsMessage
{
    public function __construct(User $user, ReminderNotice $notice)
    {
        $resources = Resources::GetInstance();
        $resources->SetLanguage($user->Language());

        $format = $resources->GetDateFormat('sms_datetime');
        $url = ReservationUrl::Create($notice->ReferenceNumber());
        $message = $resources->GetString('SMSMessageReservationStartReminder', [$notice->ResourceName(), $notice->StartDate()->ToTimezone($user->Timezone())->Format($format), $url]);
        parent::__construct($user->PhoneWithCountryCode(), $message);
    }
}