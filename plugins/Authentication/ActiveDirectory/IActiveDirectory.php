<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

interface IActiveDirectory
{
	/**
	 * @return bool If connection was successful
	 */
	public function Connect();

	/**
	 * @return bool If authentication was successful
	 */
	public function Authenticate($username, $password);

	/**
	 * @return ActiveDirectoryUser The details for the user
	 */
	public function GetLdapUser($username);
}
?>