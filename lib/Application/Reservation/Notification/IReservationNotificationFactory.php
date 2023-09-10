<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

interface IReservationNotificationFactory
{
	/**
	 * @param ReservationAction $reservationAction
	 * @param UserSession $userSession
	 * @return IReservationNotificationService
	 */
	function Create($reservationAction, $userSession);
}