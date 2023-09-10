<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

interface IAuthenticationPage
{
    /**
     * @return string
     */
    public function GetEmailAddress();

    /**
     * @return string
     */
    public function GetPassword();

    /**
     * @return void
     */
    public function SetShowLoginError();
}

interface IAuthentication extends IAuthenticationPromptOptions, IAuthenticationActionOptions
{
    /**
     * @abstract
     * @param string $username
     * @param string $password
     * @return bool If user is valid
     */
    public function Validate($username, $password);

    /**
     * @abstract
     * @param string $username
     * @param ILoginContext $loginContext
     * @return UserSession
     */
    public function Login($username, $loginContext);

    /**
     * @param UserSession $user
     * @return void
     */
    public function Logout(UserSession $user);

    /**
     * @return bool
     */
    public function AreCredentialsKnown();

    /**
     * @param IAuthenticationPage $loginPage
     * @return void
     */
    public function HandleLoginFailure(IAuthenticationPage $loginPage);
}

interface IAuthenticationPromptOptions
{
    /**
     * @return bool
     */
    public function ShowUsernamePrompt();

    /**
     * @return bool
     */
    public function ShowPasswordPrompt();

    /**
     * @return bool
     */
    public function ShowPersistLoginPrompt();

    /**
     * @return bool
     */
    public function ShowForgotPasswordPrompt();

    /**
     * @return boolean
     */
    public function AllowRegistration();

    /**
     * @return bool
     */
    public function AllowManualLogin();
}

interface IAuthenticationActionOptions
{
    /**
     * @return bool
     */
    public function AllowUsernameChange();

    /**
     * @return bool
     */
    public function AllowEmailAddressChange();

    /**
     * @return bool
     */
    public function AllowPasswordChange();

    /**
     * @return bool
     */
    public function AllowNameChange();

    /**
     * @return bool
     */
    public function AllowPhoneChange();

    /**
     * @return bool
     */
    public function AllowOrganizationChange();

    /**
     * @return bool
     */
    public function AllowPositionChange();
}