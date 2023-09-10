<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */


class WebLoginContext implements ILoginContext
{
	/**
	 * @var LoginData
	 */
	private $data;

	public function __construct(LoginData $data)
	{
		$this->data = $data;
	}

	/**
	 * @return LoginData
	 */
	public function GetData()
	{
		return $this->data;
	}
}
