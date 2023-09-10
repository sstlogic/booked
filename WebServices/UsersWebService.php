<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require_once(ROOT_DIR . 'lib/WebService/namespace.php');
require_once(ROOT_DIR . 'lib/Application/User/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Attributes/namespace.php');
require_once(ROOT_DIR . 'WebServices/Responses/UsersResponse.php');
require_once(ROOT_DIR . 'WebServices/Responses/UserResponse.php');
require_once(ROOT_DIR . 'WebServices/Responses/LanguageResponse.php');
require_once(ROOT_DIR . 'WebServices/Responses/PhoneCountryCodesResponse.php');
require_once(ROOT_DIR . 'Domain/Values/CountryCodes.php');

class UsersWebService
{
    /**
     * @var IRestServer
     */
    private $server;

    /**
     * @var IUserRepositoryFactory
     */
    private $repositoryFactory;

    /**
     * @var IAttributeService
     */
    private $attributeService;

    public function __construct(IRestServer       $server, IUserRepositoryFactory $repositoryFactory,
                                IAttributeService $attributeService)
    {
        $this->server = $server;
        $this->repositoryFactory = $repositoryFactory;
        $this->attributeService = $attributeService;
    }

    /**
     * @name GetAllUsers
     * @description Loads all users that the current user can see.
     * Optional query string parameters: username, email, firstName, lastName, phone, organization, position and any custom attributes.
     * If searching on custom attributes, the query string parameter has to be in the format att#=value.
     * For example, Users/?att1=ExpectedAttribute1Value
     * @response UsersResponse
     * @return Response
     */
    public function GetUsers(Request $request, Response $response, $args)
    {
        $session = $this->server->GetSession();
        $attributeList = $this->attributeService->GetAttributes(CustomAttributeCategory::USER, $session);
        $attributes = $attributeList->GetDefinitions();

        $filter = $this->GetUserFilter($request, $attributes);

        $repository = $this->repositoryFactory->Create($session);
        $data = $repository->GetList(null, null, null, null, $filter->GetFilter(), AccountStatus::ACTIVE);

        $attributeLabels = array();
        foreach ($attributes as $attribute) {
            $attributeLabels[$attribute->Id()] = $attribute->Label();
        }

        $usersResponse = new UsersResponse($this->server, $data->Results(), $attributeLabels);

        unset($data);
        unset($attributeLabels);

        return $this->server->WriteResponse($usersResponse, $response);
    }

    /**
     * @name GetUser
     * @description Loads the requested user by Id
     * @response UserResponse
     * @param int $userId
     * @return Response
     */
    public function GetUser(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];
        $responseCode = RestResponse::OK_CODE;

        $hideUsers = Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY,
            ConfigKeys::PRIVACY_HIDE_USER_DETAILS,
            new BooleanConverter());
        $userSession = $this->server->GetSession();

        $repository = $this->repositoryFactory->Create($userSession);
        $user = $repository->LoadById($userId);

        $loadedUserId = $user->Id();
        if (empty($loadedUserId)) {
            return $this->server->WriteResponse(RestResponse::NotFound(), $response, RestResponse::NOT_FOUND_CODE);
        }

        $attributes = $this->attributeService->GetAttributes(CustomAttributeCategory::USER, $userSession, array($userId));

        if ($userId == $userSession->UserId || !$hideUsers || $userSession->IsAdmin) {
            $resp = new UserResponse($this->server, $user, $attributes);
        } else {
            $me = $repository->LoadById($userSession->UserId);

            if ($me->IsAdminFor($user)) {
                $resp = new UserResponse($this->server, $user, $attributes);
            } else {
                $resp = RestResponse::Unauthorized();
                $responseCode = RestResponse::UNAUTHORIZED_CODE;
            }
        }

        return $this->server->WriteResponse($resp, $response, $responseCode);
    }

    /**
     * @name GetLanguages
     * @description All supported languages
     * @response LanguageResponse
     * @return Response
     */
    public function GetLanguages(Request $request, Response $response, $args)
    {
        return $this->server->WriteResponse(new LanguageResponse(), $response, RestResponse::OK_CODE);
    }

    /**
     * @name PhoneCountryCodes
     * @description All supported phone country codes
     * @response PhoneCountryCodesResponse
     * @return Response
     */
    public function GetPhoneCountryCodes(Request $request, Response $response, $args)
    {
        return $this->server->WriteResponse(new PhoneCountryCodesResponse(), $response, RestResponse::OK_CODE);
    }

    /**
     * @param CustomAttribute[] $attributes
     * @return UserFilter
     */
    private function GetUserFilter(Request $request, $attributes)
    {
        $attributeFilters = array();
        foreach ($attributes as $attribute) {
            $attributeValue = $this->server->GetQueryString($request, WebServiceQueryStringKeys::ATTRIBUTE_PREFIX . $attribute->Id());
            if (!empty($attributeValue)) {
                $attributeFilters[] = new \Booked\Attribute($attribute, $attributeValue);
            }
        }

        $filter = new UserFilter(
            $this->server->GetQueryString($request, WebServiceQueryStringKeys::USERNAME),
            $this->server->GetQueryString($request, WebServiceQueryStringKeys::EMAIL),
            $this->server->GetQueryString($request, WebServiceQueryStringKeys::FIRST_NAME),
            $this->server->GetQueryString($request, WebServiceQueryStringKeys::LAST_NAME),
            $this->server->GetQueryString($request, WebServiceQueryStringKeys::PHONE),
            $this->server->GetQueryString($request, WebServiceQueryStringKeys::ORGANIZATION),
            $this->server->GetQueryString($request, WebServiceQueryStringKeys::POSITION),
            $attributeFilters
        );

        return $filter;
    }
}