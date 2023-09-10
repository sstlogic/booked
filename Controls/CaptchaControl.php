<?php
/**
 * Copyright 2013-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Controls/Control.php');
require_once(ROOT_DIR . 'lib/Common/namespace.php');

class CaptchaControl extends Control
{
    public function PageLoad()
    {
        if (Configuration::Instance()->GetSectionKey(ConfigSection::RECAPTCHA, ConfigKeys::RECAPTCHA_ENABLED,
            new BooleanConverter())
        ) {
            $this->showRecaptcha();
        } else {
            $this->showNative();
        }
    }

    private function showRecaptcha()
    {
        Log::Debug('CaptchaControl using Recaptcha');

        $publicKey = Configuration::Instance()->GetSectionKey(ConfigSection::RECAPTCHA, ConfigKeys::RECAPTCHA_PUBLIC_KEY);
        $version = Configuration::Instance()->GetSectionKey(ConfigSection::RECAPTCHA, ConfigKeys::RECAPTCHA_VERSION, new IntConverter());

        if (empty($version) || $version == 2) {
            $response = '<script src=\'https://www.google.com/recaptcha/api.js?\'></script>';
            $response .= '<div class="g-recaptcha" data-sitekey="' . $publicKey . '"></div>';

        } else {
            $response = '<script src=\'https://www.google.com/recaptcha/api.js?render=' . $publicKey . '\'></script>';
            $response .= "<script>
                    $(document).ready(function () {
                        grecaptcha.ready(function() {
                              grecaptcha.execute('$publicKey', {action: 'submit'}).then(function(token) {
                                $('form').prepend('<input type=\"hidden\" name=\"g-recaptcha-response\" value=\"' + token + '\">');
                              });
                            });
                     });
                    </script>";
        }

        echo $response;
    }

    private function showNative()
    {
        Log::Debug('CaptchaControl using native captcha');
        $url = CaptchaService::Create()->GetImageUrl();

        $label = Resources::GetInstance()->GetString('SecurityCode');
        $message = Resources::GetInstance()->GetString('Required');
        $formName = FormKeys::CAPTCHA;

        echo "<div id=\"captchaDiv\">
                <div><img src=\"$url\" alt=\"captcha\" id=\"captchaImg\"/></div>
		        <label for=\"captchaValue\">$label *</label>
                <input type=\"text\" class=\"form-control\" name=\"$formName\" id=\"$formName\" 
                required=\"required\"/>
                <div class=\"invalid-feedback\">
                                            $message
                						</div>
            </div>";
    }
}