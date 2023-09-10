<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 * Copyright 2012-2014 Moritz Schepp, IST Austria
 */

require_once(ROOT_DIR . 'lib/Application/Authentication/namespace.php');
require_once(ROOT_DIR . 'lib/Common/namespace.php');
require_once(ROOT_DIR . 'lib/Database/namespace.php');
require_once(ROOT_DIR . 'lib/Database/Commands/namespace.php');
require_once(ROOT_DIR . 'Domain/Values/RoleLevel.php');

class Authentication implements IAuthentication
{
    /**
     * @var IPassword
     */
    private $password = null;
    /**
     * @var IRoleService
     */
    private $roleService;
    /**
     * @var IUserRepository
     */
    private $userRepository;
    /**
     * @var IFirstRegistrationStrategy
     */
    private $firstRegistration;
    /**
     * @var IGroupRepository
     */
    private $groupRepository;
    /**
     * @var IMultiFactorAuthentication
     */
    private $mfa;

    public function __construct(IRoleService $roleService, IUserRepository $userRepository, IGroupRepository $groupRepository)
    {
        $this->roleService = $roleService;
        $this->userRepository = $userRepository;
        $this->groupRepository = $groupRepository;
    }

    public function SetPassword(IPassword $password)
    {
        $this->password = $password;
    }

    /**
     * @return IPassword
     */
    private function GetPassword()
    {
        if (is_null($this->password)) {
            $this->password = new Password();
        }

        return $this->password;
    }

    public function SetFirstRegistrationStrategy(IFirstRegistrationStrategy $migration)
    {
        $this->firstRegistration = $migration;
    }

    /**
     * @return IFirstRegistrationStrategy
     */
    private function GetFirstRegistrationStrategy()
    {
        if (is_null($this->firstRegistration)) {
            $this->firstRegistration = new SetAdminFirstRegistrationStrategy();
        }

        return $this->firstRegistration;
    }

    public function SetMultiFactorAuthentication(IMultiFactorAuthentication $mfa)
    {
        $this->mfa = $mfa;
    }

    /**
     * @return IMultiFactorAuthentication
     */
    private function GetMultiFactorAuthentication()
    {
        if (is_null($this->mfa)) {
            $this->mfa = MultiFactorAuthentication::Create();
        }

        return $this->mfa;
    }


    public function Validate($username, $passwordPlainText)
    {
        if (($this->ShowUsernamePrompt() && empty($username)) || ($this->ShowPasswordPrompt() && empty($passwordPlainText))) {
            return false;
        }

        Log::Debug('Trying to log in', ['username' => $username]);

        $command = new AuthorizationCommand($username);
        $reader = ServiceLocator::GetDatabase()->Query($command);
        $valid = false;

        if ($row = $reader->GetRow()) {
            Log::Debug('User was found', ['username' => $username]);
            $pw = $this->GetPassword();
            $valid = $pw->Validate($passwordPlainText, $row[ColumnNames::PASSWORD] . '', $row[ColumnNames::PASSWORD_HASH_VERSION], $row[ColumnNames::SALT]);

            if ($valid) {
                $pw->Migrate($row[ColumnNames::USER_ID], $passwordPlainText, $row[ColumnNames::PASSWORD_HASH_VERSION]);
            }
        }
        else {
            Log::Debug('User was not found', ['username' => $username]);
        }

        Log::Debug('User validation result',  ['username' => $username, 'isValid' => $valid]);
        return $valid;
    }

    public function Login($username, $loginContext)
    {
        Log::Debug('Logging in', ['username' => $username]);

        $user = $this->userRepository->LoadByUsername($username);
        if ($user->StatusId() == AccountStatus::ACTIVE) {
            $loginData = $loginContext->GetData();
            $loginTime = LoginTime::Now();
            $language = $user->Language();

            $lastLogin = $user->LastLogin();

            if (!empty($loginData->Language)) {
                $language = $loginData->Language;
            }

            $user->Login($loginTime, $language);
            $this->userRepository->Update($user);

            $user = $this->GetFirstRegistrationStrategy()->HandleLogin($user, $this->userRepository, $this->groupRepository);

            $session = $this->GetUserSession($user, $loginTime, $lastLogin);
            if ($loginData->EnforceMfa) {
                $requiresMfa = $this->GetMultiFactorAuthentication()->Enforce($user, $loginData->LoginToken);
                $session->IsAwaitingMultiFactorAuth = $requiresMfa;

                if (!$requiresMfa) {
                    $user->RefreshLoginToken();
                    $this->userRepository->Update($user);
                    $session->LoginToken = $user->LoginToken();
                }
            }

            return $session;
        }

        return new NullUserSession();
    }

    public function Logout(UserSession $userSession)
    {
        // hook for implementing Logout logic
    }

    public function AreCredentialsKnown()
    {
        return false;
    }

    public function HandleLoginFailure(IAuthenticationPage $loginPage)
    {
        $loginPage->SetShowLoginError();
    }

    /**
     * @param User $user
     * @param string $loginTime
     * @param string $lastLogin
     * @return UserSession
     */
    private function GetUserSession(User $user, $loginTime, $lastLogin)
    {
        $userSession = new UserSession($user->Id());
        $userSession->Email = $user->EmailAddress();
        $userSession->FirstName = $user->FirstName();
        $userSession->LastName = $user->LastName();
        $userSession->Timezone = $user->Timezone();
        $userSession->HomepageId = $user->Homepage();
        $userSession->LanguageCode = $user->Language();
        $userSession->LoginTime = $loginTime;
        $userSession->PublicId = $user->GetPublicId();
        $userSession->ScheduleId = $user->GetDefaultScheduleId();
        $userSession->ApiOnly = $user->GetIsApiOnly();

        $userSession->IsAdmin = $this->roleService->IsApplicationAdministrator($user);
        $userSession->IsGroupAdmin = $this->roleService->IsGroupAdministrator($user);
        $userSession->IsResourceAdmin = $this->roleService->IsResourceAdministrator($user);
        $userSession->IsScheduleAdmin = $this->roleService->IsScheduleAdministrator($user);
        $userSession->CSRFToken = CSRFToken::Create();
        $userSession->ForcePasswordReset = $user->MustChangePassword();
        $userSession->RememberMeToken = $user->RememberMeToken();
        $userSession->LoginToken = $user->LoginToken();
        $userSession->IsFirstLogin = empty($lastLogin);
        $userSession->DateFormat = $user->DateFormat();
        $userSession->TimeFormat = $user->TimeFormat();

        foreach ($user->Groups() as $group) {
            $userSession->Groups[] = $group->GroupId;
        }

        foreach ($user->GetAdminGroups() as $group) {
            $userSession->AdminGroups[] = $group->GroupId;
        }

        return $userSession;
    }

    public function ShowUsernamePrompt()
    {
        return true;
    }

    public function ShowPasswordPrompt()
    {
        return true;
    }

    public function ShowPersistLoginPrompt()
    {
        return true;
    }

    public function ShowForgotPasswordPrompt()
    {
        return true;
    }

    public function AllowUsernameChange()
    {
        return true;
    }

    public function AllowEmailAddressChange()
    {
        return true;
    }

    public function AllowPasswordChange()
    {
        return true;
    }

    public function AllowNameChange()
    {
        return true;
    }

    public function AllowPhoneChange()
    {
        return true;
    }

    public function AllowOrganizationChange()
    {
        return true;
    }

    public function AllowPositionChange()
    {
        return true;
    }

    public function GetRegistrationUrl()
    {
        return '';
    }

    public function GetPasswordResetUrl()
    {
        return '';
    }

    public function AllowRegistration()
    {
        return true;
    }

    public function AllowManualLogin()
    {
        return !Configuration::Instance()->GetSectionKey(ConfigSection::AUTHENTICATION, ConfigKeys::AUTHENTICATION_HIDE_BOOKED_LOGIN_PROMPT, new BooleanConverter());
    }
}