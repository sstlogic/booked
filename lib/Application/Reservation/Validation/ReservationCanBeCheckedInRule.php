<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

class ReservationCanBeCheckedInRule implements IReservationValidationRule
{
    /**
     * @var IAdminForReservationCheck
     */
    private $adminCheck;

    public function __construct(IAdminForReservationCheck $adminCheck)
    {
        $this->adminCheck = $adminCheck;
    }

    /**
     * @param ExistingReservationSeries $reservationSeries
     * @param null|ReservationRetryParameter[] $retryParameters
     * @return ReservationRuleResult
     */
    public function Validate($reservationSeries, $retryParameters)
    {
        $isOk = true;
        $atLeastOneResourceRequiresCheckIn = false;
        $atLeastOneResourceRequiresAdmin = false;
        $checkinMinutes = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_CHECKIN_MINUTES, new IntConverter());

        $reservation = $reservationSeries->CurrentInstance();
        $tooEarly = Date::Now()->LessThan($reservation->StartDate()->AddMinutes(-$checkinMinutes));
        $tooLate = Date::Now()->GreaterThanOrEqual($reservation->EndDate());

        foreach ($reservationSeries->AllResources() as $resource) {
            if ($resource->IsCheckInEnabled()) {
                $atLeastOneResourceRequiresCheckIn = true;
            }

            if ($resource->GetCheckinLimitedToAdmins()) {
                $atLeastOneResourceRequiresAdmin = true;
            }

            $pastCheckinTime = $this->PastCheckinTime($resource, $reservationSeries);
            if ($pastCheckinTime || $tooEarly || $tooLate) {
                Log::Debug('Reservation cannot be checked in to.', ['referenceNumber' => $reservation->ReferenceNumber(), 'pastCheckinTime' => $pastCheckinTime, 'tooEarly' => $tooEarly, 'tooLate' => $tooLate]);
                $isOk = false;
                break;
            }
        }

        if ($atLeastOneResourceRequiresAdmin && !$this->adminCheck->Check($reservationSeries)) {
            $isOk = false;
        }

        return new ReservationRuleResult($isOk && $atLeastOneResourceRequiresCheckIn, Resources::GetInstance()->GetString('ReservationCannotBeCheckedInTo'));
    }

    private function PastCheckinTime(BookableResource $resource, ExistingReservationSeries $reservationSeries)
    {
        if (!$resource->IsAutoReleased() || !$resource->IsCheckInEnabled()) {
            return false;
        }

        $latestCheckin = $reservationSeries->CurrentInstance()->StartDate()->AddMinutes($resource->GetAutoReleaseMinutes());

        return Date::Now()->GreaterThan($latestCheckin);
    }
}