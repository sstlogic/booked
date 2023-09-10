<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/namespace.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/ReservationEvents.php');

class Registration implements IRegistration
{
    /**
     * @var IPassword
     */
    private $password;

    /**
     * @var IUserRepository
     */
    private $userRepository;

    /**
     * @var IRegistrationNotificationStrategy
     */
    private $notificationStrategy;

    /**
     * @var IRegistrationPermissionStrategy
     */
    private $permissionAssignmentStrategy;

    /**
     * @var IGroupViewRepository
     */
    private $groupRepository;

    /**
     * @param null|IPassword $password
     * @param null|IUserRepository $userRepository
     * @param null|IRegistrationNotificationStrategy $notificationStrategy
     * @param null|IRegistrationPermissionStrategy $permissionAssignmentStrategy
     * @param null|IGroupViewRepository $groupRepository
     */
    public function __construct($password = null,
                                $userRepository = null,
                                $notificationStrategy = null,
                                $permissionAssignmentStrategy = null,
                                $groupRepository = null)
    {
        $this->password = $password;
        $this->userRepository = $userRepository;
        $this->notificationStrategy = $notificationStrategy;
        $this->permissionAssignmentStrategy = $permissionAssignmentStrategy;
        $this->groupRepository = $groupRepository;

        if ($password == null) {
            $this->password = new Password();
        }

        if ($userRepository == null) {
            $this->userRepository = new UserRepository();
        }

        if ($notificationStrategy == null) {
            $this->notificationStrategy = new RegistrationNotificationStrategy();
        }

        if ($permissionAssignmentStrategy == null) {
            $this->permissionAssignmentStrategy = new RegistrationPermissionStrategy();
        }

        if ($groupRepository == null) {
            $this->groupRepository = new GroupRepository();
        }
    }

    public function Register($username,
                             $email,
                             $firstName,
                             $lastName,
                             $plainTextPassword,
                             $timezone,
                             $language,
                             $homepageId,
                             $additionalFields = [],
                             $attributeValues = [],
                             $groups = null,
                             $acceptTerms = false,
                             $apiOnly = false,
                             $color = null)
    {
        $homepageId = empty($homepageId) ? Pages::DEFAULT_HOMEPAGE_ID : $homepageId;
        $encryptedPassword = $this->password->Encrypt($plainTextPassword);
        $timezone = empty($timezone) ? Configuration::Instance()->GetKey(ConfigKeys::DEFAULT_TIMEZONE) : $timezone;

        $attributes = new UserAttribute($additionalFields);

        if ($this->CreatePending()) {
            $user = User::CreatePending($firstName, $lastName, $email, $username, $language, $timezone, $encryptedPassword, $homepageId);
        } else {
            $user = User::Create($firstName, $lastName, $email, $username, $language, $timezone, $encryptedPassword, $homepageId);
        }

        $user->ChangeAttributes($attributes->Get(UserAttribute::Phone), $attributes->Get(UserAttribute::Organization), $attributes->Get(UserAttribute::Position), $attributes->Get(UserAttribute::PhoneCountryCode));
        $user->ChangeCustomAttributes($attributeValues);
        $user->AcceptTerms($acceptTerms);
        $user->IsApiOnly($apiOnly);
        $user->ChangePreference(UserPreferences::RESERVATION_COLOR, $color);

        if ($groups != null) {
            $user->WithGroups($groups);
        }

        if (Configuration::Instance()->GetKey(ConfigKeys::REGISTRATION_AUTO_SUBSCRIBE_EMAIL, new BooleanConverter())) {
            foreach (ReservationEvent::DefaultSubscribeEvents() as $event) {
                $user->ChangeEmailPreference($event, true);
            }
        }

        $userId = $this->userRepository->Add($user);
        if ($user->Id() != $userId) {
            $user->WithId($userId);
        }
        $this->permissionAssignmentStrategy->AddAccount($user);
        $this->notificationStrategy->NotifyAccountCreated($user, $plainTextPassword);

        return $user;
    }

    /**
     * @return bool
     */
    protected function CreatePending()
    {
        $requireActivation = Configuration::Instance()->GetKey(ConfigKeys::REGISTRATION_REQUIRE_ACTIVATION, new BooleanConverter());
        $mfa = Configuration::Instance()->GetSectionKey(ConfigSection::MFA, ConfigKeys::MFA_TYPE);

        return $requireActivation || $mfa != "";
    }

    public function UserExists($loginName, $emailAddress)
    {
        $userId = $this->userRepository->UserExists($emailAddress, $loginName);
        return !empty($userId);
    }

    public function Synchronize(AuthenticatedUser $user, $insertOnly = false, $overwritePassword = true)
    {
        $userId = $this->userRepository->UserExists($user->Email(), $user->UserName());
        if (!empty($userId)) {
            if ($insertOnly) {
                return $this->userRepository->LoadById($userId);
            }

            $password = null;
            $salt = null;
            $version = Password::$CURRENT_HASH_VERSION;

            if ($overwritePassword) {
                $encryptedPassword = $this->password->Encrypt($user->Password());
                $password = $encryptedPassword->EncryptedPassword();
                $salt = $encryptedPassword->Salt();
                $version = $encryptedPassword->Version();
            }

            Log::Debug('User exists, synchronizing.', ['username' => $user->Username(), 'email' => $user->Email(), 'userId' => $userId]);
            $command = new UpdateUserFromLdapCommand($userId, $user->UserName(), $user->Email(), $user->FirstName(), $user->LastName(), $password, $salt, $version, $user->Phone(), $user->Organization(), $user->Title());
            ServiceLocator::GetDatabase()->Execute($command);

            if ($this->GetUserGroups($user) != null) {
                $updatedUser = $this->userRepository->LoadById($userId);
                $updatedUser->ChangeGroups($this->GetUserGroups($user));
                $this->userRepository->Update($updatedUser);
                return $updatedUser;
            }

            return $this->userRepository->LoadById($userId);
        } else {
            Log::Debug('User does not exist, adding.', ['username' => $user->Username(), 'email' => $user->Email()]);

            $defaultHomePageId = Configuration::Instance()->GetKey(ConfigKeys::DEFAULT_HOMEPAGE, new IntConverter());
            $additionalFields = [
                UserAttribute::Phone => $user->Phone(),
                UserAttribute::Organization => $user->Organization(),
                UserAttribute::Position => $user->Title(),
                UserAttribute::PhoneCountryCode => null];

            return $this->Register($user->UserName(),
                $user->Email(),
                $user->FirstName(),
                $user->LastName(),
                $user->Password(),
                $user->TimezoneName(),
                $user->LanguageCode(),
                empty($defaultHomePageId) ? Pages::DEFAULT_HOMEPAGE_ID : $defaultHomePageId,
                $additionalFields,
                [],
                $this->GetUserGroups($user));
        }
    }

    /**
     * @param AuthenticatedUser $user
     * @return null|UserGroup[]
     */
    private function GetUserGroups(AuthenticatedUser $user)
    {
        $userGroups = $user->GetGroups();

        if (empty($userGroups)) {
            return null;
        }

        $lowercaseGroups = array_map('trim', array_map('strtolower', $userGroups));

        $groupsToSync = array();
        $groups = $this->groupRepository->GetList()->Results();

        /** @var GroupItemView $group */
        foreach ($groups as $group) {
            if (in_array(strtolower($group->Name()), $lowercaseGroups)) {
                Log::Debug('Syncing group for user', ['groupName' => $group->Name(), 'username' => $user->Username()]);
                $groupsToSync[] = new UserGroup($group->Id(), $group->Name());
            } else {
                Log::Debug('User is not part of group, sync skipped', ['groupName' => $group->Name(), 'username' => $user->Username()]);
            }
        }

        return $groupsToSync;
    }
}

class AdminRegistration extends Registration
{
    protected function CreatePending()
    {
        return false;
    }
}

class GuestRegistration extends Registration
{
    protected function CreatePending()
    {
        return false;
    }
}