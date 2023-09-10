<?php
/**
Copyright 2013-2023 Twinkle Toes Software, LLC
 */

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require_once(ROOT_DIR . 'lib/WebService/namespace.php');
require_once(ROOT_DIR . 'WebServices/Controllers/ResourceSaveController.php');
require_once(ROOT_DIR . 'WebServices/Requests/Resource/ResourceRequest.php');
require_once(ROOT_DIR . 'WebServices/Responses/Resource/ResourceCreatedResponse.php');
require_once(ROOT_DIR . 'WebServices/Responses/Resource/ResourceUpdatedResponse.php');

class ResourcesWriteWebService
{
	/**
	 * @var IRestServer
	 */
	private $server;

	/**
	 * @var IResourceSaveController
	 */
	private $controller;

	public function __construct(IRestServer $server, IResourceSaveController $controller)
	{
		$this->server = $server;
		$this->controller = $controller;
	}

	/**
	 * @name CreateResource
	 * @description Creates a new resource
	 * @request ResourceRequest
	 * @response ResourceCreatedResponse
	 * @return Response
	 */
	public function Create(Request $request, Response $response, $args)
	{
		$req = new ResourceRequest($this->server->GetRequest($request));

		Log::Debug('ResourcesWriteWebService.Create()', ['userId' => $this->server->GetSession()->UserId, 'request' => $req]);
		$result = $this->controller->Create($req, $this->server->GetSession());

		if ($result->WasSuccessful())
		{
			Log::Debug('ResourcesWriteWebService.Create() - Resource created', ['resourceId' => $result->ResourceId()]);

			return $this->server->WriteResponse(new ResourceCreatedResponse($this->server, $result->ResourceId()),
										 $response,RestResponse::CREATED_CODE);
		}
		else
		{
			Log::Debug('ResourcesWriteWebService.Create() - Resource create failed');

			return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()),
										 $response, RestResponse::BAD_REQUEST_CODE);
		}
	}

	/**
	 * @name UpdateResource
	 * @description Updates an existing resource
	 * @request ResourceRequest
	 * @response ResourceUpdatedResponse
	 * @param $resourceId
	 * @return Response
	 */
	public function Update(Request $request, Response $response, $args)
	{
	    $resourceId = $args['resourceId'];
        $req = new ResourceRequest($this->server->GetRequest($request));

		Log::Debug('ResourcesWriteWebService.Update()', ['userId' => $this->server->GetSession()->UserId, 'request' => $req]);

		$result = $this->controller->Update($resourceId, $req, $this->server->GetSession());

		if ($result->WasSuccessful())
		{
			Log::Debug('ResourcesWriteWebService.Update() - Resource Updated.',
					   ['resourceId' => $result->ResourceId()]);

			return $this->server->WriteResponse(new ResourceUpdatedResponse($this->server, $result->ResourceId()),
										 $response,RestResponse::OK_CODE);
		}
		else
		{
			Log::Debug('ResourcesWriteWebService.Update() - Resource Update Failed.');

			return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()),
										 $response,RestResponse::BAD_REQUEST_CODE);
		}
	}

	/**
	 * @name DeleteResource
	 * @description Deletes an existing resource
	 * @response DeletedResponse
	 * @param int $resourceId
	 * @return Response
	 */
	public function Delete(Request $request, Response $response, $args)
	{
	    $resourceId = $args['resourceId'];
		Log::Debug('ResourcesWriteWebService.Delete()', ['userId' => $this->server->GetSession()->UserId]);

		$result = $this->controller->Delete($resourceId, $this->server->GetSession());

		if ($result->WasSuccessful())
		{
			Log::Debug('ResourcesWriteWebService.Delete() - Resource Deleted.', ['resourceId' => $result->ResourceId()]);

			return $this->server->WriteResponse(new DeletedResponse(), $response,RestResponse::OK_CODE);
		}
		else
		{
			Log::Debug('ResourcesWriteWebService.Delete() - Resource Delete Failed.');

			return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()),
										 $response,RestResponse::BAD_REQUEST_CODE);
		}
	}
}

