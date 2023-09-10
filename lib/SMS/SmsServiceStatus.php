<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

class SmsServiceStatus
{
    private $allowedMessagesPerMonth;
    private $sentMessagesThisMonth;
    private $remainingMessages;

    public function __construct(int $allowedMessagesPerMonth, int $sentMessagesThisMonth, int $remainingMessages)
    {

        $this->allowedMessagesPerMonth = $allowedMessagesPerMonth;
        $this->sentMessagesThisMonth = $sentMessagesThisMonth;
        $this->remainingMessages = $remainingMessages;
    }

    public function GetAllowedMessagesPerMonth(): int
    {
        return $this->allowedMessagesPerMonth;
    }

    public function GetSentMessagesThisMonth():int
    {
        return $this->sentMessagesThisMonth;
    }

    public function GetRemainingMessages(): int
    {
        return $this->remainingMessages;
    }
}