<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/WebService/namespace.php');

class LanguageItemResponse
{
    public $code;
    public $name;
}

class LanguageResponse extends RestResponse
{
    /**
     * @var array|LanguageItemResponse[]
     */
    public $languages = array();

    public function __construct()
    {
        foreach (Resources::GetInstance()->AvailableLanguages as $l) {
            $r = new LanguageItemResponse();
            $r->code = $l->LanguageCode;
            $r->name = $l->DisplayName;
            $this->languages[] = $r;
        }
    }
}