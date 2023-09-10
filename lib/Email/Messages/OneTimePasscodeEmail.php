<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Email/namespace.php');
require_once(ROOT_DIR . 'Pages/Pages.php');

class OneTimePasscodeEmail extends EmailMessage
{
    /**
     * @var User
     */
    private $user;
    /**
     * @var string
     */
    private $otp;
    /**
     * @var Date
     */
    private $expirationDate;

    /**
     * @param User $language
     * @param string $otp
     * @param Date $expirationDate
     */
    public function __construct(User $user, $otp, $expirationDate)
    {
        parent::__construct($user->Language());
        $this->user = $user;
        $this->otp = $otp;
        $this->expirationDate = $expirationDate;
    }

    public function To()
    {
        return new EmailAddress($this->user->EmailAddress(), $this->user->FullName());
    }

    /**
     * @return string
     */
    public function Subject()
    {
        return $this->Translate('OneTimePasscodeSubject', [Configuration::Instance()->GetKey(ConfigKeys::APP_TITLE)]);
    }

    /**
     * @return string
     */
    public function Body()
    {
        $format = Resources::GetInstance()->GetDateFormat("reservation_email", $this->user->DateFormat(), $this->user->TimeFormat());
        $this->Set('dateFormat', $format);

        $this->Set('FirstName', $this->user->FirstName());
        $this->Set('OTP', $this->otp);
        $this->Set('OtpUrl', sprintf("%s/auth/confirm-account.php", Configuration::Instance()->GetScriptUrl()));
        $this->Set('ExpirationDate', $this->expirationDate);
        return $this->FetchTemplate('OneTimePasscodeEmail.tpl');
    }
}
