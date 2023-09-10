<?php

/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

class RequiredEmailDomainValidator extends ValidatorBase implements IValidator
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function Validate()
    {
        $this->isValid = true;

        $domains = Configuration::Instance()->GetSectionKey(ConfigSection::AUTHENTICATION, ConfigKeys::AUTHENTICATION_REQUIRED_EMAIL_DOMAINS);

        if (empty($domains)) {
            return;
        }

        $allDomains = preg_split('/[\,\s;]/', $domains);

        $trimmed = strtolower(trim($this->value));

        foreach ($allDomains as $d) {
            $d = strtolower(str_replace('@', '', trim($d)));
            if (BookedStringHelper::EndsWith($trimmed, '@' . $d)) {
                return;
            }
        }

        $this->isValid = false;
    }

    public static function IsEmailAddressValid(string $email): bool
    {
        $validator = new RequiredEmailDomainValidator($email);
        $validator->Validate();
        return $validator->IsValid();
    }
}