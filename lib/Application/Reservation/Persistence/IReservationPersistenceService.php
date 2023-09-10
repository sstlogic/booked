<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

interface IReservationPersistenceService
{
	/**
	 * @param ReservationSeries|ExistingReservationSeries $reservation
	 */
	function Persist($reservation);
}