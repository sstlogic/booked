<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

interface IReservationValidationFactory
{
	/**
	 * @param ReservationAction $reservationAction
	 * @param UserSession $userSession
	 * @return IReservationValidationService
	 */
	function Create($reservationAction, $userSession);
}