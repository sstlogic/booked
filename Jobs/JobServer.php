<?php
require_once(ROOT_DIR . 'lib/Server/namespace.php');

class JobServer extends Server
{
    /**
     * @var UserSession
     */
    private $userSession;
    private $cookies = array();

    public function __construct()
    {

    }

    protected function InitSession()
    {
        // no-op
    }

    /**
     * @return UserSession
     */
    public function GetUserSession()
    {
        if ($this->userSession == null) {

            return new NullUserSession();
        }

        return $this->userSession;
    }

    /**
     * @param $userSession UserSession
     * @return void
     */
    public function SetUserSession($userSession)
    {
        $this->userSession = $userSession;
    }

    public function DeleteCookie(Cookie $cookie)
    {
        $this->cookies[$cookie->Name] = null;
    }

    public function SetCookie(Cookie $cookie)
    {
        $this->cookies[$cookie->Name] = $cookie;
    }

    public function GetCookie($name)
    {
        if (array_key_exists($name, $this->cookies)) {
            return $this->cookies[$name];
        }
        return null;
    }

}