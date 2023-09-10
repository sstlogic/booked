<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/WebService/namespace.php');

class ReservationUserResponse extends RestResponse
{
	public $userId;
	public $firstName;
	public $lastName;
	public $emailAddress;

	public function __construct(IRestServer $server, $userId, $firstName, $lastName, $emailAddress)
	{
		$this->userId = $userId;
		$this->firstName = apidecode($firstName);
		$this->lastName = apidecode($lastName);
		$this->emailAddress = apidecode($emailAddress);
		$this->AddService($server, WebServices::GetUser, array(WebServiceParams::UserId => $userId));
	}

	public static function Masked()
	{
		return new MaskedReservationUserResponse();
	}

	public static function Example()
	{
		return new ExampleReservationUserResponse();
	}
}

class MaskedReservationUserResponse extends ReservationUserResponse
{
	public function __construct()
	{
		$this->userId = null;
		$this->firstName = 'Private';
		$this->lastName = 'Private';
		$this->emailAddress = 'Private';
	}
}

class ExampleReservationUserResponse extends ReservationUserResponse
{
	public function __construct()
	{
		$this->userId = 123;
		$this->firstName = 'first';
		$this->lastName = 'last';
		$this->emailAddress = 'email@address.com';
	}
}

