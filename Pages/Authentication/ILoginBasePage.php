<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

interface ILoginRedirectorPage
{
    public function GetResumeUrl();

    public function Redirect($url);
}

interface ILoginBasePage extends IPage, ILoginRedirectorPage
{
}

