<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Values/RoleLevel.php');
require_once(ROOT_DIR . 'Domain/Values/NotificationPreferences.php');
require_once(ROOT_DIR . 'Domain/Values/CountryCodes.php');
require_once(ROOT_DIR . 'lib/Application/Authentication/namespace.php');

class User
{
    public function __construct()
    {
        $this->emailPreferences = new NotificationPreferences();
        $this->preferences = new UserPreferences();
    }

    /**
     * @var INotificationPreferences
     */
    protected $emailPreferences;

    protected $id;

    public function Id()
    {
        return $this->id;
    }

    protected $firstName;

    public function FirstName()
    {
        return $this->firstName . '';
    }

    protected $lastName;

    public function LastName()
    {
        return $this->lastName . '';
    }

    public function FullName()
    {
        return $this->FirstName() . ' ' . $this->LastName();
    }

    protected $emailAddress;

    public function EmailAddress()
    {
        return $this->emailAddress;
    }

    protected $username;

    public function Username()
    {
        return $this->username;
    }

    protected $language;

    public function Language()
    {
        return $this->language;
    }

    protected $timezone;

    public function Timezone()
    {
        return $this->timezone;
    }

    protected $homepageId;

    public function Homepage()
    {
        return $this->homepageId;
    }

    protected $statusId;

    /**
     * @return int|null|AccountStatus
     */
    public function StatusId()
    {
        return $this->statusId;
    }

    /**
     * @var string
     */
    private $lastLogin;

    /**
     * @return string
     */
    public function LastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * @var Date
     */
    private $dateCreated;

    /**
     * @return Date
     */
    public function DateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @var array|UserGroup[]
     */
    protected $groups = array();

    /**
     * @var UserGroup[]
     */
    private $addedGroups = array();

    /**
     * @var UserGroup[]
     */
    private $removedGroups = array();

    /**
     * @var array|UserGroup[]
     */
    protected $groupsICanAdminister = array();

    /**
     * @return array|UserGroup[]
     */
    public function Groups()
    {
        return $this->groups;
    }

    /**
     * @return UserGroup[]
     */
    public function GetAddedGroups()
    {
        return $this->addedGroups;
    }

    /**
     * @return UserGroup[]
     */
    public function GetRemovedGroups()
    {
        return $this->removedGroups;
    }

    /**
     * @param int $groupId
     * @return bool
     */
    public function IsInGroup($groupId)
    {
        foreach ($this->groups as $group) {
            if ($group->GroupId == $groupId) {
                return true;
            }
        }

        return false;
    }

    private $isCalendarSubscriptionAllowed = false;

    private $originalCredits = 0;

    /**
     * @return float
     */
    public function GetOriginalCredits()
    {
        return $this->originalCredits;
    }

    /**
     * @param bool $isAllowed
     */
    protected function SetIsCalendarSubscriptionAllowed($isAllowed)
    {
        $this->isCalendarSubscriptionAllowed = $isAllowed;
    }

    /**
     * @return bool
     */
    public function GetIsCalendarSubscriptionAllowed()
    {
        return $this->isCalendarSubscriptionAllowed;
    }

    private $publicId;

    /**
     * @param string $publicId
     */
    protected function SetPublicId($publicId)
    {
        $this->publicId = $publicId;
    }

    /**
     * @return string
     */
    public function GetPublicId()
    {
        return $this->publicId;
    }

    public function EnablePublicProfile()
    {
        if (empty($this->publicId)) {
            $this->SetPublicId(BookedStringHelper::Random(20));
        }
    }

    public function EnableSubscription()
    {
        $this->SetIsCalendarSubscriptionAllowed(true);
        $this->EnablePublicProfile();
    }

    public function DisableSubscription()
    {
        $this->SetIsCalendarSubscriptionAllowed(false);
    }

    public function Activate()
    {
        $this->statusId = AccountStatus::ACTIVE;
    }

    public function Deactivate()
    {
        $this->statusId = AccountStatus::INACTIVE;
    }

    protected $preferences;

    /**
     * @return UserPreferences
     */
    public function GetPreferences()
    {
        return $this->preferences;
    }

    /**
     * @return bool
     */
    public function IsRegistered()
    {
        return !empty($this->id);
    }

    public function ChangePreference($name, $value)
    {
        $this->preferences->Update($name, $value);
    }

    protected $creditsNote;

    /**
     * @return string
     */
    public function GetCreditsNote()
    {
        return $this->creditsNote;
    }

    /**
     * @var bool
     */
    private $permissionsChanged = false;
    private $removedPermissions = [];
    private $addedPermissions = [];
    private $removedViewPermissions = [];
    private $addedViewPermissions = [];
    private $removedNonePermissions = [];
    private $addedNonePermissions = [];

    /**
     * @var int[]
     */
    protected $allowedResourceIds = array();

    /**
     * @var int[]
     */
    protected $viewableResourceIds = array();

    /**
     * @var int[]
     */
    protected $noPermissionResourceIds = array();

    /**
     * @var EncryptedPassword
     */
    protected $encryptedPassword;

    /**
     * @return EncryptedPassword
     */
    public function GetEncryptedPassword()
    {
        return $this->encryptedPassword;
    }

    private $attributes = array();

    private $isGroupAdmin = false;
    private $isApplicationAdmin = false;
    private $isResourceAdmin = false;
    private $isScheduleAdmin = false;
    private $rememberMeToken = null;
    private $securityCleared = false;

    /**
     * @var null|Date
     */
    private $phoneLastChanged = null;

    /**
     * @return string|null
     */
    public function RememberMeToken()
    {
        return $this->rememberMeToken;
    }

    /**
     * @param string|null $token
     */
    public function WithRememberMeToken($token)
    {
        $this->rememberMeToken = $token;
    }

    private $loginToken = null;

    /**
     * @return string|null
     */
    public function LoginToken()
    {
        return $this->loginToken;
    }

    /**
     * @param string|null $token
     */
    public function WithLoginToken($token)
    {
        $this->loginToken = $token;
    }

    /**
     * @param string|null $token
     * @return bool
     */
    public function IsLoginTokenValid($token)
    {
        if (empty($this->loginToken)) {
            return true;
        }
        return !empty($token) && ($token == $this->loginToken);
    }

    /**
     * @param int[] $allowedResourceIds
     */
    public function WithAllowedPermissions($allowedResourceIds = array())
    {
        $this->permissionsChanged = false;
        $this->allowedResourceIds = $allowedResourceIds;
    }

    /**
     * @param int[] $viewableResourceIds
     */
    public function WithViewablePermission($viewableResourceIds = array())
    {
        $this->permissionsChanged = false;
        $this->viewableResourceIds = $viewableResourceIds;
    }

    /**
     * @param int[] $noneResourceIds
     */
    public function WithNonePermission($noneResourceIds = array())
    {
        $this->permissionsChanged = false;
        $this->noPermissionResourceIds = $noneResourceIds;
    }

    public function WithPreferences(UserPreferences $preferences)
    {
        $this->preferences = $preferences;
    }

    /**
     * @param UserGroup[] $groups
     */
    public function WithGroups($groups = array())
    {
        foreach ($groups as $group) {
            if ($group->IsGroupAdmin) {
                $this->isGroupAdmin = true;
            }
            if ($group->IsApplicationAdmin) {
                $this->isApplicationAdmin = true;
            }
            if ($group->IsResourceAdmin) {
                $this->isResourceAdmin = true;
            }
            if ($group->IsScheduleAdmin) {
                $this->isScheduleAdmin = true;
            }
        }

        $this->groups = $groups;
    }

    /**
     * @param UserGroup[] $ownedGroups
     */
    public function WithOwnedGroups($ownedGroups = array())
    {
        $this->groupsICanAdminister = $ownedGroups;
    }

    /**
     * @param int[] $allowedResourceIds
     */
    public function ChangeAllowedPermissions($allowedResourceIds = array())
    {
        $removed = array_diff($this->allowedResourceIds, $allowedResourceIds);
        $added = array_diff($allowedResourceIds, $this->allowedResourceIds);

        if (!empty($removed) || !empty($added)) {
            $this->permissionsChanged = true;
            $this->removedPermissions = $removed;
            $this->addedPermissions = $added;

            $this->allowedResourceIds = $allowedResourceIds;
        }
    }

    /**
     * @param int[] $viewableResourceIds
     */
    public function ChangeViewPermissions($viewableResourceIds = array())
    {
        $diff = new ArrayDiff($this->viewableResourceIds, $viewableResourceIds);
        $removed = $diff->GetRemovedFromArray1();
        $added = $diff->GetAddedToArray1();

        if ($diff->AreDifferent()) {
            $this->permissionsChanged = true;
            $this->removedViewPermissions = $removed;
            $this->addedViewPermissions = $added;

            $this->viewableResourceIds = $viewableResourceIds;
        }
    }

    /**
     * @param int[] $noneResourceIds
     */
    public function ChangeNonePermissions($noneResourceIds = array())
    {
        $diff = new ArrayDiff($this->noPermissionResourceIds, $noneResourceIds);
        $removed = $diff->GetRemovedFromArray1();
        $added = $diff->GetAddedToArray1();

        if ($diff->AreDifferent()) {
            $this->permissionsChanged = true;
            $this->removedNonePermissions = $removed;
            $this->addedNonePermissions = $added;

            $this->noPermissionResourceIds = $noneResourceIds;
        }
    }

    /**
     * @return int[]
     */
    public function GetAllowedResourceIds()
    {
        return $this->allowedResourceIds;
    }

    /**
     * @return int[]
     * @internal
     */
    public function GetAddedPermissions()
    {
        return array_values($this->addedPermissions);
    }

    /**
     * @return int[]
     * @internal
     */
    public function GetAddedViewPermissions()
    {
        return array_values($this->addedViewPermissions);
    }

    /**
     * @return int[]
     * @internal
     */
    public function GetAddedNoPermissions()
    {
        return array_values($this->addedNonePermissions);
    }

    /**
     * @return int[]
     * @internal
     */
    public function GetRemovedPermissions()
    {
        return array_values(array_merge($this->removedPermissions, $this->removedViewPermissions, $this->removedNonePermissions));
    }

    /**
     * @return int[]
     */
    public function GetAllowedViewResourceIds()
    {
        return $this->viewableResourceIds;
    }

    /**
     * @return int[]
     */
    public function GetNoPermissionResourceIds()
    {
        return $this->noPermissionResourceIds;
    }

    /**
     * @param INotificationPreferences $notificationPreferences
     * @internal
     */
    public function WithNotificationPreferences(INotificationPreferences $notificationPreferences)
    {
        $this->emailPreferences = $notificationPreferences;
    }

    /**
     * @param IDomainEvent $event
     * @return bool
     */
    public function WantsEventEmail(IDomainEvent $event)
    {
        return $this->emailPreferences->Exists($event->EventCategory(), $event->EventType(), NotificationPreferences::NOTIFICATION_METHOD_EMAIL);
    }

    /**
     * @param IDomainEvent $event
     * @param bool $turnedOn
     */
    public function ChangeEmailPreference(IDomainEvent $event, $turnedOn)
    {
        if ($turnedOn) {
            $this->emailPreferences->AddPreference($event, NotificationPreferences::NOTIFICATION_METHOD_EMAIL);
        } else {
            $this->emailPreferences->RemovePreference($event, NotificationPreferences::NOTIFICATION_METHOD_EMAIL);
        }
    }

    /**
     * @param IDomainEvent $event
     * @return bool
     */
    public function WantsEventSms(IDomainEvent $event)
    {
        return $this->emailPreferences->Exists($event->EventCategory(), $event->EventType(), NotificationPreferences::NOTIFICATION_METHOD_SMS);
    }

    /**
     * @param IDomainEvent $event
     * @param bool $turnedOn
     */
    public function ChangeSmsPreference(IDomainEvent $event, $turnedOn)
    {
        if ($turnedOn) {
            $this->emailPreferences->AddPreference($event, NotificationPreferences::NOTIFICATION_METHOD_SMS);
        } else {
            $this->emailPreferences->RemovePreference($event, NotificationPreferences::NOTIFICATION_METHOD_SMS);
        }
    }

    /**
     * @param string $loginTime
     * @param string $language
     */
    public function Login($loginTime, $language)
    {
        $this->lastLogin = $loginTime;
        $this->language = $language;
        $this->rememberMeToken = BookedStringHelper::Random();
        $this->EnablePublicProfile();
    }

    public function RefreshLoginToken()
    {
        $this->loginToken = BookedStringHelper::Random();
    }

    public function ClearSecuritySettings()
    {
        $this->loginToken = null;
        $this->rememberMeToken = null;
        $this->securityCleared = true;
    }

    public function WasSecurityCleared()
    {
        return $this->securityCleared;
    }

    /**
     * @return array|IDomainEvent[]
     */
    public function GetAddedEmailPreferences()
    {
        return $this->emailPreferences->GetAddedEmails();
    }

    /**
     * @return array|IDomainEvent[]
     */
    public function GetRemovedEmailPreferences()
    {
        return $this->emailPreferences->GetRemovedEmails();
    }

    /**
     * @return array|IDomainEvent[]
     */
    public function GetAddedSmsPreferences()
    {
        return $this->emailPreferences->GetAddedSms();
    }

    /**
     * @return array|IDomainEvent[]
     */
    public function GetRemovedSmsPreferences()
    {
        return $this->emailPreferences->GetRemovedSms();
    }

    public static function FromRow($row)
    {
        $user = new User();
        $user->id = $row[ColumnNames::USER_ID];
        $user->firstName = $row[ColumnNames::FIRST_NAME];
        $user->lastName = $row[ColumnNames::LAST_NAME];
        $user->emailAddress = $row[ColumnNames::EMAIL];
        $user->username = $row[ColumnNames::USERNAME];
        $user->language = $row[ColumnNames::LANGUAGE_CODE];
        $user->timezone = $row[ColumnNames::TIMEZONE_NAME];
        $user->statusId = $row[ColumnNames::USER_STATUS_ID];
        $user->encryptedPassword = new EncryptedPassword($row[ColumnNames::PASSWORD], $row[ColumnNames::SALT], $row[ColumnNames::PASSWORD_HASH_VERSION]);
        $user->homepageId = $row[ColumnNames::HOMEPAGE_ID];
        $user->lastLogin = $row[ColumnNames::LAST_LOGIN];
        $user->dateCreated = Date::FromDatabase($row[ColumnNames::DATE_CREATED]);
        $user->isCalendarSubscriptionAllowed = $row[ColumnNames::ALLOW_CALENDAR_SUBSCRIPTION];
        $user->publicId = $row[ColumnNames::PUBLIC_ID];
        $user->defaultScheduleId = $row[ColumnNames::DEFAULT_SCHEDULE_ID];

        $user->attributes[UserAttribute::Phone] = $row[ColumnNames::PHONE_NUMBER];
        $user->attributes[UserAttribute::Position] = $row[ColumnNames::POSITION];
        $user->attributes[UserAttribute::Organization] = $row[ColumnNames::ORGANIZATION];
        $user->attributes[UserAttribute::PhoneCountryCode] = $row[ColumnNames::PHONE_COUNTRY_CODE];

        $user->isApplicationAdmin = Configuration::Instance()->IsAdminEmail($row[ColumnNames::EMAIL]);
        $user->mustChangePassword = $row[ColumnNames::FORCE_PASSWORD_RESET] == 1;
        $user->phoneLastChanged = Date::FromDatabase($row[ColumnNames::PHONE_LAST_UPDATED]);

        $user->dateFormat = intval($row[ColumnNames::DATE_FORMAT]);
        $user->timeFormat = intval($row[ColumnNames::TIME_FORMAT]);

        return $user;
    }

    /**
     * @static
     * @param string $firstName
     * @param string $lastName
     * @param string $emailAddress
     * @param string $userName
     * @param string $language
     * @param string $timezone
     * @param EncryptedPassword $encryptedPassword
     * @param int $homepageId
     * @return User
     */
    public static function Create($firstName, $lastName, $emailAddress, $userName, $language, $timezone, $encryptedPassword,
                                  $homepageId = Pages::DEFAULT_HOMEPAGE_ID)
    {
        $user = new User();
        $user->firstName = $firstName;
        $user->lastName = $lastName;
        $user->emailAddress = $emailAddress;
        $user->username = $userName;
        $user->language = $language;
        $user->timezone = $timezone;
        $user->encryptedPassword = $encryptedPassword;
        $user->homepageId = $homepageId;
        $user->statusId = AccountStatus::ACTIVE;
        $user->loginToken = BookedStringHelper::Random();
        return $user;
    }

    /**
     * @static
     * @param string $firstName
     * @param string $lastName
     * @param string $emailAddress
     * @param string $userName
     * @param string $language
     * @param string $timezone
     * @param EncryptedPassword $encryptedPassword
     * @param int $homepageId
     * @return User
     */
    public static function CreatePending($firstName, $lastName, $emailAddress, $userName, $language, $timezone,
                                         $encryptedPassword, $homepageId = Pages::DEFAULT_HOMEPAGE_ID)
    {
        $user = self::Create($firstName, $lastName, $emailAddress, $userName, $language, $timezone, $encryptedPassword, $homepageId);
        $user->statusId = AccountStatus::AWAITING_ACTIVATION;
        return $user;
    }

    /**
     * @param int $userId
     */
    public function WithId($userId)
    {
        $this->id = $userId;
    }

    /**
     * @param string $loginTime
     */
    public function WithLastLogin($loginTime)
    {
        $this->lastLogin = $loginTime;
    }

    /**
     * @param EncryptedPassword $encryptedPassword
     */
    public function ChangePassword($encryptedPassword)
    {
        $this->encryptedPassword = $encryptedPassword;
        $this->mustChangePassword = false;
    }

    /**
     * @var bool
     */
    private $mustChangePassword = false;

    /**
     * @return bool
     */
    public function MustChangePassword()
    {
        return $this->mustChangePassword;
    }

    /**
     * @param bool $mustChange
     */
    public function SetMustChangePassword($mustChange)
    {
        $this->mustChangePassword = $mustChange;
    }

    public function ChangeName($firstName, $lastName)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function ChangeEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

    public function ChangeUsername($username)
    {
        $this->username = $username;
    }

    public function ChangeDefaultHomePage($homepageId)
    {
        $this->homepageId = $homepageId;
    }

    public function ChangeTimezone($timezoneName)
    {
        $this->timezone = $timezoneName;
    }

    public function ChangeLanguage($language)
    {
        $this->language = $language;
    }

    public function ChangeAttributes($phone, $organization, $position, $phoneCountryCode)
    {
        $phoneVal = empty($phone) ? null : trim($phone);
        $organizationVal = empty($organization) ? null : trim($organization);
        $positionVal = empty($position) ? null : trim($position);
        $phoneCountryCodeVal = empty($phoneCountryCode) ? null : strtoupper(trim($phoneCountryCode));

        $this->phoneLastChanged = $this->IsPhoneChanged($phoneVal, $phoneCountryCodeVal) ? Date::Now() : $this->phoneLastChanged;

        $this->attributes[UserAttribute::Phone] = $phoneVal;
        $this->attributes[UserAttribute::Organization] = $organizationVal;
        $this->attributes[UserAttribute::Position] = $positionVal;
        $this->attributes[UserAttribute::PhoneCountryCode] = empty($phone) ? null : $phoneCountryCodeVal;
    }

    public function IsPhoneChanged($phone, $countryCode)
    {
        $oldDigits = preg_replace('/[^0-9]/', '', $this->Phone() . '');
        $newDigits = preg_replace('/[^0-9]/', '', $phone . '');

        return $this->CountryCode() != $countryCode || $oldDigits != $newDigits;
    }

    /**
     * @return Date
     */
    public function PhoneLastModified()
    {
        return empty($this->phoneLastChanged) ? new NullDate() : $this->phoneLastChanged;
    }

    /**
     * @return string
     */
    public function PhoneWithCountryCode()
    {
        return CountryCodes::Get($this->CountryCode(), $this->Phone(), $this->Language())->phone . $this->Phone();
    }

    /**
     * @param UserAttribute|string $attributeName
     * @return string
     */
    public function GetAttribute($attributeName)
    {
        if (array_key_exists($attributeName, $this->attributes)) {
            return $this->attributes[$attributeName];
        }
        return null;
    }

    /**
     * @return string|null
     */
    public function Phone()
    {
        if (array_key_exists(UserAttribute::Phone, $this->attributes)) {
            return $this->attributes[UserAttribute::Phone];
        }
        return null;
    }

    /**
     * @return string|null
     */
    public function Organization()
    {
        if (array_key_exists(UserAttribute::Organization, $this->attributes)) {
            return $this->attributes[UserAttribute::Organization];
        }
        return null;
    }

    /**
     * @return string|null
     */
    public function Position()
    {
        if (array_key_exists(UserAttribute::Position, $this->attributes)) {
            return $this->attributes[UserAttribute::Position];
        }
        return null;
    }

    /**
     * @return string|null
     */
    public function CountryCode()
    {
        if (array_key_exists(UserAttribute::PhoneCountryCode, $this->attributes)) {
            return $this->attributes[UserAttribute::PhoneCountryCode];
        }
        return null;
    }

    /**
     * @return bool
     */
    public function IsGroupAdmin()
    {
        return $this->isGroupAdmin;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function IsAdminFor(User $user)
    {
        if ($this->isApplicationAdmin) {
            return true;
        }

        if (!$this->isGroupAdmin) {
            return false;
        }

        $adminIdsForUser = array();
        foreach ($user->Groups() as $userGroup) {
            if (!empty($userGroup->AdminGroupId)) {
                $adminIdsForUser[$userGroup->AdminGroupId] = true;
            }
        }

        foreach ($this->Groups() as $group) {
            if ($group->IsGroupAdmin) {
                if (array_key_exists($group->GroupId, $adminIdsForUser)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param IResource $resource
     * @return bool
     */
    public function IsResourceAdminFor(IResource $resource)
    {
        if ($this->isApplicationAdmin) {
            return true;
        }

        if ($this->isResourceAdmin || $this->isScheduleAdmin) {
            foreach ($this->groups as $group) {
                if (
                    ($group->GroupId == $resource->GetAdminGroupId()) ||
                    ($group->GroupId == $resource->GetScheduleAdminGroupId())
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    public function GetResourceAdminGroupIds()
    {
        if (!$this->isResourceAdmin) {
            return [];
        }

        $groups = [];
        foreach ($this->groups as $group) {
            if ($group->IsResourceAdmin) {
                $groups[] = $group->GroupId;
            }
        }

        return $groups;
    }

    public function GetScheduleAdminGroupIds()
    {
        if (!$this->isScheduleAdmin) {
            return [];
        }

        $groups = [];
        foreach ($this->groups as $group) {
            if ($group->IsScheduleAdmin) {
                $groups[] = $group->GroupId;
            }
        }

        return $groups;
    }

    /**
     * @param IResource[] $resources
     * @return bool
     */
    public function IsResourceAdminForOneOf($resources)
    {
        foreach ($resources as $resource) {
            if ($this->IsResourceAdminFor($resource)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param ISchedule $schedule
     * @return bool
     */
    public function IsScheduleAdminFor(ISchedule $schedule)
    {
        if ($this->isApplicationAdmin) {
            return true;
        }

        if (!$this->isScheduleAdmin) {
            return false;
        }

        foreach ($this->groups as $group) {
            if ($group->GroupId == $schedule->GetAdminGroupId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int|RoleLevel $roleLevel
     * @return bool
     */
    public function IsInRole($roleLevel)
    {
        if ($roleLevel == RoleLevel::GROUP_ADMIN) {
            return $this->isGroupAdmin;
        }
        if ($roleLevel == RoleLevel::APPLICATION_ADMIN) {
            return $this->isApplicationAdmin;
        }
        if ($roleLevel == RoleLevel::RESOURCE_ADMIN) {
            return $this->isResourceAdmin;
        }
        if ($roleLevel == RoleLevel::SCHEDULE_ADMIN) {
            return $this->isScheduleAdmin;
        }

        return false;
    }

    /**
     * @static
     * @return User
     */
    public static function Null()
    {
        return new NullUser();
    }

    /**
     * @return array|UserGroup[]
     */
    public function GetAdminGroups()
    {
        return $this->groupsICanAdminister;
    }

    /**
     * @param $attribute AttributeValue
     * @param $adminOnly bool
     */
    public function WithAttribute(AttributeValue $attribute, $adminOnly = false)
    {
        $this->attributeValues[$attribute->AttributeId] = $attribute;
        if ($adminOnly) {
            $this->adminAttributesIds[] = $attribute->AttributeId;
        }
    }

    /**
     * @var array|int[]
     */
    private $adminAttributesIds = array();

    /**
     * @var array|AttributeValue[]
     */
    private $attributeValues = array();

    /**
     * @var array|AttributeValue[]
     */
    private $_addedAttributeValues = array();

    /**
     * @var array|AttributeValue[]
     */
    private $_removedAttributeValues = array();

    /**
     * @var float
     */
    private $credits;

    /**
     * @param $attributes AttributeValue[]|array
     */
    public function ChangeCustomAttributes($attributes, $removeAttrOnDiff = true)
    {
        $diff = new ArrayDiff($this->attributeValues, $attributes);

        $added = $diff->GetAddedToArray1();
        $removed = $diff->GetRemovedFromArray1();

        /** @var $attribute AttributeValue */
        foreach ($added as $attribute) {
            $this->_addedAttributeValues[] = $attribute;
        }

        if ($removeAttrOnDiff) {
            /** @var $attribute AttributeValue */
            foreach ($removed as $attribute) {
                if (!in_array($attribute->AttributeId, $this->adminAttributesIds)) {
                    $this->_removedAttributeValues[] = $attribute;
                }
            }
        }

        foreach ($attributes as $attribute) {
            $this->AddAttributeValue($attribute);
        }
    }

    /**
     * @param $attributeValue AttributeValue
     */
    public function AddAttributeValue($attributeValue)
    {
        $this->attributeValues[$attributeValue->AttributeId] = $attributeValue;
    }

    /**
     * @return array|AttributeValue[]
     */
    public function GetAddedAttributes()
    {
        return $this->_addedAttributeValues;
    }

    /**
     * @return array|AttributeValue[]
     */
    public function GetRemovedAttributes()
    {
        return $this->_removedAttributeValues;
    }

    /**
     * @param $customAttributeId
     * @return mixed
     */
    public function GetAttributeValue($customAttributeId)
    {
        if (array_key_exists($customAttributeId, $this->attributeValues)) {
            return $this->attributeValues[$customAttributeId]->Value;
        }

        return null;
    }

    /**
     * @var int|null
     */
    protected $defaultScheduleId;

    /**
     * @return int|null
     */
    public function GetDefaultScheduleId()
    {
        return $this->defaultScheduleId;
    }

    /**
     * @param int $scheduleId
     */
    public function ChangeDefaultSchedule($scheduleId)
    {
        $this->defaultScheduleId = $scheduleId;
    }

    /**
     * @param int $scheduleId
     */
    public function WithDefaultSchedule($scheduleId)
    {
        $this->defaultScheduleId = $scheduleId;
    }

    /**
     * @param $groupId int|int[]
     * @return bool
     */
    public function IsGroupAdminFor($groupId)
    {
        if (!is_array($groupId)) {
            $groupId = array($groupId);
        }

        foreach ($this->groupsICanAdminister as $group) {
            if (in_array($group->GroupId, $groupId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $preferenceName string
     * @return null|string
     */
    public function GetPreference($preferenceName)
    {
        return $this->preferences->Get($preferenceName);
    }

    public function ChangeGroups($groups)
    {
        $diff = new ArrayDiff($this->groups, $groups);

        $added = $diff->GetAddedToArray1();
        $removed = $diff->GetRemovedFromArray1();

        /** @var $group UserGroup */
        foreach ($added as $group) {
            $this->addedGroups[] = $group;
        }

        /** @var $group UserGroup */
        foreach ($removed as $group) {
            $this->removedGroups[] = $group;
        }

        $this->WithGroups($groups);
    }

    /**
     * @param $attribute AttributeValue
     */
    public function ChangeCustomAttribute($attribute)
    {
        $this->_removedAttributeValues[] = $attribute;
        $this->_addedAttributeValues[] = $attribute;
        $this->AddAttributeValue($attribute);
    }

    public function GetCurrentCredits()
    {
        return empty($this->credits) ? 0 : $this->credits;
    }

    public function WithCredits($credits)
    {
        $this->originalCredits = empty($credits) ? 0 : floatval($credits);
        $this->credits = empty($credits) ? 0 : floatval($credits);
    }

    public function ChangeCurrentCredits($credits, $note = '')
    {
        $this->credits = !empty($credits) ? floatval($credits) : 0;
        $this->creditsNote = $note;
    }

    public function AddCredits($credits, $note = '')
    {
        $this->credits = floatval($this->credits) + floatval($credits);
        $this->creditsNote = $note;
    }

    public function HaveCreditsChanged()
    {
        return $this->credits != $this->originalCredits;
    }

    /**
     * @var Date|null
     */
    protected $termsAcceptanceDate;

    /**
     * @return Date|null
     */
    public function TermsAcceptanceDate()
    {
        return $this->termsAcceptanceDate;
    }

    /**
     * @param bool $accepted
     */
    public function AcceptTerms($accepted)
    {
        if ($accepted) {
            $this->termsAcceptanceDate = Date::Now();
        }
    }

    protected $isApiOnly = false;

    /**
     * @param bool $isApiOnly
     */
    public function IsApiOnly($isApiOnly)
    {
        $this->isApiOnly = intval($isApiOnly);
    }

    /**
     * return bool
     */
    public function GetIsApiOnly()
    {
        return intval($this->isApiOnly);
    }

    /**
     * @return string|null
     */
    public function GetReservationColor()
    {
        return $this->GetPreference(UserPreferences::RESERVATION_COLOR);
    }

    /**
     * @return bool
     */
    public function IsResourceAdmin()
    {
        return $this->isResourceAdmin;
    }

    /**
     * @return bool
     */
    public function IsScheduleAdmin()
    {
        return $this->isScheduleAdmin;
    }

    /**
     * @return bool
     */
    public function IsApplicationAdmin()
    {
        return $this->isApplicationAdmin;
    }

    public function IsSmsOptedIn()
    {
        if (empty($this->smsOptInDate)
            || $this->smsOptInDate->IsNull()
            || $this->HasOutstandingSmsConfirmation()
            || empty($this->Phone())
            || empty($this->CountryCode())
            || $this->PhoneLastModified()->GreaterThan($this->smsOptInDate)
            || !$this->IsSmsRegionSupported()
        ) {
            return false;
        };

        return empty($this->smsOptOutDate) || $this->smsOptOutDate->IsNull();
    }

    public function IsSmsRegionSupported()
    {
        return $this->CountryCode() == "US";
    }

    public function HasOutstandingSmsConfirmation()
    {
        return !empty($this->smsConfirmationCode);
    }

    /**
     * @var Date|null
     */
    private $smsOptInDate;

    /**
     * @var Date|null
     */
    private $smsOptOutDate;

    /**
     * @var string|null
     */
    private $smsConfirmationCode;

    /**
     * @var int|null
     */
    private $smsConfigId;

    public function SmsConfigId()
    {
        return $this->smsConfigId;
    }

    public function WithSms(Date $optInDate, Date $optOutDate, ?string $confirmationCode, ?int $smsConfigId)
    {
        $this->smsOptInDate = $optInDate;
        $this->smsOptOutDate = $optOutDate;
        $this->smsConfirmationCode = $confirmationCode;
        $this->smsConfigId = $smsConfigId;
    }

    public function WithPhoneLastChanged(Date $date)
    {
        $this->phoneLastChanged = $date;
    }

    public function IsSmsConfirmationCodeValid(string $otp)
    {
        return !empty($this->smsConfirmationCode) && !empty($otp) && strtoupper($this->smsConfirmationCode) == strtoupper($otp);
    }

    /**
     * @var int|null
     */
    private $dateFormat = null;

    /**
     * @return int|null
     */
    public function DateFormat()
    {
        return empty($this->dateFormat) ? null : $this->dateFormat;
    }

    /**
     * @var int|null
     */
    private $timeFormat = null;

    /**
     * @return int|null
     */
    public function TimeFormat()
    {
        return empty($this->timeFormat) ? null : $this->timeFormat;
    }

    /**
     * @param int|null $dateFormat
     * @param int|null $timeFormat
     */
    public function ChangeDateTimeFormat($dateFormat, $timeFormat)
    {
        $this->dateFormat = $dateFormat;
        $this->timeFormat = $timeFormat;
    }
}

class NullUser extends User
{
    public function Id()
    {
        return null;
    }
}

class GuestUser extends User
{
    public function __construct($email)
    {
        parent::__construct();
        $this->emailAddress = $email;
        $this->language = Configuration::Instance()->GetKey(ConfigKeys::LANGUAGE);
        $this->timezone = Configuration::Instance()->GetDefaultTimezone();
    }
}

class UserAttribute
{
    const Phone = 'phone';
    const Organization = 'organization';
    const Position = 'position';
    const PhoneCountryCode = 'phoneCountryCode';

    /**
     * @var array|string[]
     */
    private $attributeValues = array();

    public function __construct($attributeValues = array())
    {
        $this->attributeValues = $attributeValues;
    }

    /**
     * @param string|UserAttribute $attributeName
     * @return null|string
     */
    public function Get($attributeName)
    {
        if (array_key_exists($attributeName, $this->attributeValues)) {
            return $this->attributeValues[$attributeName];
        }

        return null;
    }
}

class UserGroup
{
    /**
     * @var int
     */
    public $GroupId;

    /**
     * @var string
     */
    public $GroupName;

    /**
     * @var int|null
     */
    public $AdminGroupId;

    /**
     * @var bool
     */
    public $IsGroupAdmin = false;

    /**
     * @var bool
     */
    public $IsApplicationAdmin = false;

    /**
     * @var bool
     */
    public $IsResourceAdmin = false;

    /**
     * @var bool
     */
    public $IsScheduleAdmin = false;

    /**
     * @param int $groupId
     * @param string $groupName
     * @param int|null $adminGroupId
     * @param int|RoleLevel $roleLevel defaults to none
     */
    public function __construct($groupId, $groupName, $adminGroupId = null, $roleLevel = RoleLevel::NONE)
    {
        $this->GroupId = $groupId;
        $this->GroupName = $groupName;
        $this->AdminGroupId = $adminGroupId;
        $this->AddRole($roleLevel);
    }

    /**
     * @param int|null|RoleLevel $roleLevel
     */
    public function AddRole($roleLevel = null)
    {
        if ($roleLevel == RoleLevel::GROUP_ADMIN) {
            $this->IsGroupAdmin = true;
        }
        if ($roleLevel == RoleLevel::APPLICATION_ADMIN) {
            $this->IsApplicationAdmin = true;
        }
        if ($roleLevel == RoleLevel::RESOURCE_ADMIN) {
            $this->IsResourceAdmin = true;
        }
        if ($roleLevel == RoleLevel::SCHEDULE_ADMIN) {
            $this->IsScheduleAdmin = true;
        }
    }

    public function __toString()
    {
        return $this->GroupId . '';
    }
}
