<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

interface IReservationValidationRule
{
	/**
	 * @param ReservationSeries $reservationSeries
	 * @param ReservationRetryParameter[]|null $retryParameters
	 * @return ReservationRuleResult
	 */
	public function Validate($reservationSeries, $retryParameters);
}