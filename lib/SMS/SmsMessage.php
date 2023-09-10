<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

class SmsMessage
{
    private $phoneNumber;
    private $messageBody;

    public function __construct(string $phoneNumber, string $messageBody)
    {
        $this->phoneNumber =  preg_replace('/[^0-9]/', '', $phoneNumber . '');
        $this->messageBody = $messageBody;
    }

    public function GetPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function GetMessage(): string
    {
        return $this->messageBody;
    }
}