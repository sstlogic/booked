<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'Pages/Authentication/ResetPasswordPage.php');

class ResetPasswordActions
{
    const Reset = 'reset';
}

class ResetPasswordPresenter extends ActionPresenter
{
    /**
     * @var IResetPasswordPage
     */
    private $page;
    /**
     * @var IUserRepository
     */
    private $userRepository;

    public function __construct(IResetPasswordPage $page, IUserRepository $userRepository)
    {
        parent::__construct($page);
        $this->page = $page;
        $this->userRepository = $userRepository;

        $this->AddAction(ResetPasswordActions::Reset, 'ResetPassword');
    }

    public function PageLoad()
    {
        $token = $this->page->GetToken();
        $request = $this->userRepository->GetPasswordReset($token);

        $this->page->ShowError($request == null || $request->IsExpired());

        if ($request != null && $request->IsExpired()) {
            $this->userRepository->DeletePasswordRequest($request->UserId());
        }
    }

    public function ResetPassword()
    {
        $token = $this->page->GetToken();
        $newPassword = $this->page->GetPassword();
        $request = $this->userRepository->GetPasswordReset($token);

        $password = new Password();
        $encrypted = $password->Encrypt($newPassword);

        $user = $this->userRepository->LoadById($request->UserId());

        $user->ChangePassword($encrypted);
        $user->ClearSecuritySettings();
        $this->userRepository->Update($user);
        $this->userRepository->DeletePasswordRequest($request->UserId());

        $this->page->ShowResetSuccess(true);
    }

    protected function LoadValidators($action)
    {
        if ($action == ResetPasswordActions::Reset) {
            $this->page->RegisterValidator('password', new PasswordComplexityValidator($this->page->GetPassword()));
            $this->page->RegisterValidator('expired', new ResetPasswordExpirationValidator($this->userRepository, $this->page->GetToken()));
        }
    }
}

class ResetPasswordExpirationValidator extends ValidatorBase
{

    /**
     * @var IUserRepository
     */
    private $userRepository;
    private $token;

    public function __construct(IUserRepository $userRepository, $token)
    {

        $this->userRepository = $userRepository;
        $this->token = $token;
    }

    public function Validate()
    {
        $request = $this->userRepository->GetPasswordReset($this->token);

        if ($request != null && $request->IsExpired()) {
            $this->isValid = false;
        }
    }
}