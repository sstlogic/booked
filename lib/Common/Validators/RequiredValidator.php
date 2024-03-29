<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

class RequiredValidator extends ValidatorBase implements IValidator
{
	private $value;

	public function __construct($value)
	{
		$this->value = $value;
	}

	public function Validate()
	{
		$trimmed = trim($this->value . '');
		$this->isValid = !empty($trimmed);
	}
}