<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

class FailedResponse extends RestResponse
{
	/**
	 * @var array|string[]
	 */
	public $errors;

	/**
	 * @param IRestServer $server
	 * @param array|string[] $errors
	 */
	public function __construct(IRestServer $server, $errors)
	{
		$this->message = 'There were errors processing your request';
		$this->errors = $errors;
	}
}
