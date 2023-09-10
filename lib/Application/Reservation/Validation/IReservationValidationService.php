<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

interface IReservationValidationService
{
	/**
	 * @param ReservationSeries|ExistingReservationSeries $series
	 * @param ReservationRetryParameter[]|null $retryParameters
	 * @return IReservationValidationResult
	 */
	public function Validate($series, $retryParameters = null);
}