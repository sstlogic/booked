<?php

/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

interface IGuestUserService
{
    /**
     * @param string $email
     * @return UserSession
     */
    public function CreateOrLoad($email);

    /**
     * @param $email
     * @return bool
     */
    public function EmailExists($email);
}

class GuestUserService implements IGuestUserService
{
    /**
     * @var IAuthentication
     */
    private $authentication;

    /**
     * @var IRegistration
     */
    private $registration;

    public function __construct(IAuthentication $authentication, IRegistration $registration)
    {
        $this->authentication = $authentication;
        $this->registration = $registration;
    }

    public function CreateOrLoad($email)
    {
        $user = $this->authentication->Login($email, new WebLoginContext(new LoginData(false, "", Date::Now()->ToIso(true))));
        if ($user->IsLoggedIn()) {
            Log::Debug('User already has account, skipping guest creation', ['email' => $email]);

            return $user;
        }

        Log::Debug('Email address was not found, creating guest account', ['email' => $email]);

        $currentLanguage = Resources::GetInstance()->CurrentLanguage;
        $user = $this->registration->Register($email, $email, 'Guest', 'Guest', Password::GenerateRandom(), null, $currentLanguage, null);
        return $this->authentication->Login($email, new WebLoginContext(new LoginData(false, $currentLanguage, $user->LoginToken())));
    }

    public function EmailExists($email)
    {
        $user = $this->authentication->Login($email, new WebLoginContext(new LoginData(false, '', Date::Now()->ToIso(true))));
        return $user->IsLoggedIn();
    }
}
