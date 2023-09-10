<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

interface ILoginContext
{
	/**
	 * @return LoginData
	 */
	public function GetData();
}

class LoginData
{
	/**
	 * @var bool
	 */
	public $Persist;

	/**
	 * @var string
	 */
	public $Language;

    /**
     * @var string|null
     */
	public $LoginToken;

    /**
     * @var boolean
     */
	public $EnforceMfa;

	public function __construct($persist = false, $language = '', $loginToken = null, $enforceMfa = false)
	{
		$this->Persist = $persist;
		$this->Language = $language;
		$this->LoginToken = $loginToken;
		$this->EnforceMfa = $enforceMfa;
	}
}