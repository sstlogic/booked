<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/WebService/namespace.php');

class UserItemResponse extends RestResponse
{
	public $id;
	public $userName;
	public $firstName;
	public $lastName;
	public $emailAddress;
	public $phoneNumber;
	public $phoneCountryCode;
	public $dateCreated;
	public $lastLogin;
	public $statusId;
	public $timezone;
	public $organization;
	public $position;
	public $language;
	/** @var array|CustomAttributeResponse[] */
	public $customAttributes = array();
	public $currentCredits;
	public $reservationColor;

	/**
	 * @param IRestServer $server
	 * @param UserItemView $user
	 * @param array|string[] $attributeLabels
	 */
	public function __construct(IRestServer $server, UserItemView $user, $attributeLabels)
	{
		$userId = intval($user->Id);
		$this->id = $userId;
		$this->dateCreated = $user->DateCreated->ToIso();
		$this->emailAddress = apidecode($user->Email);
		$this->firstName = apidecode($user->First);
		$this->lastName = apidecode($user->Last);
		$this->language = $user->Language;
		$this->lastLogin = $user->LastLogin->ToIso();
		$this->organization = apidecode($user->Organization);
		$this->phoneNumber = apidecode($user->Phone);
		$this->phoneCountryCode = $user->PhoneCountryCode;
		$this->position = apidecode($user->Position);
		$this->statusId = $user->StatusId;
		$this->timezone = $user->Timezone;
		$this->userName = apidecode($user->Username);
		$this->currentCredits = $user->CurrentCreditCount;
		$this->reservationColor = $user->ReservationColor;

		if (!empty($attributeLabels))
		{
			foreach($attributeLabels as $id => $label)
			{
				$this->customAttributes[] = new CustomAttributeResponse($server, $id, $label, $user->GetAttributeValue($id));
			}
		}

		$this->AddService($server, WebServices::GetUser, array(WebServiceParams::UserId => $userId));
	}

	public static function Example()
	{
		return new ExampleUserItemResponse();
	}
}

class ExampleUserItemResponse extends UserItemResponse
{
	public function __construct()
	{
		$date = Date::Now()->ToIso();
		$this->id = 1;
		$this->dateCreated = $date;
		$this->emailAddress = 'email@address.com';
		$this->firstName = 'first';
		$this->lastName = 'last';
		$this->language = 'language_code';
		$this->lastLogin = $date;
		$this->organization = 'organization';
        $this->phoneCountryCode = 'US';
		$this->phoneNumber = '123-456-7890';
		$this->statusId = 'statusId';
		$this->timezone = 'timezone';
		$this->userName = 'username';
		$this->position = 'position';
		$this->customAttributes = array(CustomAttributeResponse::Example());
		$this->currentCredits = '2.50';
		$this->reservationColor = '#000000';
	}
}