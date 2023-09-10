<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

interface IAccountActivation
{
	/**
	 * @abstract
	 * @param User $user
	 * @return void
	 */
	public function Notify(User $user);

	/**
	 * @abstract
	 * @param string $activationCode
	 * @return ActivationResult
	 */
	public function Activate($activationCode);
}