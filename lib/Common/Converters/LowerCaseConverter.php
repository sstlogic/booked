<?php

/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

class LowerCaseConverter implements IConvert
{
	public function Convert($value)
	{
		return strtolower($value . '');
	}
}