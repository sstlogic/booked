<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Application/Authentication/namespace.php');

class PostRegistration implements IPostRegistration
{
	/**
	 * @var IWebAuthentication
	 */
	protected $authentication;

	/**
	 * @var IAccountActivation
	 */
	protected $activation;

	public function __construct(IWebAuthentication $authentication, IAccountActivation $activation)
	{
		$this->authentication = $authentication;
		$this->activation = $activation;
	}

	public function HandleSelfRegistration(User $user, IRegistrationPage $page, ILoginContext $loginContext)
	{
		if ($user->StatusId() == AccountStatus::ACTIVE)
		{
			Log::Debug('PostRegistration - Handling activate user', ['email' => $user->EmailAddress()]);
			$this->authentication->Login($user->EmailAddress(), $loginContext);
			$page->Redirect(Pages::UrlFromId($user->Homepage()));
		}
		else
		{
			Log::Debug('PostRegistration - Handling pending user', ['email' => $user->EmailAddress()]);
			$this->authentication->HandlePending($user, $loginContext);
			$this->activation->Notify($user);
			$page->Redirect(Pages::ACTIVATION);
		}
	}
}