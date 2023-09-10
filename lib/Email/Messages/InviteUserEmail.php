<?php

/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Email/namespace.php');

class InviteUserEmail extends EmailMessage
{
	/**
	 * @var string
	 */
	private $emailAddress;
	/**
	 * @var UserSession
	 */
	private $currentUser;
	/**
	 * @var FullName
	 */
	private $fullName;

	public function __construct($emailAddress, UserSession $currentUser)
	{
		$this->emailAddress = $emailAddress;
		$this->currentUser = $currentUser;
		$this->fullName = new FullName($this->currentUser->FirstName, $this->currentUser->LastName);
		parent::__construct();
	}

	/**
	 * @return array|EmailAddress[]|EmailAddress
	 */
	function To()
	{
		return new EmailAddress($this->emailAddress);
	}

	/**
	 * @return string
	 */
	function Subject()
	{
		return $this->Translate('InviteUserSubject', array($this->fullName->__toString(), Configuration::Instance()->GetKey(ConfigKeys::APP_TITLE)));
	}

	/**
	 * @return string
	 */
	function Body()
	{
		$registerUrl = new Url(Configuration::Instance()->GetScriptUrl());
		$registerUrl->Add(Pages::REGISTRATION);

		$this->Set('FullName', $this->fullName->__toString());
		$this->Set('AppTitle', Configuration::Instance()->GetKey(ConfigKeys::APP_TITLE));
		$this->Set('RegisterUrl', $registerUrl->ToString());
		return $this->FetchTemplate('InviteUser.tpl');
	}
}
