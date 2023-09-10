<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'lib/Email/namespace.php');
require_once(ROOT_DIR . 'Domain/namespace.php');
require_once(ROOT_DIR . 'Domain/Values/PasswordResetRequest.php');

class ForgotPasswordEmail extends EmailMessage
{
	/**
	 * @var \User
	 */
	private $user;

	/**
	 * @var string
	 */
	private $token;

	public function __construct(User $user, $token)
	{
		parent::__construct($user->Language());

		$this->user = $user;
		$this->token = $token;
	}

	function To()
	{
		return new EmailAddress($this->user->EmailAddress(), $this->user->FullName());
	}

	function Subject()
	{
		return $this->Translate('ResetPasswordRequest');
	}

	function Body()
	{
        $url = (new Url(Configuration::Instance()->GetScriptUrl()))->Add(Pages::RESET_PASSWORD)->AddQueryString(QueryStringKeys::RESET_TOKEN, $this->token);
		$this->Set('ResetUrl', $url->ToString());
		$this->Set('Minutes', PasswordResetRequest::EXPIRATION_MINUTES);
		return $this->FetchTemplate('ResetPasswordRequest.tpl');
	}
}