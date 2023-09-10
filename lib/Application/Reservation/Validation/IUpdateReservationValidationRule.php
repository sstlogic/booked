<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

interface IUpdateReservationValidationRule
{
	/**
	 * @param ExistingReservationSeries $reservationSeries
	 * @return ReservationRuleResult
	 */
	function Validate($reservationSeries);
}