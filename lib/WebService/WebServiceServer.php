<?php
/**
 * Copyright 2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Server/Server.php');

class WebServiceServer extends Server
{
    private IRestServer $slimServer;

    public function __construct(IRestServer $slimServer)
    {
        parent::__construct();
        $this->slimServer = $slimServer;
    }

    public function SetUserSession($userSession)
    {
        $this->slimServer->SetSession($userSession);
    }

    public function GetUserSession()
    {
        return $this->slimServer->GetSession();
    }
}