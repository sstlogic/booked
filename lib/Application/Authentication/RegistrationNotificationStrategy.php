<?php

/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

class RegistrationNotificationStrategy implements IRegistrationNotificationStrategy
{
	public function NotifyAccountCreated(User $user, $password)
	{
		if (Configuration::Instance()->GetKey(ConfigKeys::REGISTRATION_NOTIFY, new BooleanConverter()))
		{
			ServiceLocator::GetEmailService()->Send(new AccountCreationEmail($user,
																			 ServiceLocator::GetServer()->GetUserSession()));
		}
	}
}
