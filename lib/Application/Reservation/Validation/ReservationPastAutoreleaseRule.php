<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ReservationPastAutoreleaseRule implements IReservationValidationRule
{
    /**
     * @param ExistingReservationSeries $reservationSeries
     * @param $retryParameters
     * @return ReservationRuleResult
     * @throws Exception
     */
    public function Validate($reservationSeries, $retryParameters)
    {
        $pastCheckinTime = false;
        foreach ($reservationSeries->AllResources() as $resource) {
            if ($resource->IsCheckInEnabled() && $resource->IsAutoReleased()) {
                $pastCheckinTime = $this->PastCheckinTime($resource, $reservationSeries);
            }

            if ($pastCheckinTime) {
                break;
            }
        }

        return new ReservationRuleResult(!$pastCheckinTime, Resources::GetInstance()->GetString('PastAutoreleaseError'));
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
