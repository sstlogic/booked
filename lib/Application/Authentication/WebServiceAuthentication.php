<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Access/UserSessionRepository.php');
require_once(ROOT_DIR . 'lib/Application/Authentication/namespace.php');

interface IWebServiceAuthentication
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
     * @return WebServiceUserSession
     */
    public function Login($username);

    /**
     * @param string $publicUserId
     * @param string $sessionToken
     * @return void
     */
    public function Logout($publicUserId, $sessionToken);
}

class WebServiceAuthentication implements IWebServiceAuthentication
{
    /**
     * @var IAuthentication
     */
    private $authentication;
    /**
     * @var IAuthentication
     */
    private $apiAuthentication;
    /**
     * @var IUserSessionRepository
     */
    private $userSessionRepository;

    /**
     * @param IAuthentication $authentication
     * @param IUserSessionRepository $userSessionRepository
     */
    public function __construct(IAuthentication $authentication, IUserSessionRepository $userSessionRepository, IAuthentication $apiAuthentication)
    {
        $this->authentication = $authentication;
        $this->apiAuthentication = $apiAuthentication;
        $this->userSessionRepository = $userSessionRepository;
    }

    /**
     * @param string $username
     * @param string $password
     * @return bool If user is valid
     */
    public function Validate($username, $password)
    {
        if ($this->apiAuthentication->Validate($username, $password)) {
            return true;
        }
        return $this->authentication->Validate($username, $password);
    }

    /**
     * @param string $username
     * @return WebServiceUserSession
     */
    public function Login($username)
    {
        Log::Debug('Web Service Login', ['username' => $username]);
        $userSession = $this->apiAuthentication->Login($username, new WebServiceLoginContext());

        if ($userSession == null) {
            $userSession = $this->authentication->Login($username, new WebServiceLoginContext());
        }

        if ($userSession->IsLoggedIn()) {
            $webSession = WebServiceUserSession::FromSession($userSession);
            $existingSession = $this->userSessionRepository->LoadBySessionToken($webSession->SessionToken);

            if ($existingSession == null) {
                $this->userSessionRepository->Add($webSession);
            }
            else {
                $this->userSessionRepository->Update($webSession);
            }

            return $webSession;
        }

        return new NullUserSession();
    }

    /**
     * @param int $userId
     * @param string $sessionToken
     * @return void
     */
    public function Logout($userId, $sessionToken)
    {
        Log::Debug('Web Service Logout', ['sessionToken' => $sessionToken]);

        $webSession = $this->userSessionRepository->LoadBySessionToken($sessionToken);
        if ($webSession != null && $webSession->UserId == $userId) {
            $this->userSessionRepository->Delete($webSession);
            $this->authentication->Logout($webSession);
        }
    }
}

class WebServiceLoginContext implements ILoginContext
{
    /**
     * @return LoginData
     */
    public function GetData()
    {
        return new LoginData(false, null, Date::Now()->ToIso(true));
    }
}
