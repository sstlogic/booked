<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

interface IRegistrationPermissionStrategy
{
	public function AddAccount(User $user);
}