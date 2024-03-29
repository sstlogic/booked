<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Email/namespace.php');

class AccountDeletedEmail extends EmailMessage
{
	/**
	 * @var User
	 */
	private $deletedUser;
	/**
	 * @var UserDto
	 */
	private $to;
	/**
	 * @var UserSession
	 */
	private $userSession;

	public function __construct(User $deletedUser, UserDto $to, UserSession $userSession)
	{
		parent::__construct($to->LanguageCode);

		$this->deletedUser = $deletedUser;
		$this->to = $to;
		$this->userSession = $userSession;
	}

	/**
	 * @return array|EmailAddress[]|EmailAddress
	 */
	function To()
	{
		return new EmailAddress($this->to->EmailAddress(), $this->to->FullName());
	}

	/**
	 * @return string
	 */
	function Subject()
	{
		return $this->Translate('UserDeleted', array($this->deletedUser->FullName(), new FullName($this->userSession->FirstName, $this->userSession->LastName)));
	}

	/**
	 * @return string
	 */
	function Body()
	{
		$this->Set('UserFullName', $this->deletedUser->FullName());
		$this->Set('AdminFullName', new FullName($this->userSession->FirstName, $this->userSession->LastName));
		return $this->FetchTemplate('AccountDeleted.tpl');
	}
}