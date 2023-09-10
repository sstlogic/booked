<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
require_once(ROOT_DIR . 'lib/WebService/namespace.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'WebServices/Responses/AccessoriesResponse.php');
require_once(ROOT_DIR . 'WebServices/Responses/AccessoryResponse.php');

class AccessoriesWebService
{
	/**
	 * @var IRestServer
	 */
	private $server;

	/**
	 * @var IResourceRepository
	 */
	private $resourceRepository;

	/**
	 * @var IAccessoryRepository
	 */
	private $accessoryRepository;

	public function __construct(IRestServer $server, IResourceRepository $resourceRepository,
								IAccessoryRepository $accessoryRepository)
	{
		$this->server = $server;
		$this->resourceRepository = $resourceRepository;
		$this->accessoryRepository = $accessoryRepository;
	}

	/**
	 * @name GetAllAccessories
	 * @description Loads all accessories. CreditApplicability of 1 is per slot, 2 is per reservation
	 * @response AccessoriesResponse
	 * @return Response
	 */
	public function GetAll(Request $request, Response $response)
	{
		$accessories = $this->resourceRepository->GetAccessoryList();
		return $this->server->WriteResponse(new AccessoriesResponse($this->server, $accessories), $response);
	}

	/**
	 * @name GetAccessory
	 * @description Loads a specific accessory by id. CreditApplicability of 1 is per slot, 2 is per reservation
	 * @param int $accessoryId
	 * @response AccessoryResponse
	 * @return Response
	 */
	public function GetAccessory(Request $request, Response $response, $args)
	{
	    $accessoryId = $args['accessoryId'];
		$accessory = $this->accessoryRepository->LoadById($accessoryId);

		if (empty($accessory))
		{
			return $this->server->WriteResponse(RestResponse::NotFound(), $response,RestResponse::NOT_FOUND_CODE);
		}
		else
		{
			return $this->server->WriteResponse(new AccessoryResponse($this->server, $accessory), $response);
		}
	}
}
