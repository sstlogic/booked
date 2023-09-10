<?php
/**
Copyright 2013-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/WebService/namespace.php');

class UserUpdatedResponse extends RestResponse
{
	public $userId;

	public function __construct(IRestServer $server, $userId)
	{
		$this->userId = $userId;
		$this->AddService($server, WebServices::GetUser, array(WebServiceParams::UserId => $userId));
		$this->AddService($server, WebServices::UpdateUser, array(WebServiceParams::UserId => $userId));
	}

	public static function Example()
	{
		return new ExampleUserUpdatedResponse();
	}
}

class ExampleUserUpdatedResponse extends UserCreatedResponse
{
	public function __construct()
	{
		$this->AddLink('http://url/to/user', WebServices::GetUser);
		$this->AddLink('http://url/to/update/user', WebServices::UpdateUser);
	}
}