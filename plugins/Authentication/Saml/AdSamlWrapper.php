<?php
/**
Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'plugins/Authentication/Saml/adSAML.php');
require_once(ROOT_DIR . 'plugins/Authentication/Saml/SamlUser.php');

class AdSamlWrapper implements ISaml
{
    /**
     * @var SamlOptions
     */
    private $options;

    /**
     * @var adSAML|null
     */
    private $saml;

    /**
     * @param SamlOptions $samlOptions
     */
    public function __construct($samlOptions)
    {
        $this->options = $samlOptions;
    }

    public function Connect()
    {
        $options = $this->options->AdSamlOptions();

        $this->saml = new adSaml($options);
    }

    public function Authenticate()
    {
        return $this->saml->authenticate();
    }

    public function GetSamlUser()
    {
        return new SamlUser($this->saml->getAttributes(), $this->options);
    }

    public function Logout()
    {
        $this->Connect();
        $this->saml->Logout($this->options->ReturnTo());
    }
}