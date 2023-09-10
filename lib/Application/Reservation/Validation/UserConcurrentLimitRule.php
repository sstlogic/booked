<?php

/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Access/UserRepository.php');

class UserConcurrentLimitRule implements IReservationValidationRule
{
    /**
     * @var IReservationViewRepository
     */
    private $reservationRepository;

    /**
     * @param IReservationViewRepository $reservationRepository
     */
    public function __construct(IReservationViewRepository $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
    }

    public function Validate($reservationSeries, $retryParameters)
    {
        $limit = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_TOTAL_USER_CONCURRENT_LIMIT, new IntConverter());
        if (empty($limit) || $limit < 1) {
            return new ReservationRuleResult();
        }

        $bufferTime = $reservationSeries->MaxBufferTime();

        /** @var Date $conflicts */
        $violationDates = [];
        foreach ($reservationSeries->Instances() as $reservation) {
            if (!$reservationSeries->IsMarkedForDelete($reservation->ReservationId())
                && $this->InstanceViolatesLimit($reservation, $bufferTime, $reservationSeries->UserId())) {
                $violationDates[] = $reservation->StartDate();
            }
        }

        /** @var Date[] $uniqueDates */
        $uniqueDates = array_unique($violationDates);
        sort($uniqueDates);

        $errorString = new StringBuilder();

        $errorString->Append(Resources::GetInstance()->GetString('UserConcurrentLimitError', array($limit)));
        $errorString->Append("\n");
        $format = Resources::GetInstance()->GetDateFormat(ResourceKeys::DATE_GENERAL);

        foreach ($uniqueDates as $date)
        {
            $errorString->Append($date->Format($format));
            $errorString->Append("\n");
        }

        return new ReservationRuleResult(empty($violationDates), $errorString->ToString());
    }

    /**
     * @param Reservation $reservation
     * @param TimeInterval|null $bufferTime
     * @param int[] $resourceIds
     * @param int $userId
     * @return bool
     */
    private function InstanceViolatesLimit(Reservation $reservation, $bufferTime, $userId)
    {
        $startDate = $reservation->StartDate();
        $endDate = $reservation->EndDate();

        if ($bufferTime != null) {
            $startDate = $startDate->SubtractInterval($bufferTime);
            $endDate = $endDate->AddInterval($bufferTime);
        }

        $existingReservations = $this->reservationRepository->GetReservations($startDate, $endDate, $userId, ReservationUserLevel::OWNER);
        if (empty($existingReservations)) {
            return false;
        }

        /** @var ReservationItemView[] $instanceConflicts */
        $instanceConflicts = [];
        foreach ($existingReservations as $existing) {
            if ($existing->ReferenceNumber == $reservation->ReferenceNumber()) {
                continue;
            }

            if ($existing->BufferedTimes()->Overlaps($reservation->Duration())) {
                $instanceConflicts[] = $existing;
            }
        }

        $conflicts = 0;
        $conflictsReference = array();
        foreach($instanceConflicts as $c1) {
            $conflictsReference[$c1->GetReferenceNumber()] = [$c1->GetReferenceNumber()];
            foreach ($instanceConflicts as $c2) {
                if ($c1->GetReferenceNumber() == $c2->GetReferenceNumber()) {
                    continue;
                }
                if ($c1->BufferedTimes()->Overlaps($c2->BufferedTimes()) && !in_array($c2->GetReferenceNumber(), $conflictsReference[$c1->GetReferenceNumber()]))
                {
                    $conflictsReference[$c1->GetReferenceNumber()][] = $c2->GetReferenceNumber();
                }
            }
        }

        foreach ($conflictsReference as $ref => $conflictList) {
            $maxConflicts = 0;
            foreach ($conflictList as $otherRef) {
                $maxConflicts = count(array_intersect($conflictsReference[$ref], $conflictsReference[$otherRef]));
            }

            if ($maxConflicts > $conflicts) {
                $conflicts = $maxConflicts;
            }
        }

        $limit = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_TOTAL_USER_CONCURRENT_LIMIT, new IntConverter());

//        if ($conflicts <= 1)
//        {
//            return count($instanceConflicts) > $limit;
//        }

        return $conflicts >= $limit;
    }
}
