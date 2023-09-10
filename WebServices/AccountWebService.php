<?php
/**
 * Copyright 2019-2023 Twinkle Toes Software, LLC
 */

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require_once(ROOT_DIR . 'lib/WebService/namespace.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'WebServices/Requests/Account/CreateAccountRequest.php');
require_once(ROOT_DIR . 'WebServices/Requests/Account/UpdateAccountRequest.php');
require_once(ROOT_DIR . 'WebServices/Requests/Account/UpdateAccountPasswordRequest.php');
require_once(ROOT_DIR . 'WebServices/Responses/Account/AccountResponse.php');
require_once(ROOT_DIR . 'WebServices/Responses/Account/AccountActionResponse.php');
require_once(ROOT_DIR . 'WebServices/Controllers/AccountController.php');

class AccountWebService
{
    /**
     * @var IRestServer
     */
    private $server;
    /**
     * @var IAccountController
     */
    private $controller;

    public function __construct(IRestServer $server, IAccountController $controller)
    {
        $this->server = $server;
        $this->controller = $controller;
    }

    /**
     * @name GetAccount
     * @description Gets the currently authenticated user account information
     * @response AccountResponse
     * @return Response
     */
    public function GetAccount(Request $request, Response $response, $args)
    {
        $session = $this->server->GetSession();
        $user = $this->controller->LoadUser($session);
        $userId = $user->Id();
        if (empty($userId))
        {
            return $this->server->WriteResponse(RestResponse::NotFound(), $response, RestResponse::NOT_FOUND_CODE);
        }

        $attributes = $this->controller->GetUserAttributes($session);
        return $this->server->WriteResponse(new AccountResponse($this->server, $user, $attributes), $response);
    }

    /**
     * @name RegisterAccount
     * @description Register a user account. This does not authenticate
     * @request CreateAccountRequest
     * @response AccountActionResponse
     * @return Response
     */
    public function Create(Request $request, Response $response, $args)
    {
        if (!Configuration::Instance()->GetSectionKey(ConfigSection::API, ConfigKeys::ALLOW_REGISTRATION, new BooleanConverter()))
        {
            return $this->server->WriteResponse(new FailedResponse($this->server, array('allow.self.registration is not enabled for the API')),
                $response,RestResponse::UNAUTHORIZED_CODE);
        }

        /** @var $req CreateAccountRequest */
        $req = new CreateAccountRequest($this->server->GetRequest($request));

        Log::Debug('AccountWebService.Create()');

        $result = $this->controller->Create($req);

        if ($result->WasSuccessful())
        {
            Log::Debug('AccountWebService.Create() - User Created. Created', ['userId' => $result->UserId()]);

            return $this->server->WriteResponse(new AccountActionResponse($this->server, $result->UserId()),
                $response,RestResponse::CREATED_CODE);
        }
        else
        {
            Log::Debug('AccountWebService.Create() - User Create Failed.');

            return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()),
                $response, RestResponse::BAD_REQUEST_CODE);
        }
    }
    
    /**
     * @name UpdateAccount
     * @description Updates the user account for the current user
     * @request UpdateAccountRequest
     * @response AccountActionResponse
     * @return Response
     */
    public function Update(Request $request, Response $response, $args)
    {
        /** @var $req UpdateAccountRequest */
        $req = new UpdateAccountRequest($this->server->GetRequest($request));

        Log::Debug('AccountWebService.Update()');

        $result = $this->controller->Update($req, $this->server->GetSession());

        if ($result->WasSuccessful())
        {
            Log::Debug('AccountWebService.Update() - User Updated. Updated', ['userId' => $result->UserId()]);

            return $this->server->WriteResponse(new AccountActionResponse($this->server, $result->UserId()),
                $response, RestResponse::OK_CODE);
        }
        else
        {
            Log::Debug('AccountWebService.Update() - User Update Failed.');

            return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()),
                $response,RestResponse::BAD_REQUEST_CODE);
        }
    }

    /**
     * @name UpdatePassword
     * @description Updates the password for the current user
     * @request UpdateAccountPasswordRequest
     * @response AccountActionResponse
     * @return Response
     */
    public function UpdatePassword(Request $request, Response $response, $args)
    {
        /** @var $req UpdateAccountPasswordRequest */
        $req = new UpdateAccountPasswordRequest($this->server->GetRequest($request));

        Log::Debug('AccountWebService.UpdatePassword()');

        $result = $this->controller->UpdatePassword($req, $this->server->GetSession());

        if ($result->WasSuccessful())
        {
            Log::Debug('AccountWebService.UpdatePassword() - Password Updated. Updated', ['userId' => $result->UserId()]);

            return $this->server->WriteResponse(new AccountActionResponse($this->server, $result->UserId()),
                $response,RestResponse::OK_CODE);
        }
        else
        {
            Log::Debug('AccountWebService.Update() - User Update Failed.');

            return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()),
                $response, RestResponse::BAD_REQUEST_CODE);
        }
    }
}