<?php

/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

class ReservationCanBeCheckedOutRule implements IReservationValidationRule
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

        $tooEarly = Date::Now()->LessThan($reservationSeries->CurrentInstance()->StartDate());

        foreach ($reservationSeries->AllResources() as $resource) {
            if ($resource->IsCheckInEnabled()) {
                $atLeastOneResourceRequiresCheckIn = true;
            }

            if ($resource->GetCheckinLimitedToAdmins()) {
                $atLeastOneResourceRequiresAdmin = true;
            }

            if ($tooEarly || !$reservationSeries->CurrentInstance()->IsCheckedIn()) {
                $isOk = false;
                break;
            }
        }

        if ($atLeastOneResourceRequiresAdmin && !$this->adminCheck->Check($reservationSeries)) {
            $isOk = false;
        }

        return new ReservationRuleResult($isOk && $atLeastOneResourceRequiresCheckIn, Resources::GetInstance()->GetString('ReservationCannotBeCheckedOutFrom'));
    }
}