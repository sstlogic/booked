<?php
/**
 * Copyright 2018-2023 Twinkle Toes Software, LLC
 */

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require_once(ROOT_DIR . 'lib/WebService/namespace.php');
require_once(ROOT_DIR . 'WebServices/Controllers/GroupSaveController.php');
require_once(ROOT_DIR . 'WebServices/Responses/Group/GroupCreatedResponse.php');
require_once(ROOT_DIR . 'WebServices/Requests/Group/GroupRequest.php');
require_once(ROOT_DIR . 'WebServices/Requests/Group/GroupRolesRequest.php');
require_once(ROOT_DIR . 'WebServices/Requests/Group/GroupPermissionsRequest.php');
require_once(ROOT_DIR . 'WebServices/Requests/Group/GroupUsersRequest.php');

class GroupsWriteWebService
{
    /**
     * @var IGroupSaveController
     */
    private $controller;

    /**
     * @var IRestServer
     */
    private $server;

    public function __construct(IRestServer $server, IGroupSaveController $controller)
    {
        $this->server = $server;
        $this->controller = $controller;
    }

    /**
     * @name CreateGroup
     * @description Creates a new group
     * @request GroupRequest
     * @response GroupCreatedResponse
     * @return Response
     */
    public function Create(Request $request, Response $response, $args)
    {
        /** @var $req GroupRequest */
        $req = $this->server->GetRequest($request);

        Log::Debug('GroupsWriteWebService.Create()', ['userId' => $this->server->GetSession()->UserId, 'request' => $request]);

        $result = $this->controller->Create($req, $this->server->GetSession());

        if ($result->WasSuccessful()) {
            Log::Debug('GroupsWriteWebService.Create() - Group Created.', ['groupId' => $result->GroupId()]);

            return $this->server->WriteResponse(new GroupCreatedResponse($this->server, $result->GroupId()), $response, RestResponse::CREATED_CODE);
        } else {
            Log::Debug('GroupsWriteWebService.Create() - Create Failed.');

            return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()), $response, RestResponse::BAD_REQUEST_CODE);
        }
    }

    /**
     * @name UpdateGroup
     * @description Updates an existing group
     * @request GroupRequest
     * @response GroupUpdatedResponse
     * @param $groupId
     * @return Response
     */
    public function Update(Request $request, Response $response, $args)
    {
        $groupId = $args['groupId'];
        /** @var $req GroupRequest */
        $req = $this->server->GetRequest($request);

        Log::Debug('GroupsWriteWebService.Update()', ['userId' => $this->server->GetSession()->UserId, 'groupId' => $groupId, 'request' => $request]);

        $result = $this->controller->Update($groupId, $req, $this->server->GetSession());

        if ($result->WasSuccessful()) {
            Log::Debug('GroupsWriteWebService.Update() - Group Updated.', ['groupId' => $result->GroupId()]);

            return $this->server->WriteResponse(new GroupUpdatedResponse($this->server, $result->GroupId()), $response, RestResponse::CREATED_CODE);
        } else {
            Log::Debug('GroupsWriteWebService.Update() - Update Failed.');

            return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()), $response, RestResponse::BAD_REQUEST_CODE);
        }
    }

    /**
     * @name DeleteGroup
     * @description Deletes an existing group
     * @response DeletedResponse
     * @param int $groupId
     * @return Response
     */
    public function Delete(Request $request, Response $response, $args)
    {
        $groupId = $args['groupId'];
        Log::Debug('GroupsWriteWebService.Delete()', ['groupId' => $groupId, 'userId' => $this->server->GetSession()->UserId]);

        $result = $this->controller->Delete($groupId, $this->server->GetSession());

        if ($result->WasSuccessful()) {
            Log::Debug('GroupsWriteWebService.Delete() - Group Deleted.', ['groupId' => $result->GroupId()]);

            return $this->server->WriteResponse(new DeletedResponse(), $response, RestResponse::OK_CODE);
        } else {
            Log::Debug('GroupsWriteWebService.Delete() - Group Delete Failed.');

            return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()), $response, RestResponse::BAD_REQUEST_CODE);
        }
    }

    /**
     * @name ChangeGroupRoles
     * @description Updates the roles for an existing group
     * roleIds : 1 (Group Administrator), 2 (Application Administrator), 3 (Resource Administrator), 4 (Schedule Administrator)
     * @request GroupRolesRequest
     * @response GroupUpdatedResponse
     * @param int $groupId
     * @return Response
     */
    public function Roles(Request $request, Response $response, $args)
    {
        $groupId = $args['groupId'];
        /** @var $req GroupRolesRequest */
        $req = $this->server->GetRequest($request);

        Log::Debug('GroupsWriteWebService.Roles()', ['userId' => $this->server->GetSession()->UserId, 'groupId' => $groupId, 'request' => $request]);

        $result = $this->controller->ChangeRoles($groupId, $req, $this->server->GetSession());

        if ($result->WasSuccessful()) {
            Log::Debug('GroupsWriteWebService.Roles() - Group Updated.', ['groupId' => $result->GroupId()]);

            return $this->server->WriteResponse(new GroupUpdatedResponse($this->server, $result->GroupId()), $response, RestResponse::CREATED_CODE);
        } else {
            Log::Debug('GroupsWriteWebService.Roles() - Update Failed.');

            return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()), $response, RestResponse::BAD_REQUEST_CODE);
        }
    }

    /**
     * @name ChangeGroupPermissions
     * @description Updates the permissions for an existing group
     * @request GroupPermissionsRequest
     * @response GroupUpdatedResponse
     * @param int $groupId
     * @return Response
     */
    public function Permissions(Request $request, Response $response, $args)
    {
        $groupId = $args['groupId'];
        /** @var $req GroupPermissionsRequest */
        $req = $this->server->GetRequest($request);

        Log::Debug('GroupsWriteWebService.Permissions()', ['userId' => $this->server->GetSession()->UserId, 'groupId' => $groupId, 'request' => $request]);

        $result = $this->controller->ChangePermissions($groupId, $req, $this->server->GetSession());

        if ($result->WasSuccessful()) {
            Log::Debug('GroupsWriteWebService.Permissions() - Group Updated.', ['groupId' => $result->GroupId()]);

            return $this->server->WriteResponse(new GroupUpdatedResponse($this->server, $result->GroupId()), $response, RestResponse::CREATED_CODE);
        } else {
            Log::Debug('GroupsWriteWebService.Permissions() - Update Failed.');

            return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()), $response, RestResponse::BAD_REQUEST_CODE);
        }
    }

    /**
     * @name ChangeGroupUsers
     * @description Updates the users belonging to an existing group
     * @request GroupUsersRequest
     * @response GroupUpdatedResponse
     * @param int $groupId
     * @return Response
     */
    public function Users(Request $request, Response $response, $args)
    {
        $groupId = $args['groupId'];
        /** @var $req GroupUsersRequest */
        $req = $this->server->GetRequest($request);

        Log::Debug('GroupsWriteWebService.Users()', ['userId' => $this->server->GetSession()->UserId, 'groupId' => $groupId, 'request' => $request]);

        $result = $this->controller->ChangeUsers($groupId, $req, $this->server->GetSession());

        if ($result->WasSuccessful()) {
            Log::Debug('GroupsWriteWebService.Users() - Group Updated.', ['groupId' => $result->GroupId()]);

            return $this->server->WriteResponse(new GroupUpdatedResponse($this->server, $result->GroupId()), $response, RestResponse::CREATED_CODE);
        } else {
            Log::Debug('GroupsWriteWebService.Users() - Update Failed.');

            return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()), $response, RestResponse::BAD_REQUEST_CODE);
        }
    }
}
