<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require_once(ROOT_DIR . 'lib/WebService/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Attributes/namespace.php');
require_once(ROOT_DIR . 'WebServices/Responses/CustomAttributes/CustomAttributesResponse.php');

class AttributesWebService
{
	/**
	 * @var IRestServer
	 */
	private $server;

	/**
	 * @var IAttributeService
	 */
	private $attributeService;

	public function __construct(IRestServer $server, IAttributeService $attributeService)
	{
		$this->server = $server;
		$this->attributeService = $attributeService;
	}

	/**
	 * @name GetCategoryAttributes
	 * @description Gets all custom attribute definitions for the requested category
	 * Categories are RESERVATION = 1, USER = 2, RESOURCE = 4
	 * @response CustomAttributesResponse
	 * @return Response
	 * @param int $categoryId
	 */
	public function GetAttributes(Request $request, Response $response, $args)
	{
	    $categoryId = $args['categoryId'];
		$attributes = $this->attributeService->GetByCategory($categoryId);

		return $this->server->WriteResponse(new CustomAttributesResponse($this->server, $attributes), $response);
	}

	/**
	 * @name GetAttribute
	 * @description Gets all custom attribute definitions for the requested attribute
	 * @response CustomAttributeDefinitionResponse
	 * @return Response
	 * @param int $attributeId
	 */
	public function GetAttribute(Request $request, Response $response, $args)
	{
	    $attributeId = $args['attributeId'];
		$attribute = $this->attributeService->GetById($attributeId);

		if ($attribute != null)
		{
			return $this->server->WriteResponse(new CustomAttributeDefinitionResponse($this->server, $attribute), $response);
		}
		else
		{
			return $this->server->WriteResponse(RestResponse::NotFound(), $response,RestResponse::NOT_FOUND_CODE);
		}
	}
}

