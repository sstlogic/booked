<?php
/**
 * Copyright 2013-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Authentication/namespace.php');
require_once(ROOT_DIR . 'lib/Email/Messages/AccountDeletedEmail.php');

interface IManageUsersService
{
    /**
     * @param $username string
     * @param $email string
     * @param $firstName string
     * @param $lastName string
     * @param $plainTextPassword string
     * @param $timezone string
     * @param $language string
     * @param $homePageId int
     * @param $extraAttributes array|string[]
     * @param $customAttributes array|AttributeValue[]
     * @param $apiOnly bool
     * @param $color string
     * @return User
     */
    public function AddUser(
        $username,
        $email,
        $firstName,
        $lastName,
        $plainTextPassword,
        $timezone,
        $language,
        $homePageId,
        $extraAttributes,
        $customAttributes,
        $apiOnly,
        $color);

    /**
     * @param $userId int
     * @param $username string
     * @param $email string
     * @param $firstName string
     * @param $lastName string
     * @param $timezone string
     * @param $extraAttributes string[]|array
     * @param $customAttributes AttributeValue[]
     * @param $apiOnly bool
     * @param $color string
     * @param $homepage int|null
     * @return User
     */
    public function UpdateUser($userId, $username, $email, $firstName, $lastName, $timezone, $extraAttributes, $customAttributes, $apiOnly, $color, $homepage = null);

    /**
     * @param $userId int
     * @param $attribute AttributeValue
     */
    public function ChangeAttribute($userId, $attribute);

    /**
     * @param $userId int
     * @param $attributes AttributeValue[]
     */
    public function ChangeAttributes($userId, $attributes);

    /**
     * @param $userId int
     */
    public function DeleteUser($userId);

    /**
     * @param User $user
     * @param int[] $groupIds
     */
    public function ChangeGroups($user, $groupIds);

    /**
     * @param int $userId
     * @param string $password
     */
    public function UpdatePassword($userId, $password);

    /**
     * @param string $email
     * @return User
     */
    public function LoadUser($email);

    /**
     * @param int $userId
     * @param int|AccountStatus $statusId
     */
    public function ChangeStatus($userId, $statusId);
}

class ManageUsersService implements IManageUsersService
{
    /**
     * @var IRegistration
     */
    private $registration;

    /**
     * @var IUserRepository
     */
    private $userRepository;

    /**
     * @var IGroupRepository
     */
    private $groupRepository;

    /**
     * @var IUserViewRepository
     */
    private $userViewRepository;

    /**
     * @var IPassword
     */
    private $password;

    public function __construct(IRegistration       $registration,
                                IUserRepository     $userRepository,
                                IGroupRepository    $groupRepository,
                                IUserViewRepository $userViewRepository,
                                IPassword           $password)
    {
        $this->registration = $registration;
        $this->userRepository = $userRepository;
        $this->groupRepository = $groupRepository;
        $this->userViewRepository = $userViewRepository;
        $this->password = $password;
    }

    public function AddUser(
        $username,
        $email,
        $firstName,
        $lastName,
        $plainTextPassword,
        $timezone,
        $language,
        $homePageId,
        $extraAttributes,
        $customAttributes,
        $apiOnly,
        $color)
    {
        $user = $this->registration->Register($username,
            $email,
            $firstName,
            $lastName,
            $plainTextPassword,
            $timezone,
            $language,
            $homePageId,
            $extraAttributes,
            $customAttributes,
            null,
            null,
            $apiOnly,
            $color);

        return $user;
    }

    public function ChangeAttribute($userId, $attributeValue)
    {
        $user = $this->userRepository->LoadById($userId);
        $user->ChangeCustomAttribute($attributeValue);
        $this->userRepository->Update($user);
    }

    public function ChangeAttributes($userId, $attributes)
    {
        $user = $this->userRepository->LoadById($userId);
        foreach ($attributes as $attribute) {
            $user->ChangeCustomAttribute($attribute);
        }
        $this->userRepository->Update($user);
    }

    public function DeleteUser($userId, $notify = true)
    {
        $currentUser = ServiceLocator::GetServer()->GetUserSession();
        if ($currentUser->UserId == $userId) {
            // don't delete own account
            return;
        }

        $user = $this->userRepository->LoadById($userId);
        $this->userRepository->DeleteById($userId);

        if ($notify && Configuration::Instance()->GetKey(ConfigKeys::REGISTRATION_NOTIFY, new BooleanConverter())) {

            $applicationAdmins = $this->userViewRepository->GetApplicationAdmins();
            $groupAdmins = $this->userViewRepository->GetGroupAdmins($userId);

            foreach ($applicationAdmins as $applicationAdmin) {
                ServiceLocator::GetEmailService()->Send(new AccountDeletedEmail($user, $applicationAdmin, $currentUser));
            }

            foreach ($groupAdmins as $groupAdmin) {
                ServiceLocator::GetEmailService()->Send(new AccountDeletedEmail($user, $groupAdmin, $currentUser));
            }
        }
    }

    public function UpdateUser($userId, $username, $email, $firstName, $lastName, $timezone, $extraAttributes, $customAttributes, $apiOnly, $color, $homepage = null)
    {
        $attributes = new UserAttribute($extraAttributes);
        $user = $this->userRepository->LoadById($userId);
        $user->ChangeName($firstName, $lastName);
        $user->ChangeEmailAddress($email);
        $user->ChangeUsername($username);
        $user->ChangeTimezone($timezone);
        $user->ChangeAttributes($attributes->Get(UserAttribute::Phone),
            $attributes->Get(UserAttribute::Organization),
            $attributes->Get(UserAttribute::Position),
            $attributes->Get(UserAttribute::PhoneCountryCode));
        $user->IsApiOnly($apiOnly);
        if (!empty($homepage)) {
            $user->ChangeDefaultHomePage($homepage);
        }

        foreach ($customAttributes as $attribute) {
            $user->ChangeCustomAttribute($attribute);
        }
        $user->ChangePreference(UserPreferences::RESERVATION_COLOR, $color);
        $this->userRepository->Update($user);

        return $user;
    }

    public function ChangeGroups($user, $groupIds)
    {
        if (is_null($groupIds)) {
            return;
        }

        $existingGroupIds = array();
        foreach ($user->Groups() as $group) {
            $existingGroupIds[] = $group->GroupId;
        }

        foreach ($groupIds as $targetGroupId) {
            if (!in_array($targetGroupId, $existingGroupIds)) {
                // add group
                $group = $this->groupRepository->LoadById($targetGroupId);
                $group->AddUser($user->Id());
                $this->groupRepository->Update($group);
            }
        }

        foreach ($existingGroupIds as $existingId) {
            if (!in_array($existingId, $groupIds)) {
                // remove user
                $group = $this->groupRepository->LoadById($existingId);
                $group->RemoveUser($user->Id());
                $this->groupRepository->Update($group);
            }
        }
    }

    public function UpdatePassword($userId, $password)
    {
        $user = $this->userRepository->LoadById($userId);

        $encrypted = $this->password->Encrypt($password);

        $user->ChangePassword($encrypted);

        $this->userRepository->Update($user);
    }

    public function LoadUser($email)
    {
        return $this->userRepository->LoadByUsername($email);
    }

    public function ChangeStatus($userId, $statusId)
    {
        $user = $this->userRepository->LoadById($userId);

        if ($statusId == AccountStatus::ACTIVE) {
            $user->Activate();
        }
        if ($statusId == AccountStatus::INACTIVE) {
            $user->Deactivate();
        }

        $this->userRepository->Update($user);
    }
}