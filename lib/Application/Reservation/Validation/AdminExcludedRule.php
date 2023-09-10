<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

class AdminExcludedRule implements IReservationValidationRule
{
    /**
     * @var IReservationValidationRule
     */
    private $rule;

    /**
     * @var IAdminForReservationCheck
     */
    private $adminCheck;

    public function __construct(IReservationValidationRule $baseRule, UserSession $session, IUserRepository $userRepository)
    {
        $this->rule = $baseRule;
        $this->adminCheck = new AdminForReservationCheck($session, $userRepository);
    }

    public function Validate($reservationSeries, $retryParameters)
    {
        if ($this->adminCheck->Check($reservationSeries)) {
            Log::Debug('Skipping reservation validation');

            return new ReservationRuleResult(true);
        }

        return $this->rule->Validate($reservationSeries, $retryParameters);
    }
}