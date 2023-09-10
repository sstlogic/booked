<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Config/namespace.php');
require_once(ROOT_DIR . 'lib/Common/namespace.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Email/Messages/ForgotPasswordEmail.php');

class ForgotPwdPresenter
{
    /**
     * @var IForgotPwdPage
     */
    private $_page = null;

    public function __construct(IForgotPwdPage $page)
    {
        $this->_page = $page;
    }

    public function PageLoad()
    {
        if (Configuration::Instance()->GetKey(ConfigKeys::DISABLE_PASSWORD_RESET, new BooleanConverter())
            || !PluginManager::Instance()->LoadAuthentication()->ShowForgotPasswordPrompt()) {
            $this->_page->SetEnabled(false);
            return;
        }

        if ($this->_page->ResetClicked()) {
            $this->SendResetEmail();
            $this->_page->ShowResetEmailSent(true);
        }
    }

    public function SendResetEmail()
    {
        $emailAddress = $this->_page->GetEmailAddress();

        $addr = $_SERVER['REMOTE_ADDR'] ?? '';
        $host = $_SERVER['REMOTE_HOST'] ?? '';
        Log::Debug('Password reset request', ['emailAddress' => $emailAddress, 'remoteAddress' => $addr, 'remoteHost' => $host]);

        $userRepository = new UserRepository();
        $user = $userRepository->FindByEmail($emailAddress);

        if ($user != null) {
            $resetToken = BookedStringHelper::Random();

            $userRepository->AddPasswordReset($user->Id(), $resetToken);

            $emailMessage = new ForgotPasswordEmail($user, $resetToken);
            ServiceLocator::GetEmailService()->Send($emailMessage);
        }
    }
}