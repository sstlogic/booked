<?php
/**
 * Copyright 2013-2023 Twinkle Toes Software, LLC
 */

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require_once(ROOT_DIR . 'lib/WebService/namespace.php');
require_once(ROOT_DIR . 'WebServices/Controllers/UserSaveController.php');
require_once(ROOT_DIR . 'WebServices/Requests/User/CreateUserRequest.php');
require_once(ROOT_DIR . 'WebServices/Requests/User/UpdateUserRequest.php');
require_once(ROOT_DIR . 'WebServices/Requests/User/UpdateUserPasswordRequest.php');
require_once(ROOT_DIR . 'WebServices/Requests/User/UpdateUserStatusRequest.php');
require_once(ROOT_DIR . 'WebServices/Responses/UserCreatedResponse.php');
require_once(ROOT_DIR . 'WebServices/Responses/UserUpdatedResponse.php');
require_once(ROOT_DIR . 'Domain/Values/CountryCodes.php');

class UsersWriteWebService
{
    /**
     * @var IRestServer
     */
    private $server;

    /**
     * @var IUserSaveController
     */
    private $controller;

    public function __construct(IRestServer $server, IUserSaveController $controller)
    {
        $this->server = $server;
        $this->controller = $controller;
    }

    /**
     * @name CreateUser
     * @description Creates a new user
     * @request CreateUserRequest
     * @response UserCreatedResponse
     * @return Response
     */
    public function Create(Request $request, Response $response, $args)
    {
        /** @var $req CreateUserRequest */
        $req = new CreateUserRequest($this->server->GetRequest($request));

        Log::Debug('UsersWriteWebService.Create()', ['userId' => $this->server->GetSession()->UserId]);

        $result = $this->controller->Create($req, $this->server->GetSession());

        if ($result->WasSuccessful()) {
            Log::Debug('UsersWriteWebService.Create() - User Created. Created',
                ['userId' => $result->UserId()]);

            return $this->server->WriteResponse(new UserCreatedResponse($this->server, $result->UserId()),
                $response, RestResponse::CREATED_CODE);
        } else {
            Log::Debug('UsersWriteWebService.Create() - User Create Failed.');

            return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()),
                $response, RestResponse::BAD_REQUEST_CODE);
        }
    }

    /**
     * @name UpdateUser
     * @description Updates an existing user
     * @request UpdateUserRequest
     * @response UserUpdatedResponse
     * @param $userId
     * @return Response
     */
    public function Update(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        /** @var $req UpdateUserRequest */
        $req = new UpdateUserRequest($this->server->GetRequest($request));

        Log::Debug('UsersWriteWebService.Update()', ['userId' => $this->server->GetSession()->UserId]);

        $result = $this->controller->Update($userId, $req, $this->server->GetSession());

        if ($result->WasSuccessful()) {
            Log::Debug('UsersWriteWebService.Update() - User Updated.',
                ['userId' => $result->UserId()]);

            return $this->server->WriteResponse(new UserUpdatedResponse($this->server, $result->UserId()),
                $response, RestResponse::OK_CODE);
        } else {
            Log::Debug('UsersWriteWebService.Create() - User Update Failed.');

            return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()),
                $response, RestResponse::BAD_REQUEST_CODE);
        }
    }

    /**
     * @name UpdateUserStatus
     * @description Updates the status of an existing user. Options are 1 (Active) or 3 (Inactive)
     * @request UpdateUserStatusRequest
     * @response UserUpdatedResponse
     * @param $userId
     * @return Response
     */
    public function UpdateStatus(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        /** @var $req UpdateUserStatusRequest */
        $req = new UpdateUserStatusRequest($this->server->GetRequest($request));

        Log::Debug('UsersWriteWebService.UpdateStatus()', ['userId' => $this->server->GetSession()->UserId]);

        $result = $this->controller->UpdateStatus($userId, $req, $this->server->GetSession());

        if ($result->WasSuccessful()) {
            Log::Debug('UsersWriteWebService.UpdateStatus() - User Updated.',
                ['userId' => $result->UserId()]);

            return $this->server->WriteResponse(new UserUpdatedResponse($this->server, $result->UserId()),
                $response, RestResponse::OK_CODE);
        } else {
            Log::Debug('UsersWriteWebService.UpdateStatus() - User Update Failed.');

            return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()),
                $response, RestResponse::BAD_REQUEST_CODE);
        }
    }

    /**
     * @name DeleteUser
     * @description Deletes an existing user
     * @response DeletedResponse
     * @param int $userId
     * @return Response
     */
    public function Delete(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        Log::Debug('UsersWriteWebService.Delete()', ['userId' => $this->server->GetSession()->UserId]);

        $result = $this->controller->Delete($userId, $this->server->GetSession());

        if ($result->WasSuccessful()) {
            Log::Debug('UsersWriteWebService.Delete() - User Deleted.',
                ['userId' => $result->UserId()]);

            return $this->server->WriteResponse(new DeletedResponse(), $response, RestResponse::OK_CODE);
        } else {
            Log::Debug('UsersWriteWebService.Delete() - User Delete Failed.');

            return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()),
                $response, RestResponse::BAD_REQUEST_CODE);
        }
    }

    /**
     * @name UpdatePassword
     * @description Updates the password for an existing user
     * @request UpdateUserPasswordRequest
     * @response UserUpdatedResponse
     * @param int $userId
     * @return Response
     */
    public function UpdatePassword(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        Log::Debug('UsersWriteWebService.UpdatePassword()', ['userId' => $this->server->GetSession()->UserId]);

        /** @var $req UpdateUserPasswordRequest */
        $req = new UpdateUserPasswordRequest($this->server->GetRequest($request));

        $result = $this->controller->UpdatePassword($userId, $req->password, $this->server->GetSession());

        if ($result->WasSuccessful()) {
            Log::Debug('UsersWriteWebService.UpdatePassword() - User password updated.',
                ['userId' => $result->UserId()]);

            return $this->server->WriteResponse(new UserUpdatedResponse($this->server, $result->UserId()), $response, RestResponse::OK_CODE);
        } else {
            Log::Debug('UsersWriteWebService.UpdatePassword() - User Password Update Failed.');

            return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()),
                $response, RestResponse::BAD_REQUEST_CODE);
        }
    }
}

