<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require_once(ROOT_DIR . 'lib/WebService/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Authentication/namespace.php');
require_once(ROOT_DIR . 'WebServices/Responses/AuthenticationResponse.php');
require_once(ROOT_DIR . 'WebServices/Requests/AuthenticationRequest.php');
require_once(ROOT_DIR . 'WebServices/Requests/SignOutRequest.php');

class AuthenticationWebService
{
    /**
     * @var IWebServiceAuthentication
     */
    private $authentication;
    /**
     * @var IRestServer
     */
    private $server;

    public function __construct(IRestServer $server, IWebServiceAuthentication $authentication)
    {
        $this->server = $server;
        $this->authentication = $authentication;
    }

    /**
     * @name Authenticate
     * @description Authenticates an existing Booked Scheduler user
     * @request AuthenticationRequest
     * @response AuthenticationResponse
     * @return Response
     */
    public function Authenticate(Request $request, Response $response)
    {
//		/** @var $r AuthenticationRequest */
        $r = $this->server->GetRequest($request);
        $username = $r->username;
        $password = $r->password;

        Log::Debug('WebService Authenticate', ['username' => $username]);

        $isValid = $this->authentication->Validate($username, $password);
        if ($isValid) {
            Log::Debug('WebService Authenticate, was authenticated', ['username' => $username]);

            $version = Configuration::VERSION;

            $session = $this->authentication->Login($username);
            return $this->server->WriteResponse(AuthenticationResponse::Success($this->server, $session, $version), $response);
        } else {
            Log::Debug('WebService Authenticate, user was not authenticated', ['username' => $username]);

            return $this->server->WriteResponse(AuthenticationResponse::Failed(), $response);
        }
    }

    /**
     * @name SignOut
     * @description Ends the current session
     * @return Response
     */
    public function SignOut(Request $request, Response $response)
    {
        $session = $this->server->GetSession();
        $userId = $session->UserId;
        $sessionToken = $session->SessionToken;

        Log::Debug('WebService SignOut', ['userId' => $userId, 'sessionToken' => $sessionToken]);

        $this->authentication->Logout($userId, $sessionToken);
        $r = new SignedOutResponse();
        $r->signedOut = true;
        return $this->server->WriteResponse($r, $response);
    }
}

