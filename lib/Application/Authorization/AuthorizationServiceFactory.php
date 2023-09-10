<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/


class AuthorizationServiceFactory
{
	/**
	 * @return IAuthorizationService
	 */
	public static function GetAuthorizationService()
	{
		return PluginManager::Instance()->LoadAuthorization();
	}
}