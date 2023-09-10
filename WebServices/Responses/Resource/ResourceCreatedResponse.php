<?php
/**
Copyright 2013-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/WebService/namespace.php');

class ResourceCreatedResponse extends RestResponse
{
	public $resourceId;

	public function __construct(IRestServer $server, $resourceId)
	{
		$this->resourceId = $resourceId;
		$this->AddService($server, WebServices::GetResource, array(WebServiceParams::ResourceId => $resourceId));
		$this->AddService($server, WebServices::UpdateResource, array(WebServiceParams::ResourceId => $resourceId));
	}

	public static function Example()
	{
		return new ExampleResourceCreatedResponse();
	}
}

class ExampleResourceCreatedResponse extends ResourceCreatedResponse
{
	public function __construct()
	{
		$this->resourceId = 1;
		$this->AddLink('http://url/to/resource', WebServices::GetResource);
		$this->AddLink('http://url/to/update/resource', WebServices::UpdateResource);
	}
}

