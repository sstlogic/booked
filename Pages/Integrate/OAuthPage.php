<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/Page.php');
require_once(ROOT_DIR . 'Presenters/Integrate/OAuthPresenter.php');

interface IOAuthPage extends IPage
{
    public function GetCode(): ?string;

    public function GetState(): ?string;

    /**
     * @param array|string[] $errorMessages
     */
    public function ShowError(array $errorMessages);

    public function GetResumeUrl(): ?string;

    public function GetLaunchProviderId(): ?string;
}

class OAuthPage extends Page implements IOAuthPage
{
    private OAuthPresenter $presenter;

    public function __construct()
    {
        parent::__construct('Login', 1);
        $this->presenter = new OAuthPresenter($this, new WebAuthentication(PluginManager::Instance()->LoadAuthentication()), new Registration(), new OAuthRepository());
    }

    public function PageLoad()
    {
        $this->Set('ErrorMessages', []);
        $this->Set('IsError', false);

        $this->presenter->PageLoad();
        $this->Display('Authentication/oauth.tpl');
    }

    public function Launch()
    {
        $this->presenter->Launch();
    }

    public function GetCode(): ?string
    {
        return $this->GetQuerystring('code');
    }

    public function GetState(): ?string
    {
        return $this->GetQuerystring('state');
    }

    public function ShowError(array $errorMessages)
    {
        $this->Set('ErrorMessages', $errorMessages);
        $this->Set('IsError', true);
    }

    public function GetResumeUrl(): ?string
    {
        return $this->GetQuerystring(QueryStringKeys::REDIRECT);
    }

    public function GetLaunchProviderId(): ?string
    {
        return $this->GetQuerystring(QueryStringKeys::PUBLIC_ID);
    }
}