<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'Presenters/Authentication/LoginRedirector.php');
require_once(ROOT_DIR . 'lib/Application/Authentication/namespace.php');

class ConfirmAccountActions
{
    const Confirm = "Confirm";
    const Resend = "Resend";
}

class ConfirmAccountPresenter extends ActionPresenter
{
    /**
     * @var IConfirmAccountPage
     */
    private $page;
    /**
     * @var IUserRepository
     */
    private $userRepository;
    /**
     * @var IMultiFactorAuthentication
     */
    private $mfa;

    public function __construct(IConfirmAccountPage $page, IUserRepository $userRepository, IMultiFactorAuthentication $mfa)
    {
        parent::__construct($page);
        $this->page = $page;
        $this->userRepository = $userRepository;
        $this->mfa = $mfa;

        $this->AddAction(ConfirmAccountActions::Confirm, "ConfirmOtp");
        $this->AddAction(ConfirmAccountActions::Resend, "GenerateNewOtp");
    }

    public function PageLoad(UserSession $userSession)
    {
        $mfa = $this->userRepository->GetMultiFactorSettings($userSession->UserId);
        $this->page->SetIsExpired($mfa->IsExpired());
        $this->page->SetExpirationDate($mfa->ExpirationDate());
        $this->page->SetMaskedEmail(MaskedEmail::Mask($userSession->Email));
    }

    public function ConfirmOtp()
    {
        Log::Debug('Confirming OTP');
        $userSession = ServiceLocator::GetServer()->GetUserSession();

        $otp = $this->page->GetOtp();
        $mfa = $this->userRepository->GetMultiFactorSettings($userSession->UserId);

        if (!$mfa->IsExpired() && $mfa->Otp() == trim($otp)) {
            $userSession->IsAwaitingMultiFactorAuth = false;
            $this->userRepository->UpdateMultiFactorSettings(new UserMultiFactorAuthenticationSettings($userSession->UserId, null, NullDate::Instance()));
            ServiceLocator::GetServer()->SetUserSession($userSession);
            ServiceLocator::GetServer()->SetCookie(new Cookie(CookieKeys::LOGIN_TOKEN, $userSession->LoginToken, null, null, true));
            $this->page->SetConfirmResponse(['resumeUrl' => LoginRedirector::GetRedirectUrl($this->page, $userSession), 'success' => true]);
            Log::Debug('OTP successfully confirmed');
        } else {
            $this->page->SetConfirmResponse(['resumeUrl' => null, 'success' => false]);
            Log::Debug('OTP not valid or expired');
        }
    }

    public function GenerateNewOtp()
    {
        Log::Debug('Generating new OTP');
        $userSession = ServiceLocator::GetServer()->GetUserSession();

        $user = $this->userRepository->LoadById($userSession->UserId);
        $this->mfa->GenerateAndSendOtp($user);
        $userSession->IsAwaitingMultiFactorAuth = true;
        ServiceLocator::GetServer()->SetUserSession($userSession);
    }

    protected function LoadValidators($action)
    {
        if ($action == ConfirmAccountActions::Confirm) {
            $this->page->RegisterValidator('confirmationCode', new OneTimePasscodeValidator($this->page->GetOtp(), ServiceLocator::GetServer()->GetUserSession(), $this->userRepository));
        }
    }
}

class OneTimePasscodeValidator extends ValidatorBase implements IValidator
{
    /**
     * @var string
     */
    private $otpCode;
    /**
     * @var IUserRepository
     */
    private $userRepository;
    /**
     * @var int
     */
    private $userId;

    /**
     * @param string $otpCode
     * @param UserSession $userSession
     * @param IUserRepository $userRepository
     */
    public function __construct($otpCode, UserSession $userSession, IUserRepository $userRepository)
    {
        $this->otpCode = $otpCode;
        $this->userRepository = $userRepository;
        $this->userId = $userSession->UserId;
    }

    public function Validate()
    {
        $mfa = $this->userRepository->GetMultiFactorSettings($this->userId);
        $this->isValid = true;

        if ($mfa->IsExpired() || $mfa->Otp() != trim($this->otpCode)) {
            $this->isValid = false;
            $this->AddMessage(Resources::GetInstance()->GetString('InvalidOtp'));
        }
    }
}