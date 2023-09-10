<?php
/**
Copyright 2013-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'WebServices/Requests/User/UserRequestBase.php');

class CreateUserRequest extends UserRequestBase
{
	public $password;

	public static function Example()
	{
		$request = new CreateUserRequest();
		$request->firstName = 'first';
		$request->lastName = 'last';
		$request->emailAddress = 'email@address.com';
		$request->userName = 'username';
		$request->timezone = 'America/Chicago';
		$request->language = 'en_us';
		$request->password = 'plain text password';
		$request->phone = '123-456-7980';
        $request->phoneCountryCode = 'US';
		$request->organization = 'organization';
		$request->position = 'position';
		$request->customAttributes = array(new AttributeValueRequest(99, 'attribute value'));
		$request->groups = array(1,2,4);
		return $request;
	}
}