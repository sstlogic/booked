<?php
/**
Copyright 2014-2023 Twinkle Toes Software, LLC
*/

class CurrentUserIsReservationUserRule implements IReservationValidationRule
{
	/**
	 * @var UserSession
	 */
	private $userSession;

	public function __construct(UserSession $userSession)
	{
		$this->userSession = $userSession;
	}

	public function Validate($reservationSeries, $retryParameters)
	{
        $isOwner = $this->userSession->UserId == $reservationSeries->UserId();
        $isCoOwner = in_array($this->userSession->UserId, $reservationSeries->CurrentInstance()->CoOwners());
		return new ReservationRuleResult($isOwner || $isCoOwner, Resources::GetInstance()->GetString('NoReservationAccess'));
	}
}