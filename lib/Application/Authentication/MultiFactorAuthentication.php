<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Email/Messages/OneTimePasscodeEmail.php');

abstract class MultiFactorAuthentication implements IMultiFactorAuthentication
{
    const Email = 'email';
    /**
     * @var IUserRepository
     */
    protected $userRepository;
    /**
     * @var IOtpGenerator
     */
    private $otpGenerator;

    protected function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public static function Create()
    {
        $mfaType = Configuration::Instance()->GetSectionKey(ConfigSection::MFA, ConfigKeys::MFA_TYPE);
        if ($mfaType == MultiFactorAuthentication::Email) {
            return new EmailMultiFactorAuthentication(new UserRepository());
        }
        return new NullMultiFactorAuthentication();
    }

    public function Enforce(User $user, $token)
    {
        if (!$user->IsLoginTokenValid($token)) {
            Log::Debug("Device not recognized. Requiring OTP");
            $this->GenerateAndSendOtp($user);
            return true;
        }

        return false;
    }

    public function GenerateAndSendOtp(User $user)
    {
        $otp = $this->GetOtpGenerator()->Generate();
        $mfaSettings = new UserMultiFactorAuthenticationSettings($user->Id(), $otp, Date::Now());
        $this->userRepository->UpdateMultiFactorSettings($mfaSettings);
        ServiceLocator::GetEmailService()->Send(new OneTimePasscodeEmail($user, $otp, $mfaSettings->ExpirationDate()));
    }

    /**
     * @param IOtpGenerator $otpGenerator
     */
    public function SetOtpGenerator($otpGenerator)
    {
        $this->otpGenerator = $otpGenerator;
    }

    /**
     * @return IOtpGenerator
     */
    protected function GetOtpGenerator()
    {
        if (empty($this->otpGenerator)) {
            $this->otpGenerator = new OtpGenerator();
        }

        return $this->otpGenerator;
    }
}

class NullMultiFactorAuthentication extends MultiFactorAuthentication
{
    public function __construct()
    {
        // no-op
    }

    public function Enforce(User $user, $token)
    {
        return false;
    }

    public function GenerateAndSendOtp(User $user)
    {
        // no-op
    }
}

class EmailMultiFactorAuthentication extends MultiFactorAuthentication
{
    public function __construct(IUserRepository $userRepository)
    {
        parent::__construct($userRepository);
    }
}