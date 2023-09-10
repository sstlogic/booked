<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ReservationLoader
{
    /**
     * @var IReservationApiPage
     */
    private $page;
    /**
     * @var IReservationViewRepository
     */
    private $reservationViewRepository;

    public function __construct(IReservationApiPage $page, IReservationViewRepository $reservationViewRepository)
    {
        $this->page = $page;
        $this->reservationViewRepository = $reservationViewRepository;
    }

    /**
     * @throws Exception
     */
    public function LoadFromReferenceNumber(): ?ReservationView
    {
        $referenceNumber = $this->page->GetReferenceNumber();
        if (!empty($referenceNumber)) {
            $reservation = $this->reservationViewRepository->GetReservationForEditing($referenceNumber);
            if ($reservation->IsDisplayable()) {
                return $reservation;
            }
            throw new Exception("Reservation not found: $referenceNumber");
        }

        $sourceReferenceNumber = $this->page->GetSourceReferenceNumber();
        if (!empty($sourceReferenceNumber)) {
            $reservation = $this->reservationViewRepository->GetReservationForEditing($sourceReferenceNumber);
            if ($reservation->IsDisplayable()) {

                return $reservation->Duplicate();
            }
            throw new Exception("Reservation not found: $sourceReferenceNumber");
        }

        return null;
    }

    /**
     * @param Schedule $schedule
     * @param BookableResource[] $resources
     * @param UserSession $currentUser
     * @return ReservationView
     */
    public function Load(Schedule $schedule, array $resources, IReservationApiPage $page, UserSession $currentUser, IScheduleRepository $scheduleRepository): ReservationView
    {
        $selectedResources = $this->GetResourceIds($resources, $page);

        $reservation = new ReservationView();
        $reservation->OwnerId = $currentUser->UserId;
        $reservation->OwnerEmailAddress = $currentUser->Email;
        $reservation->OwnerFirstName = $currentUser->FirstName;
        $reservation->OwnerLastName = $currentUser->LastName;
        if (!empty($selectedResources)) {
            $reservation->ResourceId = array_shift($selectedResources);
            $reservation->AdditionalResourceIds = $selectedResources;
        }
        $reservation->ScheduleId = $schedule->GetId();
        $reservation->StartDate = $this->GetStartDate($page, $currentUser->Timezone);
        $reservation->EndDate = $this->GetEndDate($page, $currentUser->Timezone);
        $reservation->RepeatTerminationDate = NullDate::Instance();
        $reservation->RepeatType = RepeatType::None;
        $allowSelfJoin = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_DEFAULT_ALLOW_PARTICIPATION_JOIN, new BooleanConverter());
        $allowParticipation = !Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_PREVENT_PARTICIPATION, new BooleanConverter());
        $reservation->AllowParticipation = $allowSelfJoin && $allowParticipation;

        if ($schedule->HasCustomLayout()) {
            $periodFound = false;
            $periods = $scheduleRepository->GetCustomLayoutPeriods($reservation->StartDate->GetDate(), $schedule->GetId());
            foreach ($periods as $p) {
                if ($p->BeginDate()->Equals($reservation->StartDate) && $p->EndDate()->Equals($reservation->EndDate)) {
                    $periodFound = true;
                    break;
                }
            }

//            if (!$periodFound) {
//                Log::Error("No appointment found. Requested: %s", $reservation->StartDate->ToString());
//                throw new Exception("No appointment found");
//            }

        }
        return $reservation;
    }

    private function GetStartDate(IReservationApiPage $page, $timezone)
    {
        $reservationDate = $page->GetReservationDate();
        $startDate = $page->GetStartDate();
        $endDate = $page->GetEndDate();

        try {
            if (!empty($startDate)) {
                return Date::Parse($startDate, $timezone);
            }

            if (!empty($reservationDate)) {
                return Date::Parse($reservationDate, $timezone);
            }

            if (!empty($endDate)) {
                return Date::Parse($endDate, $timezone);
            }

        } catch (Exception $ex) {
            Log::Debug("Could not parse requested reservation start date.", ['reservationDate' => $reservationDate, 'startDate' => $startDate]);
        }
        return NullDate::Instance();
    }

    private function GetEndDate(IReservationApiPage $page, $timezone)
    {
        $reservationDate = $page->GetReservationDate();
        $endDate = $page->GetEndDate();
        $startDate = $page->GetStartDate();

        try {
            if (!empty($endDate)) {
                return Date::Parse($endDate, $timezone);
            }

            if (!empty($reservationDate)) {
                return Date::Parse($reservationDate, $timezone);
            }

            if (!empty($startDate)) {
                return Date::Parse($startDate, $timezone);
            }

        } catch (Exception $ex) {
            Log::Debug("Could not parse requested reservation end date.", ['start' => $reservationDate, 'end' => $endDate]);
        }
        return NullDate::Instance();
    }

    /**
     * @param BookableResource[] $resources
     * @param IReservationApiPage $page
     * @return int[]|null
     */
    private function GetResourceIds(array $resources, IReservationApiPage $page): ?array
    {
        $resourceIds = $page->GetResourceIds();
        $publicId = $page->GetResourcePublicId();

        if (!empty($resourceIds)) {
            $selected = [];
            foreach ($resourceIds as $id) {
                foreach ($resources as $r) {
                    if ($id == $r->GetId()) {
                        $selected[] = $id;
                        break;
                    }
                }
            }

            return $selected;
        }

        if (!empty($publicId)) {
            foreach ($resources as $r) {
                if ($publicId == $r->GetPublicId()) {
                    return [$r->GetId()];
                }
            }
        }

        return count($resources) > 0 ? [$resources[0]->GetId()] : null;
    }
}