<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

interface IRequestedResourcePage
{
    public function GetRequestedResourceId();

    public function GetRequestedResourcePublicId();

    public function GetRequestedScheduleId();
}