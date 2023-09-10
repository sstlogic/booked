<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Controls/Dashboard/UpcomingReservations.php');

class UpcomingReservationsPresenter
{
    /**
     * @var IUpcomingReservationsControl
     */
    private $control;

    /**
     * @var IReservationViewRepository
     */
    private $repository;

    /**
     * @var int
     */
    private $searchUserId = ReservationViewRepository::ALL_USERS;

    /**
     * @var int
     */
    private $searchUserLevel = ReservationUserLevel::ALL;
    /**
     * @var IReservationWaitlistRepository
     */
    private $waitlistRepository;

    public function __construct(IUpcomingReservationsControl $control, IReservationViewRepository $repository, IReservationWaitlistRepository $waitlistRepository)
    {
        $this->control = $control;
        $this->repository = $repository;
        $this->waitlistRepository = $waitlistRepository;
    }

    public function SetSearchCriteria($userId, $userLevel)
    {
        $this->searchUserId = $userId;
        $this->searchUserLevel = $userLevel;
    }

    public function PageLoad()
    {
        $user = ServiceLocator::GetServer()->GetUserSession();
        $timezone = $user->Timezone;

        $now = Date::Now();
        $today = $now->ToTimezone($timezone)->GetDate();
        $dayOfWeek = $today->Weekday();

        $weekoffset = Configuration::Instance()->GetKey(ConfigKeys::FIRST_DAY_OF_WEEK, new IntConverter());

        $lastDate = $now->AddDays(13 - $dayOfWeek - 1 + $weekoffset);
        $consolidated = $this->repository->GetReservations($now, $lastDate, $this->searchUserId, $this->searchUserLevel, null, null, true);
        $tomorrow = $today->AddDays(1);

        $startOfNextWeek = $today->AddDays(7 - $dayOfWeek + $weekoffset);

        $todays = [];
        $tomorrows = [];
        $thisWeeks = [];
        $nextWeeks = [];
        $waitlists = [];

        if (Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_ALLOW_WAITLIST, new BooleanConverter())) {
            $waitlistResourceIds = [];
            /* @var $reservation ReservationItemView */
            foreach ($consolidated as $reservation) {
                $waitlistResourceIds = array_merge($waitlistResourceIds, $reservation->ResourceIds);
            }
            $waitlists = $this->waitlistRepository->FindWaitlistRequests(array_unique($waitlistResourceIds), $now, $lastDate);
        }

        /* @var $reservation ReservationItemView */
        foreach ($consolidated as $reservation) {
            $reservation->WaitlistCount = 0;
            foreach ($waitlists as $waitlist) {
                if ($waitlist->Duration()->Overlaps($reservation->DateRange()) && in_array($waitlist->ResourceId(), $reservation->ResourceIds)) {
                    $reservation->WaitlistCount++;
                }
            }
            $start = $reservation->StartDate->ToTimezone($timezone);

            if ($start->DateEquals($today)) {
                $todays[] = $reservation;
            } else if ($start->DateEquals($tomorrow)) {
                $tomorrows[] = $reservation;
            } else if ($start->LessThan($startOfNextWeek)) {
                $thisWeeks[] = $reservation;
            } else {
                $nextWeeks[] = $reservation;
            }
        }

        $this->control->SetTotal(count($consolidated));
        $this->control->SetTimezone($timezone);
        $this->control->SetUserId($user->UserId);

        $this->control->BindToday($todays);
        $this->control->BindTomorrow($tomorrows);
        $this->control->BindThisWeek($thisWeeks);
        $this->control->BindNextWeek($nextWeeks);
    }
}