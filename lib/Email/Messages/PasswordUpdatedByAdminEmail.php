<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */


require_once(ROOT_DIR . 'lib/Email/namespace.php');
require_once(ROOT_DIR . 'Domain/namespace.php');

class PasswordUpdatedByAdminEmail extends EmailMessage
{
    /**
     * @var User
     */
    private $user;
    /**
     * @var string
     */
    private $password;

    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
        parent::__construct($user->Language());
    }

    public function To()
    {
        return new EmailAddress($this->user->EmailAddress(), $this->user->FullName());
    }

    public function Subject()
    {
        return $this->Translate('PasswordUpdatedByAdminSubject', [Configuration::Instance()->GetKey(ConfigKeys::APP_TITLE)]);
    }

    public function Body()
    {
        $this->Set('FirstName', $this->user->FirstName());
        $this->Set('NewPassword', $this->password);
        $this->Set('AppTitle',Configuration::Instance()->GetKey(ConfigKeys::APP_TITLE));
        return $this->FetchTemplate('PasswordUpdatedByAdminEmail.tpl');
    }
}
