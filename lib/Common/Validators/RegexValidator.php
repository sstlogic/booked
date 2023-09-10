<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

class RegexValidator extends ValidatorBase implements IValidator
{
    private $_value;
    private $_regex;

    public function __construct($value, $regex)
	{
		$this->_value = $value;
		$this->_regex = $regex;
	}

	public function Validate()
	{
		$this->isValid = false;
		if(preg_match($this->_regex, $this->_value))
		{
			$this->isValid = true;
		}
	}
}