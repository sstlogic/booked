<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Application/Authentication/namespace.php');

interface IWebAuthentication extends IAuthenticationPromptOptions
{
	/**
	 * @param string $username
	 * @param string $password
	 * @return bool If user is valid
	 */
	public function Validate($username, $password);

	/**
	 * @param string $username
	 * @param ILoginContext $loginContext
	 * @return void
	 */
	public function Login($username, $loginContext);

	/**
	 * @param UserSession $user
	 * @return void
	 */
	public function Logout(UserSession $user);

	/**
	 * @param string $cookieValue authentication cookie value
	 * @param ILoginContext $loginContext
	 * @return bool If the login was successful
	 */
	public function CookieLogin($cookieValue, $loginContext);

	/**
	 * @param ILoginPage $loginPage
	 * @return void
	 */
	public function HandleLoginFailure(ILoginPage $loginPage);

	/**
	 * @return bool
	 */
	public function AreCredentialsKnown();

	/**
	 * @return mixed
	 */
	public function IsLoggedIn();

	/**
	 * @return string
	 */
	public function GetRegistrationUrl();

	/**
	 * @return string
	 */
	public function GetPasswordResetUrl();

    /**
     * @param User $user
     * @param ILoginContext $loginContext
     */
	public function HandlePending(User $user, ILoginContext $loginContext);

    /**
     * @return boolean
     */
    public function AllowRegistration();

    /**
     * @return boolean
     */
    public function AllowManualLogin();
}

class WebAuthentication implements IWebAuthentication
{
	private $authentication;
	private $server;

	/**
	 * @param IAuthentication $authentication
	 * @param Server $server
	 */
	public function __construct(IAuthentication $authentication, $server = null)
	{
		$this->authentication = $authentication;
		$this->server = $server;
		if ($this->server == null)
		{
			$this->server = ServiceLocator::GetServer();
		}
	}

	/**
	 * @param string $username
	 * @param string $password
	 * @return bool If user is valid
	 */
	public function Validate($username, $password)
	{
		if (empty($password) && (!$this->authentication->AreCredentialsKnown() && !$this->authentication->AllowManualLogin()))
		{
			return false;
		}

		return $this->authentication->Validate($username, $password);
	}

	/**
	 * @param string $username
	 * @param ILoginContext $loginContext
	 * @return void
	 */
	public function Login($username, $loginContext)
	{
		$userSession = $this->authentication->Login($username, $loginContext);
		if ($userSession->ApiOnly) {
            $this->server->SetUserSession(new NullUserSession());
		    return;
        }
		$this->server->SetUserSession($userSession);

		if ($loginContext->GetData()->Persist)
		{
			$this->SetLoginCookie($userSession->UserId, $userSession->RememberMeToken);
		}

		if (!$userSession->IsAwaitingMultiFactorAuth) {
		   $this->server->SetCookie(new Cookie(CookieKeys::LOGIN_TOKEN, $userSession->LoginToken, null, null, true));
        }
	}

    public function HandlePending(User $user, ILoginContext $loginContext) {
        $this->server->SetCookie(new Cookie(CookieKeys::LOGIN_TOKEN, $user->LoginToken(), null, null, true));
    }

	/**
	 * @param UserSession $userSession
	 * @return void
	 */
	public function Logout(UserSession $userSession)
	{
		$this->authentication->Logout($userSession);
		Log::Debug('Logout', ['userId' => $userSession->UserId]);

		$this->DeleteLoginCookie($userSession->UserId);
		ServiceLocator::GetServer()->EndSession(SessionKeys::USER_SESSION);
	}

	public function CookieLogin($cookieValue, $loginContext)
	{
		$loginCookie = LoginCookie::FromValue($cookieValue);
		$valid = false;
		$this->server->SetUserSession(new NullUserSession());

		if (!is_null($loginCookie))
		{
			$validEmail = $this->ValidateCookie($loginCookie);
			$valid = !is_null($validEmail);

			if ($valid)
			{
			    $loginContext->GetData()->Persist = true;
				$this->Login($validEmail, $loginContext);
			}
		}

		Log::Debug('Cookie login', ['isValid' => $valid]);

		return $valid;
	}

	/**
     * @param int $userId
	 * @param string $token
	 */
	private function SetLoginCookie($userId, $token)
	{
		$cookie = new LoginCookie($userId, $token);
		$this->server->SetCookie($cookie);
	}

	private function DeleteLoginCookie($userId)
	{
        ServiceLocator::GetServer()->DeleteCookie(new LoginCookie($userId, null));
	}

    /**
     * @param LoginCookie $loginCookie
     * @return string|null
     */
	private function ValidateCookie($loginCookie)
	{
		$valid = false;
		$reader = ServiceLocator::GetDatabase()->Query(new CookieLoginCommand($loginCookie->UserID));

		if ($row = $reader->GetRow())
		{
			$valid = $row[ColumnNames::REMEMBER_ME_TOKEN] == $loginCookie->Token;
		}

		return $valid ? $row[ColumnNames::EMAIL] : null;
	}

	public function HandleLoginFailure(ILoginPage $loginPage)
	{
		$this->authentication->HandleLoginFailure(new WebAuthenticationPage($loginPage));
	}

	public function AreCredentialsKnown()
	{
		return $this->authentication->AreCredentialsKnown();
	}

	public function ShowUsernamePrompt()
	{
		return $this->authentication->ShowUsernamePrompt();
	}

	public function ShowPasswordPrompt()
	{
		return $this->authentication->ShowPasswordPrompt();
	}

	public function ShowPersistLoginPrompt()
	{
		return $this->authentication->ShowPersistLoginPrompt();
	}

	public function ShowForgotPasswordPrompt()
	{
		return $this->authentication->ShowForgotPasswordPrompt();
	}

	public function IsLoggedIn()
	{
		return $this->server->GetUserSession()->IsLoggedIn();
	}

	public function GetRegistrationUrl()
	{
		$url = '';
		if (method_exists($this->authentication, 'GetRegistrationUrl'))
		{
			$url = $this->authentication->GetRegistrationUrl();
		}

		return $url;
	}

	public function GetPasswordResetUrl()
	{
		$url = '';
		if (method_exists($this->authentication, 'GetPasswordResetUrl'))
		{
			$url = $this->authentication->GetPasswordResetUrl();
		}

		return $url;
	}

    public function AllowRegistration()
    {
        return $this->authentication->AllowRegistration();
    }

    public function AllowManualLogin()
    {
        return $this->authentication->AllowManualLogin();
    }
}

class WebAuthenticationPage implements IAuthenticationPage
{
    /**
     * @var ILoginPage
     */
    private $page;

    public function __construct(ILoginPage $page)
	{
		$this->page = $page;
	}

	/**
	 * @return string
	 */
	public function GetEmailAddress()
	{
		return $this->page->GetEmailAddress();
	}

	/**
	 * @return string
	 */
	public function GetPassword()
	{
		return $this->page->GetPassword();
	}

	/**
	 * @return void
	 */
	public function SetShowLoginError()
	{
		$this->page->SetShowLoginError();
	}
}