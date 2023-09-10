<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
*/

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
require_once(ROOT_DIR . 'lib/WebService/RestResponse.php');
require_once(ROOT_DIR . 'Domain/Values/WebService/WebServiceUserSession.php');

interface IRestServer
{
	/**
	 * @return mixed
	 */
	public function GetRequest(Request $request);

	/**
	 * @param RestResponse $restResponse
	 * @param int $statusCode
	 * @return Response
	 */
	public function WriteResponse(RestResponse $restResponse, Response $response, $statusCode = 200);

	/**
	 * @param string $serviceName
	 * @param array $params
	 * @return string
	 */
	public function GetServiceUrl($serviceName, $params = array());

	/**
	 * @return string
	 */
	public function GetUrl();

	/**
	 * @param string $serviceName
	 * @param array $params
	 * @return string
	 */
	public function GetFullServiceUrl($serviceName, $params = array());

	/**
     * @param Request $request
	 * @param string $headerName
	 * @return string|null
	 */
	public function GetHeader(Request $request, $headerName);

	/**
	 * @param WebServiceUserSession $session
	 * @return void
	 */
	public function SetSession(WebServiceUserSession $session);

	/**
	 * @return WebServiceUserSession|null
	 */
	public function GetSession();

	/**
	 * @param Request $request
	 * @param string $queryStringKey
	 * @return string|null
	 */
	public function GetQueryString(Request $request, $queryStringKey);
}