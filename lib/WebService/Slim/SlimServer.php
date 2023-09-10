<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

//require_once(ROOT_DIR . 'lib/external/Slim/Slim.php');
require_once(ROOT_DIR . 'lib/WebService/IRestServer.php');
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class SlimServer implements IRestServer
{
	/**
	 * @var Slim\App
	 */
	private $slim;

	/**
	 * @var WebServiceUserSession
	 */
	private $session;

	public function __construct(Slim\App $slim)
	{
		$this->slim = $slim;
	}

	public function GetRequest(Request $request)
	{
		return json_decode($request->getBody());
	}

	public function WriteResponse(RestResponse $restResponse, Response $response, $statusCode = 200)
	{
        $newResponse = $response->withAddedHeader('Content-Type', 'application/json');
        $newResponse->withStatus($statusCode);
        $newResponse->getBody()->write(json_encode($restResponse));
        unset($restResponse);
        return $newResponse;
	}

	public function GetServiceUrl($serviceName, $params = array())
	{
        $parser = $this->slim->getRouteCollector()->getRouteParser();
		$url = $parser->urlFor($serviceName, $params, []);
		foreach($params as $k => $v) {
		    $url = str_replace(":$k", $v, $url);
        }

		return $url;
	}

	public function GetUrl()
	{
        $https = isset($_SERVER['HTTPS']);
	    return ($https ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
	}

	public function GetFullServiceUrl($serviceName, $params = array())
	{
		return $this->GetUrl() . $this->GetServiceUrl($serviceName, $params);
	}

	public function GetHeader(Request $request, $headerName)
	{
        $header = $request->getHeader($headerName);
        if (empty($header) || !is_array($header)) {
            return "";
        }
        return $header[0];
	}

	public function SetSession(WebServiceUserSession $session)
	{
		$this->session = $session;
	}

	public function GetSession()
	{
		return empty($this->session) ? new WebServiceUserSession(BookedStringHelper::Random()) : $this->session;
	}

	/**
	 * @param string $queryStringKey
	 * @return string|null
	 */
	public function GetQueryString(Request $request, $queryStringKey)
	{
        $queryParams = $request->getQueryParams();
        return isset($queryParams[$queryStringKey]) ? $queryParams[$queryStringKey] : null;
	}
}