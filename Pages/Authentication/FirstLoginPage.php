<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/SecurePage.php');

class FirstLoginPage extends SecurePage
{
    public function __construct()
    {
        parent::__construct('FirstLogin');
    }

    public function PageLoad()
    {
        $user = $this->server->GetUserSession();
        $isAdmin = $user->IsAdmin;
        $isGroupAdmin = $user->IsGroupAdmin;
        $isResourceAdmin = $user->IsResourceAdmin;
        $isScheduleAdmin = $user->IsScheduleAdmin;
;
        $showUser = !$isAdmin && !$isGroupAdmin && !$isResourceAdmin && !$isScheduleAdmin;
        $showAdmin = $isAdmin || $isGroupAdmin;
        $this->Set('FirstShowUser', $showUser);
        $this->Set('FirstShowAdmin', $showAdmin);
        $this->Set('FirstShowAdmin', $showAdmin);
        $this->Set('FirstShowGroupAdmin', $isGroupAdmin);
        $this->Set('FirstShowResourceAdmin', $isResourceAdmin);
        $this->Set('FirstShowScheduleAdmin', $isScheduleAdmin);
        $defaultPageId = Configuration::Instance()->GetKey(ConfigKeys::DEFAULT_HOMEPAGE);
        $url = Pages::UrlFromId($defaultPageId);
        $this->Set("FirstHomepageUrl", $url ?? Configuration::Instance()->GetScriptUrl());

        $user->IsFirstLogin = false;
        $this->server->SetUserSession($user);
        $this->Display('Authentication/first-login.tpl');
    }
}
