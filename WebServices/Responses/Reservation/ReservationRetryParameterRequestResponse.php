<?php

/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/WebService/namespace.php');

class ReservationRetryParameterRequestResponse
{
	public $name;
	public $value;

	public function __construct($name, $value)
	{
		$this->name = $name;
		$this->value = $value;
	}

	public static function Example()
	{
		return new ReservationRetryParameterRequestResponse('name', 'value');
	}
}
