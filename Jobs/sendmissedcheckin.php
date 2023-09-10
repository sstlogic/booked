<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

@define('ROOT_DIR', dirname(__FILE__) . '/../');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'Jobs/JobCop.php');
require_once(ROOT_DIR . 'Jobs/JobServer.php');
require_once(ROOT_DIR . 'Jobs/BookedJob.php');
require_once(ROOT_DIR . 'lib/Email/Messages/MissedCheckinEmail.php');
require_once(ROOT_DIR . 'lib/Email/Messages/MissedCheckinAdminEmail.php');
require_once(ROOT_DIR . 'lib/SMS/namespace.php');
require_once(ROOT_DIR . 'lib/SMS/Messages/MissedCheckinSms.php');

class MissedCheckinJob extends BookedJob
{
    public function __construct()
    {
        parent::__construct('missed-checkin', 1);
    }

    protected function Execute()
    {
        $config = Configuration::Instance();
        $sms = new SmsService();
        $smsEnabled = $sms->IsEnabled();
        $emailEnabled = $config->GetKey(ConfigKeys::ENABLE_EMAIL, new BooleanConverter());

        if (!$emailEnabled && !$smsEnabled) {
            return;
        }

        $userRepository = new UserRepository();

        $sendToAdmins = $config->GetSectionKey(ConfigSection::RESERVATION_NOTIFY, ConfigKeys::NOTIFY_MISSED_CHECKIN_APPLICATION_ADMINS, new BooleanConverter());
        $sendToGroupAdmins = $config->GetSectionKey(ConfigSection::RESERVATION_NOTIFY, ConfigKeys::NOTIFY_MISSED_CHECKIN_GROUP_ADMINS, new BooleanConverter());
        $sendToResourceAdmins = $config->GetSectionKey(ConfigSection::RESERVATION_NOTIFY, ConfigKeys::NOTIFY_MISSED_CHECKIN_RESOURCE_ADMINS, new BooleanConverter());

        $alreadySeen = array();

        $reservationViewRepository = new ReservationViewRepository();

        $now = Date::Now();
        $onlyMissedCheckinReservations = new SqlFilterFreeForm(sprintf("`%s`=1 AND `%s` IS NULL AND `%s`.`%s` BETWEEN '%s' AND '%s'",
            ColumnNames::ENABLE_CHECK_IN, ColumnNames::CHECKIN_DATE, TableNames::RESERVATION_INSTANCES_ALIAS, ColumnNames::RESERVATION_START, $now->AddMinutes(-1)->ToDatabase(), $now->ToDatabase()));
        $reservations = $reservationViewRepository->GetList(null, null, null, null, $onlyMissedCheckinReservations)->Results();

        $applicationAdmins = [];
        $groupAdmins = [];
        $resourceAdmins = [];
        $scheduleAdmins = [];
        if ($sendToAdmins) {
            $applicationAdmins = $userRepository->GetApplicationAdmins();
        }

        /** @var ReservationItemView $reservation */
        foreach ($reservations as $reservation) {
            if (array_key_exists($reservation->ReferenceNumber, $alreadySeen)) {
                continue;
            }

            $localGroupAdmins = [];
            $localResourceAdmins = [];
            $localScheduleAdmins = [];

            if ($sendToGroupAdmins) {
                if (!array_key_exists($reservation->OwnerId, $groupAdmins)) {
                    $groupAdmins[$reservation->OwnerId] = $userRepository->GetGroupAdmins($reservation->OwnerId);
                }
                $localGroupAdmins = $groupAdmins[$reservation->OwnerId];

            }
            if ($sendToResourceAdmins) {
                if (!array_key_exists($reservation->ResourceId, $resourceAdmins)) {
                    $resourceAdmins[$reservation->ResourceId] = $userRepository->GetResourceAdmins($reservation->ResourceId);
                }
                $localResourceAdmins = $resourceAdmins[$reservation->ResourceId];

                if (!array_key_exists($reservation->ScheduleId, $scheduleAdmins)) {
                    $scheduleAdmins[$reservation->ScheduleId] = $userRepository->GetScheduleAdmins($reservation->ScheduleId);
                }
                $localScheduleAdmins = $scheduleAdmins[$reservation->ScheduleId];
            }

            $admins = array_merge($localResourceAdmins, $applicationAdmins, $localGroupAdmins, $localScheduleAdmins);

            $alreadySeen[$reservation->ReferenceNumber] = 1;

            Log::Debug('Sending missed checkin email.',
                ['referenceNumber' => $reservation->ReferenceNumber, 'userId' => $reservation->UserId, 'resourceName' => $reservation->ResourceName]);

            $user = $userRepository->LoadById($reservation->OwnerId);

            if ($emailEnabled && $user->WantsEventEmail(new ReservationMissedCheckinEvent())) {
                ServiceLocator::GetEmailService()->Send(new MissedCheckinEmail($reservation, $user));
            }

            if ($smsEnabled && $user->IsSmsOptedIn() && $user->WantsEventSms(new ReservationMissedCheckinEvent())) {
                ServiceLocator::GetSmsService()->Send(new MissedCheckinSms($user, $reservation));
            }

            foreach ($admins as $admin) {
                ServiceLocator::GetEmailService()->Send(new MissedCheckinAdminEmail($reservation, $admin));
            }
        }
    }
}

$missedCheckin = new MissedCheckinJob();
$missedCheckin->Run();