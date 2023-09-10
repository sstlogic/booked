<?php

/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/Authentication/LoginRedirector.php');

class ExternalAuthLoginPresenter
{
    /**
     * @var ExternalAuthLoginPage
     */
    private $page;
    /**
     * @var IWebAuthentication
     */
    private $authentication;
    /**
     * @var IRegistration
     */
    private $registration;

    public function __construct(ExternalAuthLoginPage $page, IWebAuthentication $authentication, IRegistration $registration)
    {
        $this->page = $page;
        $this->authentication = $authentication;
        $this->registration = $registration;
    }

    public function PageLoad()
    {
        if ($this->page->GetType() == 'google') {
            if (!Configuration::Instance()->GetSectionKey(ConfigSection::AUTHENTICATION, ConfigKeys::AUTHENTICATION_ALLOW_GOOGLE)) {
                die();
            }
            $this->ProcessSocialSingleSignOn('googleprofile.php');
        }
        if ($this->page->GetType() == 'fb') {
            if (!Configuration::Instance()->GetSectionKey(ConfigSection::AUTHENTICATION, ConfigKeys::AUTHENTICATION_ALLOW_FACEBOOK)) {
                die();
            }
            $this->ProcessSocialSingleSignOn('fbprofile.php');
        }
        if ($this->page->GetType() == 'zoom') {
            if (!Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_ALLOW_MEETING_LINKS)) {
                die();
            }
            $this->ProcessZoom();
        }
        if ($this->page->GetType() == 'teams') {
            if (!Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_ALLOW_MEETING_LINKS)) {
                die();
            }
            $this->ProcessTeams();
        }
    }

    private function ProcessSocialSingleSignOn($page)
    {
        $code = $_GET['code'];
        Log::Debug('Logging in with social.', ['code' => $code]);
        $result = file_get_contents("https://www.social.twinkletoessoftware.com/$page?code=$code");
        $profile = json_decode($result);

        $requiredDomainValidator = new RequiredEmailDomainValidator($profile->email);
        $requiredDomainValidator->Validate();
        if (!$requiredDomainValidator->IsValid()) {
            Log::Debug('Social login with invalid domain.', ['email' => $profile->email]);
            $this->page->ShowError(array(Resources::GetInstance()->GetString('InvalidEmailDomain')));
            return;
        }

        Log::Debug('Social login successful', ['email' => $profile->email]);
        $user = $this->registration->Synchronize(new AuthenticatedUser($profile->email,
            $profile->email,
            $profile->first_name,
            $profile->last_name,
            Password::GenerateRandom(),
            Resources::GetInstance()->CurrentLanguage,
            Configuration::Instance()->GetDefaultTimezone(),
            null,
            null,
            null),
            false,
            false);

        $this->authentication->Login($profile->email, new WebLoginContext(new LoginData(false, $user->Language(), $user->LoginToken())));
        LoginRedirector::Redirect($this->page, ServiceLocator::GetServer()->GetUserSession());
    }

    public function ProcessZoom() {
        if (!$this->page->IsAuthenticated()) {
            return;
        }

        $code = $_GET['code'];
        Log::Debug('Authorizing with Zoom.', ['code' => $code]);
        $result = file_get_contents("https://www.social.twinkletoessoftware.com/zoomtoken.php?code=$code");
        $tokenParts = json_decode($result);
        $userSession = ServiceLocator::GetServer()->GetUserSession();
        if (!isset($tokenParts->access_token)) {
            Log::Error("Error linking Zoom account.", ['result' => $result]);
            LoginRedirector::Redirect($this->page, $userSession);
            return;
        }

        $userRepo = new UserRepository();
        $userRepo->AddOrUpdateOAuth($userSession->UserId, $tokenParts->access_token, $tokenParts->refresh_token, Date::Now()->AddSeconds($tokenParts->expires_in), OAuthProviders::Zoom);

        LoginRedirector::Redirect($this->page, $userSession);
    }

    public function ProcessTeams() {
        if (!$this->page->IsAuthenticated()) {
            return;
        }

        $code = $_GET['code'];
        Log::Debug('Authorizing with Teams.', ['code' => $code]);
        $result = file_get_contents("https://www.social.twinkletoessoftware.com/teamstoken.php?code=$code");
        $tokenParts = json_decode($result);
        $userSession = ServiceLocator::GetServer()->GetUserSession();
        if (!isset($tokenParts->access_token)) {
            Log::Error("Error linking Teams account.", ['result' => $result]);
            LoginRedirector::Redirect($this->page, $userSession);
            return;
        }

        $userRepo = new UserRepository();
        $userRepo->AddOrUpdateOAuth($userSession->UserId, $tokenParts->access_token, $tokenParts->refresh_token, Date::Now()->AddSeconds($tokenParts->expires_in), OAuthProviders::Microsoft);

        LoginRedirector::Redirect($this->page, $userSession);
    }
}
