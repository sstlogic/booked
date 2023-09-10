<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

interface IReservationPersistenceFactory
{
	/**
	 * @param ReservationAction $reservationAction
	 * @return IReservationPersistenceService
	 */
	function Create($reservationAction);
}
