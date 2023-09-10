<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/Integrate/OAuthPage.php');
require_once(ROOT_DIR . 'Presenters/Authentication/LoginRedirector.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');

class OAuthPresenter
{
    private IOAuthPage $page;
    private IWebAuthentication $authentication;
    private IRegistration $registration;
    private IOAuthRepository $oauthRepository;

    public function __construct(IOAuthPage $page, IWebAuthentication $authentication, IRegistration $registration, IOAuthRepository $oauthRepository)
    {
        $this->page = $page;
        $this->authentication = $authentication;
        $this->registration = $registration;
        $this->oauthRepository = $oauthRepository;
    }

    public function PageLoad()
    {
        if (!Configuration::Instance()->GetSectionKey(ConfigSection::AUTHENTICATION, ConfigKeys::AUTHENTICATION_ALLOW_OAUTH)) {
            die();
        }
        
        $providedState = $this->page->GetState();
        $expectedState = ServiceLocator::GetServer()->GetSession(SessionKeys::OAUTH_STATE);
        if (empty($expectedState) || $providedState !== $expectedState) {
            Log::Error('OAuth Login invalid state.', ['expected' => $expectedState, 'provided' => $providedState]);
            $this->page->ShowError([Resources::GetInstance()->GetString('OAuthLoginError')]);
            return;
        }

        $state = OAuthState::CreateFromQueryString($providedState);
        $oauthClient = $this->oauthRepository->LoadByPublicId($state->providerId);

        if (empty($oauthClient)) {
            Log::Error('Invalid OAuth provider.', ['providerId' => $state->providerId]);
            $this->page->ShowError([Resources::GetInstance()->GetString('OAuthLoginError')]);
            return;
        }

        $code = $this->page->GetCode();
        try {
            $profile = $oauthClient->GetUser($code);
        } catch (Throwable $ex) {
            Log::Error('Unhandled OAuth exception', ['exception' => $ex]);
            $this->page->ShowError([Resources::GetInstance()->GetString('OAuthLoginError')]);
            return;
        }

        $email = $profile->Email();

        if (!RequiredEmailDomainValidator::IsEmailAddressValid($email)) {
            Log::Debug('OAuth login with invalid domain.', ['email' => $email]);
            $this->page->ShowError([Resources::GetInstance()->GetString('InvalidEmailDomain')]);
            return;
        }

        Log::Debug('OAuth login successful.', ['email' => $email]);
        $user = $this->registration->Synchronize(new AuthenticatedUser($email,
            $email,
            $profile->FirstName(),
            $profile->LastName(),
            Password::GenerateRandom(),
            Resources::GetInstance()->CurrentLanguage,
            Configuration::Instance()->GetDefaultTimezone(),
            null,
            null,
            null),
            false,
            false);

        $this->authentication->Login($email, new WebLoginContext(new LoginData(false, $user->Language(), $user->LoginToken())));
        LoginRedirector::Redirect(new LoginRedirectorPageAdapter($state->resumeUrl, $this->page), ServiceLocator::GetServer()->GetUserSession());
    }

    public function Launch()
    {
        $id = $this->page->GetLaunchProviderId();
        $client = $this->oauthRepository->LoadByPublicId($id);
        $resumeUrl = $this->page->GetResumeUrl();

        $state = $client->GetState($resumeUrl);

        ServiceLocator::GetServer()->SetSession(SessionKeys::OAUTH_STATE, $state);

        header("Location: " . $client->GetAuthorizationUrl($state));
        die();
    }
}