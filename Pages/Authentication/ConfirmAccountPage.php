<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/SecurePage.php');
require_once(ROOT_DIR . 'Presenters/Authentication/ConfirmAccountPresenter.php');

interface IConfirmAccountPage extends IActionPage, ILoginBasePage
{
    /**
     * @param bool $expired
     */
    public function SetIsExpired($expired);

    /**
     * @param Date $expirationDate
     */
    public function SetExpirationDate(Date $expirationDate);

    /**
     * @return string
     */
    public function GetOtp();

    /**
     * @param array $response
     */
    public function SetConfirmResponse(array $response);

    /**
     * @param string $email
     */
    public function SetMaskedEmail($email);
}

class ConfirmAccountPage extends ActionPage implements IConfirmAccountPage
{
    /**
     * @var ConfirmAccountPresenter
     */
    private $presenter;

    public function __construct()
    {
        parent::__construct('ConfirmAccount', 1);
        $this->presenter = new ConfirmAccountPresenter($this, new UserRepository(), MultiFactorAuthentication::Create());
    }

    public function ProcessAction()
    {
        $this->presenter->ProcessAction();
    }

    public function ProcessDataRequest($dataRequest)
    {
        $this->presenter->ProcessAction();
    }

    public function ProcessPageLoad()
    {
        $resumeUrl = self::CleanRedirect($this->server->GetQuerystring(QueryStringKeys::REDIRECT));
        $resumeUrl = str_replace('&amp;&amp;', '&amp;', $resumeUrl);
        $this->Set('ResumeUrl', $resumeUrl);
        $this->presenter->PageLoad($this->server->GetUserSession());
        $this->Display('Authentication/confirm-account.tpl');
    }

    public function SetIsExpired($expired)
    {
        $this->Set("IsExpired", $expired);
    }

    public function GetOtp()
    {
        return $this->GetForm(FormKeys::OTP);
    }

    public function SetExpirationDate(Date $expirationDate)
    {
        $this->Set("ExpirationDate", $expirationDate);
    }

    public function GetResumeUrl()
    {
        $resumeUrl = $this->GetForm(FormKeys::RESUME);
        if (empty($resumeUrl)) {
            return self::CleanRedirect($this->GetQuerystring(QueryStringKeys::REDIRECT));
        } else {
            return $this->GetForm(FormKeys::RESUME);
        }
    }

    public function SetConfirmResponse(array $response)
    {
        $this->SetJsonResponse($response);
    }

    public function SetMaskedEmail($email)
    {
        $this->Set("MaskedEmail", $email);
    }
}