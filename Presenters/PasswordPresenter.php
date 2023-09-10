<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'lib/Config/namespace.php');
require_once(ROOT_DIR . 'lib/Common/namespace.php');
require_once(ROOT_DIR . 'lib/Common/Validators/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Authentication/namespace.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Email/Messages/PasswordUpdatedByUserEmail.php');

class PasswordPresenter
{
	/**
	 * @var \IPasswordPage
	 */
	private $page;

	/**
	 * @var \IUserRepository
	 */
	private $userRepository;

    /**
     * @var \IPassword
     */
    private $password;

    public function __construct(IPasswordPage $page, IUserRepository $userRepository, IPassword $password)
    {
        $this->page = $page;
        $this->userRepository = $userRepository;
        $this->password = $password;
    }

	public function PageLoad()
	{
		$this->page->SetAllowedActions(PluginManager::Instance()->LoadAuthentication());

		if ($this->page->ResettingPassword())
		{
			$this->LoadValidators();

			if ($this->page->IsValid())
			{
				$this->page->EnforceCSRFCheck();
				$user = $this->GetUser();
                $password = $this->page->GetPassword();
                $encrypted = $this->password->Encrypt($password);

                $user->ChangePassword($encrypted);
				$this->userRepository->Update($user);

                $session = ServiceLocator::GetServer()->GetUserSession();
                $session->ForcePasswordReset = $user->MustChangePassword();
                ServiceLocator::GetServer()->SetUserSession($session);

                ServiceLocator::GetEmailService()->Send(new PasswordUpdatedByUserEmail($user));

				$this->page->ShowResetPasswordSuccess(true);
                Log::Debug('User updated their own password.', ['userId' => $user->Id()]);
			}
            else {
                $this->page->ShowError();
            }
		}
	}

	private function LoadValidators()
	{
		$this->page->RegisterValidator('currentpassword', new PasswordValidator($this->page->GetCurrentPassword(), $this->GetUser()));
		$this->page->RegisterValidator('passwordmatch', new EqualValidator($this->page->GetPassword(), $this->page->GetPasswordConfirmation()));
		$this->page->RegisterValidator('passwordcomplexity', new PasswordComplexityValidator($this->page->GetPassword()));
        $this->page->RegisterValidator('passwordold', new PasswordNewSameAsOldValidator($this->page->GetCurrentPassword(), $this->page->GetPassword()));
	}

	/**
	 * @return User
	 */
	private function GetUser()
	{
		$userId = ServiceLocator::GetServer()->GetUserSession()->UserId;

		return $this->userRepository->LoadById($userId);
	}
}