<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

class ReservationValidationRuleProcessor implements IReservationValidationService
{
	/**
	 * @var array|IReservationValidationRule[]
	 */
	private $_validationRules = array();

	public function __construct($validationRules)
	{
		$this->_validationRules = $validationRules;
	}

	public function Validate($reservationSeries, $retryParameters = null)
	{
		/** @var $rule IReservationValidationRule */
		foreach ($this->_validationRules as $rule)
		{
			$result = $rule->Validate($reservationSeries, $retryParameters);
			Log::Debug('Validating rule', ['rule' => get_class($rule), 'isValid' => $result->IsValid()]);

			if (!$result->IsValid())
			{
				return new ReservationValidationResult(false, array($result->ErrorMessage()), array(), $result->CanBeRetried(), $result->RetryParameters(), array($result->RetryMessage()), $result->CanJoinWaitlist());
			}
		}

		return new ReservationValidationResult();
	}

	public function AddRule($validationRule)
	{
		$this->_validationRules[] = $validationRule;
	}

 	public function PushRule($validationRule)
	{
		array_unshift($this->_validationRules, $validationRule);
	}
}