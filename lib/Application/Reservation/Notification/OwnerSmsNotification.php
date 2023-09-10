<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Common/namespace.php');

abstract class OwnerSmsNotification implements IReservationNotification
{
    protected $userRepository;

    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function Notify($reservationSeries)
    {
        $owner = $this->userRepository->LoadById($reservationSeries->UserId());

        if ($owner->IsSmsOptedIn() && $owner->WantsEventSms($this->GetReservationEvent())) {
            $resources = Resources::GetInstance();
            $resources->SetLanguage($owner->Language());

            $key = $resources->GetDateFormat('sms_datetime');
            $date = $reservationSeries->CurrentInstance()->StartDate()->ToTimezone($owner->Timezone())->Format($key);
            $url = ReservationUrl::Create($reservationSeries->CurrentInstance()->ReferenceNumber());
            $message = $resources->GetString($this->GetMessageKey(), [$reservationSeries->Resource()->GetName(), $date, $url]);

            ServiceLocator::GetSmsService()->Send(new SmsMessage($owner->PhoneWithCountryCode(), $message));
        }
    }

    protected abstract function GetReservationEvent(): IDomainEvent;

    protected abstract function GetMessageKey(): string;
}