<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/WebService/namespace.php');
require_once(ROOT_DIR . 'WebServices/Responses/UserItemResponse.php');

class UsersResponse extends RestResponse
{
	/**
	 * @var array|UserItemResponse[]
	 */
	public $users = array();

	/**
	 * @param IRestServer $server
	 * @param array|UserItemView[] $users
	 * @param array|string[] $attributeLabels
	 */
	public function __construct(IRestServer $server, $users, $attributeLabels)
	{
		foreach ($users as $user)
		{
			$this->users[] = new UserItemResponse($server, $user, $attributeLabels);
		}
	}

	public static function Example()
	{
		return new ExampleUsersResponse();

	}
}

class ExampleUsersResponse extends UsersResponse
{
	public function __construct()
	{
		$this->users = array(UserItemResponse::Example());
	}
}