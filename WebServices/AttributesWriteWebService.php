<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require_once(ROOT_DIR . 'lib/WebService/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Attributes/namespace.php');
require_once(ROOT_DIR . 'WebServices/Controllers/AttributeSaveController.php');
require_once(ROOT_DIR . 'WebServices/Responses/CustomAttributes/CustomAttributeCreatedResponse.php');
require_once(ROOT_DIR . 'WebServices/Requests/CustomAttributes/CustomAttributeRequest.php');

class AttributesWriteWebService
{
    /**
     * @var IAttributeSaveController
     */
    private $attributeController;

    /**
     * @var IRestServer
     */
    private $server;

    public function __construct(IRestServer $server, IAttributeSaveController $attributeController)
    {
        $this->server = $server;
        $this->attributeController = $attributeController;
    }

    /**
     * @name CreateCustomAttribute
     * @description Creates a new custom attribute.
     * Allowed values for type: 1 (single line),  2 (multi line), 3 (select list), 4 (checkbox), 5 (datetime), 6 (multi select), 7 (link/url)
     * Allowed values for categoryId: 1 (reservation), 2 (user), 4 (resource), 5 (resource type)
     * appliesToIds only allowed for category 2, 4, 5 and must match the id of corresponding entities
     * secondaryCategoryId and secondaryEntityIds only applies to category 1 and must match the id of the corresponding entities
     * @request CustomAttributeRequest
     * @response CustomAttributeCreatedResponse
     * @return Response
     */
    public function Create(Request $request, Response $response, $args)
    {
        /** @var $req CustomAttributeRequest */
        $req = $this->server->GetRequest($request);

        Log::Debug('AttributesWriteWebService.Create()', ['userId' => $this->server->GetSession()->UserId, 'request' => $request]);

        $result = $this->attributeController->Create($req, $this->server->GetSession());

        if ($result->WasSuccessful()) {
            Log::Debug('AttributesWriteWebService.Create() - Attribute Created.', ['attributeId' => $result->AttributeId()]);

            return $this->server->WriteResponse(new CustomAttributeCreatedResponse($this->server, $result->AttributeId()), $response, RestResponse::CREATED_CODE);
        } else {
            Log::Debug('AttributesWriteWebService.Create() - Create Failed.');

            return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()), $response, RestResponse::BAD_REQUEST_CODE);
        }
    }

    /**
     * @name UpdateCustomAttribute
     * @description Updates an existing custom attribute
     * Allowed values for type: 1 (single line),  2 (multi line), 3 (select list), 4 (checkbox), 5 (datetime), 6 (multi select), 7 (link/url)
     * Allowed values for categoryId: 1 (reservation), 2 (user), 4 (resource), 5 (resource type)
     * appliesToIds only allowed for category 2, 4, 5 and must match the id of corresponding entities
     * secondaryCategoryId and secondaryEntityIds only applies to category 1 and must match the id of the corresponding entities
     * @request CustomAttributeRequest
     * @response CustomAttributeUpdatedResponse
     * @param $attributeId
     * @return Response
     */
    public function Update(Request $request, Response $response, $args)
    {
        $attributeId = $args['attributeId'];
        /** @var $req CustomAttributeRequest */
        $req = $this->server->GetRequest($request);

        Log::Debug('AttributesWriteWebService.Update()', ['userId' => $this->server->GetSession()->UserId, 'attributeId' => $attributeId, 'request' => $request]);

        $result = $this->attributeController->Update($attributeId, $req, $this->server->GetSession());

        if ($result->WasSuccessful()) {
            Log::Debug('AttributesWriteWebService.Update() - Attribute Updated.', ['attributeId' => $result->AttributeId()]);

            $this->server->WriteResponse(new CustomAttributeUpdatedResponse($this->server, $result->AttributeId()), RestResponse::CREATED_CODE);
        } else {
            Log::Debug('AttributesWriteWebService.Update() - Update Failed.');

            return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()), $response, RestResponse::BAD_REQUEST_CODE);
        }
    }

    /**
     * @name DeleteCustomAttribute
     * @description Deletes an existing custom attribute
     * @response DeletedResponse
     * @param int $attributeId
     * @return Response
     */
    public function Delete(Request $request, Response $response, $args)
    {
        $attributeId = $args['attributeId'];
        Log::Debug('AttributesWriteWebService.Delete()', ['attributeId' => $attributeId, 'userId' => $this->server->GetSession()->UserId]);

        $result = $this->attributeController->Delete($attributeId, $this->server->GetSession());

        if ($result->WasSuccessful()) {
            Log::Debug('AttributesWriteWebService.Delete() - Attribute Deleted.', ['attributeId' => $result->AttributeId()]);

            return $this->server->WriteResponse(new DeletedResponse(), $response, RestResponse::OK_CODE);
        } else {
            Log::Debug('AttributesWriteWebService.Delete() - Attribute Delete Failed.');

            return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()), $response, RestResponse::BAD_REQUEST_CODE);
        }
    }
}
