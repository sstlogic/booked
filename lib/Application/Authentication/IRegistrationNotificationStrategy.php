<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

interface IRegistrationNotificationStrategy
{
	public function NotifyAccountCreated(User $user, $password);
}