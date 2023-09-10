<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

class FullName
{
    /**
     * @var string
     */
    private $fullName;

    public function __construct($firstName, $lastName)
    {
        $formatter = Configuration::Instance()->GetKey(ConfigKeys::NAME_FORMAT);
        if (empty($formatter)) {
            $this->fullName = $firstName . ' ' . $lastName;
        } else {
            $this->fullName = str_replace('{first}', $firstName, $formatter);
            $this->fullName = str_replace('{last}', $lastName, $this->fullName);
        }
    }

    public function __toString()
    {
        return $this->fullName;
    }

    /**
     * @param $firstName string
     * @param $lastName string
     * @return string
     */
    public static function AsString($firstName, $lastName)
    {
        $full = new FullName($firstName, $lastName);
        return $full->__toString();
    }
}