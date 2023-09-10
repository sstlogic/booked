<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

class SmsSendResult
{
    private $isSuccess;
    private $remainingMessages;

    public function __construct(bool $isSuccess, int $remainingMessages)
    {
        $this->isSuccess = $isSuccess;
        $this->remainingMessages = $remainingMessages;
    }

    public function IsSuccess()
    {
        return $this->isSuccess;
    }

    public function RemainingMessages()
    {
        return $this->remainingMessages;
    }
}