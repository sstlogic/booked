<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

class AuthenticationRequest
{
	/**
	 * @var string
	 */
	public $username;
	/**
	 * @var string
	 */
	public $password;

	/**
	 * @param string $username
	 * @param string $password
	 */
	public function __construct($username = null, $password = null)
	{
		$this->username = $username;
		$this->password = $password;
	}

    public static function Example()
    {
        return new AuthenticationRequest("your-username", "your-password");
    }
}
