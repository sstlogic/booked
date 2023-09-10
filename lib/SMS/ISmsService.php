<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

interface ISmsService
{
    public function GetOneTimeCode(): string;

    public function Send(SmsMessage $message): SmsSendResult;

    public function GetStatus(): SmsServiceStatus;

    public function IsEnabled(): bool;
}