<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

use Psr\Log\LoggerInterface;

require_once(ROOT_DIR . 'lib/Application/Authentication/namespace.php');
require_once(ROOT_DIR . 'plugins/Authentication/CAS/namespace.php');

class CasLogger implements LoggerInterface
{
    public function emergency($message, array $context = array()): void
    {
        Log::Error($message, $context);
    }

    public function alert($message, array $context = array()): void
    {
        Log::Error($message, $context);
    }

    public function critical($message, array $context = array()): void
    {
        Log::Error($message, $context);
    }

    public function error($message, array $context = array()): void
    {
        Log::Error($message, $context);
    }

    public function warning($message, array $context = array()): void
    {
        Log::Debug($message, $context);
    }

    public function notice($message, array $context = array()): void
    {
        Log::Debug($message, $context);
    }

    public function info($message, array $context = array()): void
    {
        Log::Debug($message, $context);
    }

    public function debug($message, array $context = array()): void
    {
        Log::Debug($message, $context);
    }

    public function log($level, $message, array $context = array()): void
    {
        Log::Debug($message, $context);
    }
}

class CAS extends Authentication implements IAuthentication
{
    private $authToDecorate;
    private $registration;

    /**
     * @var CASOptions
     */
    private $options;

    /**
     * @return Registration
     */
    private function GetRegistration()
    {
        if ($this->registration == null) {
            $this->registration = new Registration();
        }

        return $this->registration;
    }

    public function __construct(Authentication $authentication)
    {
        $this->options = new CASOptions();
        $this->setCASSettings();
        $this->authToDecorate = $authentication;
    }

    private function setCASSettings()
    {
        if ($this->options->IsCasDebugOn()) {
            phpCAS::setLogger(new CasLogger());
        }

        phpCAS::client($this->options->CasVersion(), $this->options->HostName(), $this->options->Port(), $this->options->ServerUri(), $this->options->ChangeSessionId());

        if ($this->options->CasHandlesLogouts()) {
            phpCAS::handleLogoutRequests(true);
            phpCAS::setServerLogoutURL($this->options->LogoutServers()[0]);
        }

        if ($this->options->HasCertificate()) {
            phpCAS::setCasServerCACert($this->options->Certificate());
        }
        phpCAS::setNoCasServerValidation();
    }

    public function Validate($username, $password)
    {
        try {
            phpCAS::forceAuthentication();

        } catch (Exception $ex) {
            Log::Error('CAS exception', ['exception' => $ex]);
            return false;
        }
        return true;
    }

    public function Login($username, $loginContext)
    {
        Log::Debug('Attempting CAS login', ['username' => $username]);

        $isAuth = phpCAS::isAuthenticated();
        Log::Debug('CAS result', ['isAuthenticated' => $isAuth]);
        $username = phpCAS::getUser();
        $attributes = phpCAS::getAttributes();
        $this->Synchronize($username, $attributes);

        return $this->authToDecorate->Login($username, $loginContext);
    }

    public function Logout(UserSession $user)
    {
        Log::Debug('Attempting CAS logout', ['email' => $user->Email]);
        $this->authToDecorate->Logout($user);

        if ($this->options->CasHandlesLogouts()) {
            phpCAS::logout();
        }
    }

    public function AreCredentialsKnown()
    {
        return true;
    }

    public function HandleLoginFailure(IAuthenticationPage $loginPage)
    {
        $this->authToDecorate->HandleLoginFailure($loginPage);
    }

    public function ShowUsernamePrompt()
    {
        return false;
    }

    public function ShowPasswordPrompt()
    {
        return false;
    }

    public function ShowPersistLoginPrompt()
    {
        return false;
    }

    public function ShowForgotPasswordPrompt()
    {
        return false;
    }

    public function AllowUsernameChange()
    {
        return false;
    }

    public function AllowEmailAddressChange()
    {
        return false;
    }

    public function AllowPasswordChange()
    {
        return false;
    }

    public function AllowNameChange()
    {
        return false;
    }

    public function AllowPhoneChange()
    {
        return false;
    }

    public function AllowOrganizationChange()
    {
        return false;
    }

    public function AllowPositionChange()
    {
        return false;
    }

    private function Synchronize($username, $attributes)
    {
        $registration = $this->GetRegistration();

        $registration->Synchronize(
            new AuthenticatedUser(
                $username,
                $this->getAttribute($username, $attributes, 'email'),
                $this->getAttribute($username, $attributes, 'givenName'),
                $this->getAttribute($username, $attributes, 'surName'),
                BookedStringHelper::Random(12),
                Configuration::Instance()->GetKey(ConfigKeys::LANGUAGE),
                Configuration::Instance()->GetDefaultTimezone(),
                null,
                null,
                null,
                $this->getGroups($attributes))
        );
    }

    private function getAttribute($username, $attributes, $key)
    {
        $attributeMapping = $this->options->AttributeMapping();
        if (array_key_exists($key, $attributeMapping)) {
            $mappedName = $attributeMapping[$key];
            if (array_key_exists($mappedName, $attributes)) {
                return $attributes[$mappedName];
            }
        }

        return $username;
    }

    private function getGroups($attributes)
    {
        $attributeMapping = $this->options->AttributeMapping();
        if (array_key_exists('groups', $attributeMapping)) {
            $mappedName = $attributeMapping['groups'];
            if (array_key_exists($mappedName, $attributes)) {
                $userGroups = $attributes[$mappedName];
                if (!is_array($userGroups)) {
                    return array($userGroups);
                }
                return $userGroups;
            }
        }

        return null;
    }

    public function AllowRegistration()
    {
        return false;
    }
}