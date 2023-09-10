<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

interface ICaptchaService
{
	/**
	 * @return string
	 */
	public function GetImageUrl();

	/**
	 * @param string $captchaValue
	 * @return bool
	 */
	public function IsCorrect($captchaValue);
}

class NullCaptchaService implements ICaptchaService
{
	/**
	 * @return string
	 */
	public function GetImageUrl()
	{
		return '';
	}

	/**
	 * @param string $captchaValue
	 * @return bool
	 */
	public function IsCorrect($captchaValue)
	{
		return true;
	}
}

class CaptchaService implements ICaptchaService
{
	private function __construct()
	{

	}

	public function GetImageUrl()
	{
		$url = new Url(Configuration::Instance()->GetScriptUrl() . '/Services/Authentication/show-captcha.php');
		$url->AddQueryString('show', 'true');
		return $url->__toString();
	}

	public function IsCorrect($captchaValue)
	{
        $expectedCaptcha = ServiceLocator::GetServer()->GetSession('captcha');
		$isValid = strtolower($expectedCaptcha) == strtolower($captchaValue);

		Log::Debug('Checking captcha value', ['captchaValue' => $captchaValue, 'expectedCaptcha' => $expectedCaptcha, 'isValid' => (int)$isValid]);

		return $isValid;
	}

	/**
	 * @static
	 * @return ICaptchaService
	 */
	public static function Create()
	{
		if (Configuration::Instance()->GetKey(ConfigKeys::REGISTRATION_ENABLE_CAPTCHA, new BooleanConverter()) ||
            (Configuration::Instance()->GetSectionKey(ConfigSection::AUTHENTICATION, ConfigKeys::AUTHENTICATION_CAPTCHA_ON_LOGIN, new BooleanConverter()))
        )
		{
			if (Configuration::Instance()->GetSectionKey(ConfigSection::RECAPTCHA, ConfigKeys::RECAPTCHA_ENABLED,
														 new BooleanConverter())
			)
			{
				return new ReCaptchaService();
			}
			return new CaptchaService();
		}

		return new NullCaptchaService();
	}
}

class ReCaptchaService implements ICaptchaService
{
	/**
	 * @return string
	 */
	public function GetImageUrl()
	{
		return '';
	}

	/**
	 * @param string $captchaValue
	 * @return bool
	 */
	public function IsCorrect($captchaValue)
	{
		$server = ServiceLocator::GetServer();

        require_once(ROOT_DIR . 'vendor/autoload.php');
		$privatekey = Configuration::Instance()->GetSectionKey(ConfigSection::RECAPTCHA, ConfigKeys::RECAPTCHA_PRIVATE_KEY);

        $recap = new \ReCaptcha\ReCaptcha($privatekey);
        $resp = $recap->setExpectedHostname(ServiceLocator::GetServer()->GetHost())
            ->verify($server->GetForm('g-recaptcha-response'), $server->GetRemoteAddress());

        if (!$resp->isSuccess())
        {
            Log::Error('ReCaptcha is invalid', ['errors' => $resp->getErrorCodes()]);
        }

		return $resp->isSuccess();
	}
}
