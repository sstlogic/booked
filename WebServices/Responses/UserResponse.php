<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'WebServices/Responses/CustomAttributes/CustomAttributeResponse.php');
require_once(ROOT_DIR . 'WebServices/Responses/ResourceItemResponse.php');
require_once(ROOT_DIR . 'WebServices/Responses/Group/GroupItemResponse.php');

class UserResponse extends RestResponse
{
	public $id;
	public $userName;
	public $firstName;
	public $lastName;
	public $emailAddress;
	public $phoneNumber;
	public $phoneCountryCode;
	public $lastLogin;
	public $statusId;
	public $timezone;
	public $organization;
	public $position;
	public $language;
	public $icsUrl;
	public $defaultScheduleId;
	public $currentCredits;
	public $reservationColor;

	/** @var array|CustomAttributeResponse[] */
	public $customAttributes = array();
	/** @var array|ResourceItemResponse[] */
	public $permissions = array();
	/** @var array|GroupItemResponse[] */
	public $groups = array();

	public function __construct(IRestServer $server, User $user, IEntityAttributeList $attributes)
	{
		$userId = intval($user->Id());
		$this->id = $userId;
		$this->emailAddress = apidecode($user->EmailAddress());
		$this->firstName = apidecode($user->FirstName());
		$this->lastName = apidecode($user->LastName());
		$this->language = $user->Language();
		$this->lastLogin = Date::FromDatabase($user->LastLogin())->ToIso();
		$this->organization = apidecode($user->GetAttribute(UserAttribute::Organization));
		$this->phoneNumber = apidecode($user->GetAttribute(UserAttribute::Phone));
        $this->phoneCountryCode = $user->GetAttribute(UserAttribute::PhoneCountryCode);
		$this->position = apidecode($user->GetAttribute(UserAttribute::Position));
		$this->statusId = $user->StatusId();
		$this->timezone = $user->Timezone();
		$this->userName = apidecode($user->Username());
		$this->defaultScheduleId = $user->GetDefaultScheduleId();
		$this->currentCredits = $user->GetCurrentCredits();
		$this->reservationColor = $user->GetPreference(UserPreferences::RESERVATION_COLOR);

		$attributeValues = $attributes->GetAttributes($userId);

		if (!empty($attributeValues))
		{
			foreach ($attributeValues as $av)
			{
				$this->customAttributes[] = new CustomAttributeResponse($server, $av->Id(), $av->Label(), $av->Value());
			}
		}

		foreach ($user->GetAllowedResourceIds() as $allowedResourceId)
		{
			$this->permissions[] = new ResourceItemResponse($server, $allowedResourceId, '');
		}

		foreach ($user->Groups() as $group)
		{
			$this->groups[] = new UserGroupItemResponse($server, $group->GroupId, $group->GroupName);
		}

		if ($user->GetIsCalendarSubscriptionAllowed())
		{
			$url = new CalendarSubscriptionUrl($user->GetPublicId(), null, null);
			$this->icsUrl = $url->__toString();
		}
	}

	public static function Example()
	{
		return new ExampleUserResponse();
	}
}

class UserGroupItemResponse extends RestResponse
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var bool
     */
    public $isDefault;

    /**
     * @var int[]
     */
    public $roleIds;

    public function __construct(IRestServer $server, $id, $name)
    {
        $this->id = $id;
        $this->name = $name;
        $this->AddService($server, WebServices::GetGroup, array(WebServiceParams::GroupId => $id));
    }

    public static function Example()
    {
        return new ExampleUserGroupItemResponse();
    }
}

class ExampleUserResponse extends UserResponse
{
	public function __construct()
	{
		$date = Date::Now()->ToIso();
		$this->id = 1;
		$this->emailAddress = 'email@address.com';
		$this->firstName = 'first';
		$this->lastName = 'last';
		$this->language = 'language_code';
		$this->lastLogin = $date;
		$this->organization = 'organization';
		$this->phoneNumber = '123-456-7890';
        $this->phoneCountryCode = 'US';
		$this->statusId = 'statusId';
		$this->timezone = 'timezone';
		$this->userName = 'username';
		$this->position = 'position';
		$this->icsUrl = 'webcal://url/to/calendar';
		$this->customAttributes = array(CustomAttributeResponse::Example());
		$this->permissions = array(ResourceItemResponse::Example());
		$this->groups = array(UserGroupItemResponse::Example());
		$this->defaultScheduleId = 1;
		$this->currentCredits = '2.50';
		$this->reservationColor = '#000000';
	}
}

class ExampleUserGroupItemResponse extends UserGroupItemResponse
{
    public function __construct()
    {
        $this->id = 1;
        $this->name = 'group name';
    }
}