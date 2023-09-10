<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

interface IReservationNotification
{
	/**
	 * @param ReservationSeries $reservationSeries
	 */
	public function Notify($reservationSeries);
}