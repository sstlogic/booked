<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class CreditApplicability
{
	const SLOT = 1;
	const RESERVATION = 2;

	/**
	 * @param $applicability int|mixed
	 */
	public static function Create($applicability)
	{
		if ($applicability == CreditApplicability::RESERVATION) {
			return CreditApplicability::RESERVATION;
		}
		
		return CreditApplicability::SLOT;
	}
}
