<?php
/**
 * Copyright 2019-2023 Twinkle Toes Software, LLC
 */

require_once (ROOT_DIR . 'WebServices/Requests/Account/AccountRequestBase.php');

class UpdateAccountRequest extends AccountRequestBase
{
    public static function Example()
    {
        $request = new UpdateAccountRequest();
        $request->firstName = 'FirstName';
        $request->lastName = 'LastName';
        $request->emailAddress = 'email@address.com';
        $request->userName = 'username';
        $request->timezone = Configuration::Instance()->GetDefaultTimezone();
        $request->language = Configuration::Instance()->GetKey(ConfigKeys::LANGUAGE);
        $request->organization = 'organization';
        $request->phone = 'phone';
        $request->phoneCountryCode = 'US';
        $request->position = 'position';
        $request->customAttributes = array(AttributeValueRequest::Example());

        return $request;
    }
}