<?php
/**
 * Copyright 2019-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/WebService/namespace.php');

class AccountResponse extends RestResponse
{
    public $userId;
    public $firstName;
    public $lastName;
    public $emailAddress;
    public $userName;
    public $language;
    public $timezone;
    public $phone;
    public $organization;
    public $position;
    /** @var array|CustomAttributeResponse[] */
    public $customAttributes = array();
    public $icsUrl;

    public function __construct(IRestServer $server, User $user, IEntityAttributeList $attributeList)
    {
        $this->userId = $user->Id();
        $this->firstName = apidecode($user->FirstName());
        $this->lastName = apidecode($user->LastName());
        $this->userName = apidecode($user->Username());
        $this->phone = apidecode($user->GetAttribute(UserAttribute::Phone));
        $this->organization = apidecode($user->GetAttribute(UserAttribute::Organization));
        $this->position = apidecode($user->GetAttribute(UserAttribute::Position));
        $this->timezone = $user->Timezone();
        $this->language = $user->Language();
        $this->emailAddress = apidecode($user->EmailAddress());
        $attributeValues = apidecode($attributeList->GetAttributes($this->userId));

        $i = 0;
        foreach ($attributeValues as $av) {
            $this->customAttributes[] = new CustomAttributeResponse($server, $av->Id(), $av->Label(), $av->Value());
            $i++;
        }

        if ($user->GetIsCalendarSubscriptionAllowed()) {
            $url = new CalendarSubscriptionUrl($user->GetPublicId(), null, null);
            $this->icsUrl = $url->__toString();
        }

        $this->AddService($server, WebServices::GetAccount, array(WebServiceParams::UserId, $this->userId));
        $this->AddService($server, WebServices::UpdateAccount, array(WebServiceParams::UserId, $this->userId));
        $this->AddService($server, WebServices::UpdatePassword, array(WebServiceParams::UserId, $this->userId));
    }

    public static function Example()
    {
        return new ExampleAccountResponse();
    }
}

class ExampleAccountResponse extends AccountResponse
{
    public function __construct()
    {
        $this->emailAddress = 'email@address.com';
        $this->timezone = Configuration::Instance()->GetDefaultTimezone();
        $this->language = Configuration::Instance()->GetKey(ConfigKeys::LANGUAGE);
        $this->userId = 1;
        $this->position = 'position';
        $this->organization = 'organization';
        $this->phone = 'phone';
        $this->lastName = 'last';
        $this->firstName = 'first';
        $this->userName = 'username';
        $this->customAttributes = array(CustomAttributeResponse::Example());
        $this->icsUrl = 'webcal://path-to-calendar';
    }
}
