<?php
/**
 * Copyright 2022 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Email/namespace.php');
require_once(ROOT_DIR . 'Domain/namespace.php');

class PasswordUpdatedByUserEmail extends EmailMessage
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        parent::__construct($user->Language());
    }

    public function To()
    {
        return new EmailAddress($this->user->EmailAddress(), $this->user->FullName());
    }

    public function Subject()
    {
        $this->Translate('PasswordUpdatedByUserSubject', [Configuration::Instance()->GetKey(ConfigKeys::APP_TITLE)]);
    }

    public function Body()
    {
        $this->Set('FirstName', $this->user->FirstName());
        $this->Set('AppTitle', Configuration::Instance()->GetKey(ConfigKeys::APP_TITLE));
        return $this->FetchTemplate('PasswordUpdatedByUserEmail.tpl');
    }
}