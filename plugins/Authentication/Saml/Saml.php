<?php
/**
Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Application/Authentication/namespace.php');
require_once(ROOT_DIR . 'plugins/Authentication/Saml/namespace.php');

/**
 * Provides simpleSAMLphp authentication/synchronization for Booked Scheduler
 * @see IAuthorization
 */
class Saml extends Authentication implements IAuthentication
{
	/**
	 * @var IAuthentication
	 */
	private $authToDecorate;

	/**
	 * @var AdSamlWrapper
	 */
	private $saml;

	/**
	 * @var SamlOptions
	 */
	private $options;

	/**
	 * @var IRegistration
	 */
	private $_registration;

	/**
	 * @var SamlUser
	 */
	private $user;

	/**
	 * @var string
	 *
	 */
	private $username;

	/**
	 * @var string
	 */
	private $password;

	public function SetRegistration($registration)
	{
		$this->_registration = $registration;
	}

	private function GetRegistration()
	{
		if ($this->_registration == null)
		{
			$this->_registration = new Registration();
		}

		return $this->_registration;
	}

	/**
	 * @param IAuthentication $authentication Authentication class to decorate
	 * @param ISaml $samlImplementation The actual SAML implementation to work against
	 * @param SamlOptions $samlOptions Options to use for SAML configuration
	 */
	public function __construct(IAuthentication $authentication, $samlImplementation = null, $samlOptions = null)
	{
		$this->authToDecorate = $authentication;

		$this->options = $samlOptions;
		if ($samlOptions == null)
		{
			$this->options = new SamlOptions();
		}

		$this->saml = $samlImplementation;
		if ($samlImplementation == null)
		{
			$this->saml = new AdSamlWrapper($this->options);
		}
	}

	public function Validate($username, $password)
	{
		$this->saml->Connect();
		$isValid = $this->saml->Authenticate();

		if ($isValid)
		{
			$this->user = $this->saml->GetSamlUser();
			$userLoaded = $this->SamlUserExists();

			if (!$userLoaded)
			{
				Log::Error('Could not load user details from SinmpleSamlPhpSSO. Check your SSO settings.', ['username' => $username]);
			}
			return $userLoaded;
		}

		return false;
	}

	public function Login($username, $loginContext)
	{
		$this->username = $username;
		if (empty($this->username))
		{
			$this->username = $this->user->GetUserName();
		}
		if ($this->SamlUserExists())
		{
			$this->Synchronize($this->username);
		}

		return $this->authToDecorate->Login($this->user->GetEmail(), $loginContext);
	}

	public function Logout(UserSession $user)
	{
        $this->authToDecorate->Logout($user);
	    if ($this->options->AutomaticLogin()) {
            $this->saml->Logout();
        }
	}

	public function AreCredentialsKnown()
	{
	    return $this->options->AutomaticLogin();
	}

	private function SamlUserExists()
	{
		return $this->user != null;
	}

	private function Synchronize($username)
	{
		$registration = $this->GetRegistration();

		$registration->Synchronize(
			new AuthenticatedUser(
				$username,
				$this->user->GetEmail(),
				$this->user->GetFirstName(),
				$this->user->GetLastName(),
				'',
				Configuration::Instance()->GetKey(ConfigKeys::LANGUAGE),
				Configuration::Instance()->GetDefaultTimezone(),
				$this->user->GetPhone(), $this->user->GetInstitution(),
				$this->user->GetTitle(),
                $this->user->GetGroups())
		);
	}

	public function ShowForgotPasswordPrompt()
	{
		return false;
	}

	public function ShowPasswordPrompt()
	{
		return false;
	}

	public function ShowPersistLoginPrompt()
	{
		return false;
	}

	public function ShowUsernamePrompt()
	{
		return false;
	}

    public function AllowUsernameChange()
    {
        return false;
    }

    public function AllowEmailAddressChange()
    {
        return false;
    }

    public function AllowPasswordChange()
    {
        return false;
    }

    public function AllowNameChange()
    {
        return false;
    }

    public function AllowPhoneChange()
    {
        return false;
    }

    public function AllowOrganizationChange()
    {
        return false;
    }

    public function AllowPositionChange()
    {
        return false;
    }

    public function AllowRegistration()
    {
        return false;
    }

    public function AllowManualLogin()
    {
        return !$this->options->AutomaticLogin();
    }
}
