<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/ActionPage.php');
require_once(ROOT_DIR . 'Presenters/Authentication/ResetPasswordPresenter.php');
require_once(ROOT_DIR . 'lib/Config/namespace.php');
require_once(ROOT_DIR . 'lib/Common/namespace.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'Domain/Values/PasswordResetRequest.php');

interface IResetPasswordPage extends IActionPage
{

    /**
     * @return string
     */
    public function GetToken();

    /**
     * @param boolean $showError
     */
    public function ShowError($showError);

    /**
     * @return string
     */
    public function GetPassword();

    public function ShowResetSuccess($wasSuccess);
}

class ResetPasswordPage extends ActionPage implements IResetPasswordPage
{
    /**
     * @var ResetPasswordPresenter
     */
    private $presenter;

    public function __construct()
    {
        parent::__construct('ResetPassword');
        $this->presenter = new ResetPasswordPresenter($this, new UserRepository());
    }

    public function ProcessAction()
    {
        $this->presenter->ProcessAction();;
    }

    public function ProcessDataRequest($dataRequest)
    {
        // no-op
    }

    public function ProcessPageLoad()
    {
        $this->presenter->PageLoad();
        $this->Set('Token', $this->GetToken());
        $this->Set('PasswordLetters', Configuration::Instance()->GetSectionKey(ConfigSection::PASSWORD, ConfigKeys::PASSWORD_LETTERS));
        $this->Set('PasswordNumbers', Configuration::Instance()->GetSectionKey(ConfigSection::PASSWORD, ConfigKeys::PASSWORD_NUMBERS));
        $this->Display('Authentication/reset-password.tpl');
    }

    public function GetToken()
    {
        $qs = $this->GetQuerystring(QueryStringKeys::RESET_TOKEN);
        if (!empty($qs)) {
            return $qs;
        }

        return $this->GetForm(FormKeys::RESET_TOKEN);
    }

    public function ShowError($showError)
    {
        $this->Set('ShowError', $showError);
    }

    public function GetPassword()
    {
        return $this->GetForm(FormKeys::PASSWORD);
    }

    public function ShowResetSuccess($wasSuccess)
    {
       $this->SetJsonResponse(['success' => $wasSuccess]);
    }
}