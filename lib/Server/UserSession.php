<?php

/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

class UserSession
{
	public $UserId = '';
	public $FirstName = '';
	public $LastName = '';
	public $Email = '';
	public $Timezone = '';
	public $HomepageId = 1;
	public $IsAdmin = false;
	public $IsGroupAdmin = false;
	public $IsResourceAdmin = false;
	public $IsScheduleAdmin = false;
	public $LanguageCode = '';
	public $PublicId = '';
	public $LoginTime = '';
	public $ScheduleId = '';
	public $Groups = array();
	public $AdminGroups = array();
	public $CSRFToken = '';
	public $ApiOnly = false;
    public $ForcePasswordReset = false;
    public $IsAwaitingMultiFactorAuth = false;
    public $LoginToken = null;
    public $RememberMeToken = null;
    public $IsFirstLogin = false;
    public $DateFormat = null;
    public $TimeFormat = null;

    public function __construct($id)
	{
		$this->UserId = $id;
	}

	public function IsLoggedIn()
	{
		return true;
	}

	public function IsGuest()
	{
		return false;
	}

	public function IsAdminForGroup($groupIds = array())
	{
		if (!is_array($groupIds))
		{
			$groupIds = array($groupIds);
		}

		if ($this->IsAdmin)
		{
			return true;
		}

		if (!$this->IsGroupAdmin)
		{
			return false;
		}

		foreach ($groupIds as $groupId)
		{
			if (in_array($groupId, $this->AdminGroups))
			{
				return true;
			}
		}

		return false;
	}

	public function __toString()
	{
		return "{$this->FirstName} {$this->LastName} ({$this->Email})";
	}

    public function FullName()
    {
        return new FullName($this->FirstName, $this->LastName);
    }
}

class NullUserSession extends UserSession
{
	/**
	 * @var false
	 */
	private $isLoggedIn;
	/**
	 * @var bool
	 */
	private $isGuest;

	public function __construct($isLoggedIn = false, $isGuest = true)
	{
		parent::__construct(0);
		$this->Timezone = Configuration::Instance()->GetDefaultTimezone();
		$this->isLoggedIn = $isLoggedIn;
		$this->isGuest = $isGuest;
	}

	public function IsLoggedIn()
	{
		return $this->isLoggedIn;
	}

	public function IsGuest()
	{
		return $this->isGuest;
	}
}