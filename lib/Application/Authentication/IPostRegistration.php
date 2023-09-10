<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

interface IPostRegistration
{
	public function HandleSelfRegistration(User $user, IRegistrationPage $page, ILoginContext $loginContext);
}
