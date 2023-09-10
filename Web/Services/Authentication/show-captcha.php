<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../../../');
require_once(ROOT_DIR . 'vendor/autoload.php');
use Gregwar\Captcha\CaptchaBuilder;
require_once(ROOT_DIR . 'lib/Common/namespace.php');

try
{
    $builder = new CaptchaBuilder();
    $builder->build(250, 60);
    $captcha = $builder->getPhrase();
    ServiceLocator::GetServer()->SetSession('captcha', $captcha);
    header('Content-type: image/jpeg');
    $builder->output();
}
catch (Exception $ex)
{
	Log::Error('Error showing captcha image', ['exception' => $ex]);
}


