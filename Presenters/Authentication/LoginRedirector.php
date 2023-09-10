<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/Authentication/ILoginBasePage.php');

class LoginRedirector
{
    public static function Redirect(ILoginRedirectorPage $page, UserSession $user)
    {
        $page->Redirect(self::GetRedirectUrl($page, $user));
    }

    /**
     * @param ILoginBasePage $page
     * @return string
     */
    public static function GetRedirectUrl(ILoginRedirectorPage $page, UserSession $user)
    {
        if ($user->IsFirstLogin && !Configuration::Instance()->GetSectionKey(ConfigSection::REGISTRATION, ConfigKeys::REGISTRATION_BYPASS_FIRST_LOGIN_SCREEN, new BooleanConverter())) {
            return Pages::FIRST_LOGIN;
        }
        $redirect = $page->GetResumeUrl();

        if (!empty($redirect)) {
            return html_entity_decode($redirect);
        } else {
            $defaultId = ServiceLocator::GetServer()->GetUserSession()->HomepageId;
            $url = Pages::UrlFromId($defaultId);
            return empty($url) ? Pages::UrlFromId(Pages::DEFAULT_HOMEPAGE_ID) : $url;
        }
    }
}

class LoginRedirectorPageAdapter implements ILoginRedirectorPage
{
    private $resumeUrl;
    private IPage $page;

    public function __construct($resumeUrl, IPage $page)
    {
        $this->resumeUrl = $resumeUrl;
        $this->page = $page;
    }

    public function GetResumeUrl()
    {
        return $this->resumeUrl;
    }

    public function Redirect($url)
    {
        $this->page->Redirect($url);
    }
}