<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/Admin/AdminPage.php');
require_once(ROOT_DIR . 'Pages/ActionPage.php');
require_once(ROOT_DIR . 'Presenters/Admin/ManageOAuthPresenter.php');

interface IManageOAuthPage extends IActionPage
{
}

class ManageOAuthPage extends ActionPage implements IManageOAuthPage
{
    private ManageOAuthPresenter $presenter;

    public function __construct()
    {
        parent::__construct('ManageOAuth', 2);
        $this->presenter = new ManageOAuthPresenter($this, new OAuthRepository());
    }

    public function ProcessAction()
    {
        // no-op
    }

    public function ProcessDataRequest($dataRequest)
    {
        // no-op
    }

    public function ProcessApiCall($json)
    {
        $this->presenter->ProcessApi($json);
    }

    public function ProcessPageLoad()
    {
        $this->Display('Admin/OAuth/manage-oauth-spa.tpl');
    }
}