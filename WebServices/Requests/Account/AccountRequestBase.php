<?php
/**
 * Copyright 2019-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/WebService/JsonRequest.php');

abstract class AccountRequestBase extends JsonRequest
{
    public $firstName;
    public $lastName;
    public $emailAddress;
    public $userName;
    public $language;
    public $timezone;
    public $phone;
    public $phoneCountryCode;
    public $organization;
    public $position;
    /** @var array|AttributeValueRequest[] */
    public $customAttributes = array();

    /**
     * @return string
     */
    public function GetTimezone()
    {
        if (empty($this->timezone)) {
            return Configuration::Instance()->GetDefaultTimezone();
        }

        return $this->timezone;
    }

    /**
     * @return string
     */
    public function GetLanguage()
    {
        if (empty($this->language)) {
            return Configuration::Instance()->GetKey(ConfigKeys::LANGUAGE);
        }

        return $this->language;
    }

    /**
     * @return array|AttributeValueRequest[]
     */
    public function GetCustomAttributes()
    {
        if (!empty($this->customAttributes)) {
            return $this->customAttributes;
        }
        return array();
    }

    /**
     * @return array
     */
    public function GetAdditionalFields()
    {
        $phoneCountryCode = CountryCodes::Get($this->phoneCountryCode, $this->phone, $this->GetLanguage());

        return [
            UserAttribute::Phone => apiencode($this->phone),
            UserAttribute::Organization => apiencode($this->organization),
            UserAttribute::Position => apiencode($this->position),
            UserAttribute::PhoneCountryCode => $phoneCountryCode->code,
        ];
    }
}