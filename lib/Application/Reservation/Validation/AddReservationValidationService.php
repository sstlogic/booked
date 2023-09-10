<?php

/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */
class AddReservationValidationService implements IReservationValidationService
{
	/**
	 * @var IReservationValidationService
	 */
	private $ruleProcessor;

	/**
	 * @param IReservationValidationService $ruleProcessor
	 */
	public function __construct($ruleProcessor)
	{
		$this->ruleProcessor = $ruleProcessor;
	}

	public function Validate($reservationSeries, $retryParameters = null)
	{
		return $this->ruleProcessor->Validate($reservationSeries, $retryParameters);
	}
}