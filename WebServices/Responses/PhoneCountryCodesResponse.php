<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/WebService/namespace.php');
require_once(ROOT_DIR . 'Domain/Values/CountryCodes.php');

class CountryCodeItemResponse
{
    public $code;
    public $name;
    public $prefix;
}

class PhoneCountryCodesResponse extends RestResponse
{
    /**
     * @var array|CountryCodeItemResponse[]
     */
    public $codes = array();

    public function __construct()
    {
        foreach (CountryCodes::All() as $l) {
            $r = new CountryCodeItemResponse();
            $r->code = $l->code;
            $r->name = $l->name;
            $r->prefix = $l->phone;
            $this->codes[] = $r;
        }
    }
}