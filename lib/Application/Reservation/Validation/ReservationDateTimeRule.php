<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

class ReservationDateTimeRule implements IReservationValidationRule
{
	/**
	 * @param ReservationSeries $reservationSeries
	 * @param $retryParameters
	 * @return ReservationRuleResult
	 * @throws Exception
	 */
	public function Validate($reservationSeries, $retryParameters)
	{
		$currentInstance = $reservationSeries->CurrentInstance();

		$startIsBeforeEnd = $currentInstance->StartDate()->LessThan($currentInstance->EndDate());
		return new ReservationRuleResult($startIsBeforeEnd, Resources::GetInstance()->GetString('StartDateBeforeEndDateRule'));
	}
}