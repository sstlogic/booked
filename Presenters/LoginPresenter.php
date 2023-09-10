<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Config/namespace.php');
require_once(ROOT_DIR . 'lib/Common/namespace.php');
require_once(ROOT_DIR . 'Presenters/Authentication/LoginRedirector.php');

class LoginPresenter
{
    /**
     * @var ILoginPage
     */
    private $page = null;

    /**
     * @var IWebAuthentication
     */
    private $authentication = null;

    /**
     * @var ICaptchaService
     */
    private $captchaService;

    /**
     * @var IAnnouncementRepository
     */
    private $announcementRepository;

    /**
     * @var IOAuthRepository
     */
    private $oauthRepository;

    /**
     * @param ILoginPage $page
     * @param IWebAuthentication|null $authentication
     * @param ICaptchaService|null $captchaService
     * @param IAnnouncementRepository|null $announcementRepository
     * @param IOAuthRepository|null $oauthRepository
     */
    public function __construct(ILoginPage $page, $authentication = null, $captchaService = null, $announcementRepository = null, $oauthRepository = null)
    {
        $this->page = $page;
        $this->SetAuthentication($authentication);
        $this->SetCaptchaService($captchaService);
        $this->SetAnnouncementRepository($announcementRepository);
        $this->SetOAuthRepository($oauthRepository);
        $this->LoadValidators();
    }

    /**
     * @param IWebAuthentication $authentication
     */
    private function SetAuthentication($authentication)
    {
        if (is_null($authentication)) {
            $this->authentication = new WebAuthentication(PluginManager::Instance()->LoadAuthentication(), ServiceLocator::GetServer());
        } else {
            $this->authentication = $authentication;
        }
    }

    /**
     * @param ICaptchaService $captchaService
     */
    private function SetCaptchaService($captchaService)
    {
        if (is_null($captchaService)) {
            $this->captchaService = CaptchaService::Create();
        } else {
            $this->captchaService = $captchaService;
        }
    }

    /**
     * @param IAnnouncementRepository $announcementRepository
     */
    private function SetAnnouncementRepository($announcementRepository)
    {
        if (is_null($announcementRepository)) {
            $this->announcementRepository = new AnnouncementRepository();
        } else {
            $this->announcementRepository = $announcementRepository;
        }
    }

    /**
     * @param IOAuthRepository|null $oauthRepository
     */
    private function SetOAuthRepository($oauthRepository)
    {
        if (is_null($oauthRepository)) {
            $this->oauthRepository = new OAuthRepository();
        } else {
            $this->oauthRepository = $oauthRepository;
        }
    }

    public function PageLoad()
    {
        if ($this->authentication->IsLoggedIn()) {
            $this->_Redirect();
            return;
        }

        $this->SetSelectedLanguage();

        if ($this->authentication->AreCredentialsKnown()) {
            $this->Login();
            return;
        }

        $server = ServiceLocator::GetServer();
        $loginCookie = $server->GetCookie(CookieKeys::PERSIST_LOGIN);

        if ($this->IsCookieLogin($loginCookie)) {
            if ($this->authentication->CookieLogin($loginCookie, new WebLoginContext(new LoginData(true)))) {
                $this->_Redirect();
                return;
            }
        }

        $allowRegistration = Configuration::Instance()->GetKey(ConfigKeys::ALLOW_REGISTRATION, new BooleanConverter()) && $this->authentication->AllowRegistration();
        $allowAnonymousSchedule = Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_VIEW_SCHEDULES, new BooleanConverter());
        $allowGuestBookings = Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_ALLOW_GUEST_BOOKING, new BooleanConverter());
        $this->page->SetShowRegisterLink($allowRegistration);
        $this->page->SetShowScheduleLink($allowAnonymousSchedule || $allowGuestBookings);

        $hideLogin = Configuration::Instance()->GetSectionKey(ConfigSection::AUTHENTICATION, ConfigKeys::AUTHENTICATION_HIDE_BOOKED_LOGIN_PROMPT, new BooleanConverter());

        $this->page->ShowForgotPasswordPrompt(!Configuration::Instance()->GetKey(ConfigKeys::DISABLE_PASSWORD_RESET, new BooleanConverter()) &&
            $this->authentication->ShowForgotPasswordPrompt() &&
            !$hideLogin);
        $this->page->ShowPasswordPrompt($this->authentication->ShowPasswordPrompt() && !$hideLogin);
        $this->page->ShowPersistLoginPrompt($this->authentication->ShowPersistLoginPrompt());

        $this->page->ShowUsernamePrompt($this->authentication->ShowUsernamePrompt() && !$hideLogin);
        $this->page->SetRegistrationUrl($this->authentication->GetRegistrationUrl() && !$hideLogin);
        $this->page->SetPasswordResetUrl($this->authentication->GetPasswordResetUrl());
        $this->page->SetAnnouncements($this->announcementRepository->GetFuture(Pages::ID_LOGIN));
        $this->page->SetSelectedLanguage(Resources::GetInstance()->CurrentLanguage);
        $this->page->SetPromptedSSO($this->authentication->AllowManualLogin());

        $this->BindOAuthClients();
    }

    public function Login()
    {
        if (!$this->page->IsValid()) {
            return;
        }

        $id = $this->page->GetEmailAddress();

        if ($this->authentication->Validate($id, $this->page->GetPassword())) {
            $loginToken = ServiceLocator::GetServer()->GetCookie(CookieKeys::LOGIN_TOKEN);
            $context = new WebLoginContext(new LoginData($this->page->GetPersistLogin(), $this->page->GetSelectedLanguage(), $loginToken, true));
            $this->authentication->Login($id, $context);
            $this->_Redirect();
        } else {
            sleep(2);
            $this->authentication->HandleLoginFailure($this->page);
            $this->page->SetShowLoginError();
        }
    }

    public function ChangeLanguage()
    {
        $resources = Resources::GetInstance();

        $languageCode = $this->page->GetRequestedLanguage();

        if ($resources->SetLanguage($languageCode)) {
            ServiceLocator::GetServer()->SetCookie(new Cookie(CookieKeys::LANGUAGE, $languageCode));
            $this->page->SetSelectedLanguage($languageCode);
            $this->page->Redirect(Pages::LOGIN);
        }
    }

    public function Logout()
    {
        $url = Configuration::Instance()->GetKey(ConfigKeys::LOGOUT_URL);
        if (empty($url)) {
            $url = htmlspecialchars_decode($this->page->GetResumeUrl());
            $url = sprintf('%s?%s=%s', Pages::LOGIN, QueryStringKeys::REDIRECT, urlencode($url));
        }
        $this->authentication->Logout(ServiceLocator::GetServer()->GetUserSession());
        $this->page->RedirectUnsafe($url);
    }

    private function _Redirect()
    {
        LoginRedirector::Redirect($this->page, ServiceLocator::GetServer()->GetUserSession());
    }

    private function IsCookieLogin($loginCookie)
    {
        return !empty($loginCookie);
    }

    private function SetSelectedLanguage()
    {
        $requestedLanguage = $this->page->GetRequestedLanguage();
        if (!empty($requestedLanguage)) {
            // this is handled by ChangeLanguage()
            return;
        }

        $languageCookie = ServiceLocator::GetServer()->GetCookie(CookieKeys::LANGUAGE);
        $languageHeader = ServiceLocator::GetServer()->GetLanguage();
        $languageCode = Configuration::Instance()->GetKey(ConfigKeys::LANGUAGE);

        $resources = Resources::GetInstance();

        if ($resources->IsLanguageSupported($languageCookie)) {
            $languageCode = $languageCookie;
        } else {
            if ($resources->IsLanguageSupported($languageHeader)) {
                $languageCode = $languageHeader;
            }
        }

        $this->page->SetSelectedLanguage(strtolower($languageCode . ''));
        $resources->SetLanguage($languageCode);
    }

    protected function LoadValidators()
    {
        if (Configuration::Instance()->GetSectionKey(ConfigSection::AUTHENTICATION, ConfigKeys::AUTHENTICATION_CAPTCHA_ON_LOGIN, new BooleanConverter())) {
            $this->page->RegisterValidator('captcha', new CaptchaValidator($this->page->GetCaptcha(), $this->captchaService));
        }
    }

    private function BindOAuthClients()
    {
        $options = [];
        $clients = $this->oauthRepository->LoadAll();
        foreach ($clients as $client) {
            $url = (new Url(Configuration::Instance()->GetScriptUrl()))
                ->AddSegment("integrate")
                ->AddSegment("oauth-launch.php")
                ->AddQueryString(QueryStringKeys::PUBLIC_ID, $client->GetPublicId())
                ->AddQueryString(QueryStringKeys::REDIRECT, $this->page->GetResumeUrl());
            $options[] = ['name' => $client->GetName(), 'url' => $url->ToString()];
        }

        $this->page->BindOAuthClients($options);
        ServiceLocator::GetServer()->SetSession(SessionKeys::OAUTH_STATE, null);
    }
}