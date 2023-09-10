<?php
/**
Copyright 2022-2023 Twinkle Toes Software, LLC
 */

interface ISaml
{
    	/**
	 * @return bool If connection was successful
	 */
	public function Connect();

	/**
	 * @return bool If authentication was successful
	 */
	public function Authenticate();

	/**
	 * @return SamlUser The details for the user
	 */
	public function GetSamlUser();
}
?>