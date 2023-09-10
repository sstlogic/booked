<?php
/**
 * Copyright 2013-2023 Twinkle Toes Software, LLC
 */

@define('ROOT_DIR', dirname(__FILE__) . '/../');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Email/Messages/ReminderEmail.php');
require_once(ROOT_DIR . 'lib/SMS/namespace.php');
require_once(ROOT_DIR . 'lib/SMS/Messages/StartReminderSms.php');
require_once(ROOT_DIR . 'lib/SMS/Messages/EndReminderSms.php');
require_once(ROOT_DIR . 'Jobs/JobCop.php');
require_once(ROOT_DIR . 'Jobs/JobServer.php');
require_once(ROOT_DIR . 'Jobs/BookedJob.php');

class SendRemindersJob extends BookedJob
{
    public function __construct()
    {
        parent::__construct('send-reminders', 1);
    }

    protected function Execute()
    {
        $sms = new SmsService();

        $emailEnabled = Configuration::Instance()->GetKey(ConfigKeys::ENABLE_EMAIL, new BooleanConverter());
        $smsEnabled = $sms->IsEnabled();
        $remindersEnabled = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_REMINDERS_ENABLED, new BooleanConverter());

        if (!$remindersEnabled || (!$emailEnabled && !$smsEnabled)) {
            return;
        }

        $reminderRepository = new ReminderRepository();
        $userRepository = new UserRepository();
        $now = Date::Now();

        $startNotices = $reminderRepository->GetReminderNotices($now, ReservationReminderType::Start);
        Log::Debug('Found start reminders', ['count' => count($startNotices)]);
        foreach ($startNotices as $notice) {
            if ($emailEnabled) {
                ServiceLocator::GetEmailService()->Send(new ReminderStartEmail($notice));
            }

            if ($smsEnabled) {
                $user = $userRepository->LoadById($notice->OwnerId());
                if ($user->IsSmsOptedIn() && $user->WantsEventSms(new ReservationReminderEvent())) {
                    $sms->Send(new StartReminderSms($user, $notice));
                }
            }
        }

        $endNotices = $reminderRepository->GetReminderNotices(Date::Now(), ReservationReminderType::End);
        Log::Debug('Found end reminders', ['count' => count($endNotices)]);
        foreach ($endNotices as $notice) {
            if ($emailEnabled) {
                ServiceLocator::GetEmailService()->Send(new ReminderEndEmail($notice));
            }

            if ($smsEnabled) {
                $user = $userRepository->LoadById($notice->OwnerId());
                if ($user->IsSmsOptedIn() && $user->WantsEventSms(new ReservationReminderEvent())) {
                    $sms->Send(new EndReminderSms($user, $notice));
                }
            }
        }
    }
}
$sendRemindersJob = new SendRemindersJob();
$sendRemindersJob->Run();