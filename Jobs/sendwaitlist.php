<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

@define('ROOT_DIR', dirname(__FILE__) . '/../');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'Jobs/JobCop.php');
require_once(ROOT_DIR . 'Jobs/JobServer.php');
require_once(ROOT_DIR . 'Jobs/BookedJob.php');
require_once(ROOT_DIR . 'lib/Email/Messages/ReservationAvailableEmail.php');

class SendWaitlistJob extends BookedJob
{
    public function __construct()
    {
        parent::__construct('send-waitlist', 1);
    }

    protected function Execute()
    {
        $emailEnabled = Configuration::Instance()->GetKey(ConfigKeys::ENABLE_EMAIL, new BooleanConverter());
        $waitlistEnabled = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_ALLOW_WAITLIST, new BooleanConverter());
        if (!$emailEnabled || !$waitlistEnabled) {
            return;
        }

        $deleteOldWaitlist = new AdHocCommand('DELETE FROM `reservation_waitlist_requests` WHERE `end_date` < @now');
        $deleteOldWaitlist->AddParameter(new \Parameter("@now", Date::Now()->ToDatabase()));
        ServiceLocator::GetDatabase()->Execute($deleteOldWaitlist);

        $reservationViewRepository = new ReservationViewRepository();
        $resourceRepository = new ResourceRepository();
        $waitlistRepository = new ReservationWaitlistRepository();
        $userRepository = new UserRepository();

        $waitlistRequests = $waitlistRepository->GetAll();

        /** @var ReservationWaitlistRequest $r */
        foreach ($waitlistRequests as $r) {
            $reservations = $reservationViewRepository->GetReservations($r->StartDate(), $r->EndDate(), null, null, null, $r->ResourceId());

            $conflicts = false;

            /** @var ReservationItemView $reservation */
            foreach ($reservations as $reservation) {
                if ($reservation->BufferedTimes()->Overlaps($r->Duration())) {
                    $conflicts = true;
                    break;
                }
            }

            if (!$conflicts || $r->StartDate()->LessThanOrEqual(Date::Now())) {
                $user = $userRepository->LoadById($r->UserId());
                $resource = $resourceRepository->LoadById($r->ResourceId());
                $email = new ReservationAvailableEmail($user, $resource, $r);
                ServiceLocator::GetEmailService()->Send($email);
                $waitlistRepository->Delete($r);
            }
        }
    }
}

$sendWaitlistJob = new SendWaitlistJob();
$sendWaitlistJob->Run();