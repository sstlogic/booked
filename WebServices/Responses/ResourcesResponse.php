<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/WebService/namespace.php');

class ResourcesResponse extends RestResponse
{
	/**
	 * @var array|ResourceResponse[]
	 */
	public $resources;

	/**
	 * @param IRestServer $server
	 * @param array|BookableResource[] $resources
	 * @param IEntityAttributeList $attributes
	 */
	public function __construct(IRestServer $server, $resources, $attributes)
	{
		foreach ($resources as $resource)
		{
			$this->resources[] = new ResourceResponse($server, $resource, $attributes);
		}
	}

	public static function Example()
	{
		return new ExampleResourcesResponse();
	}
}

class ExampleResourcesResponse extends ResourcesResponse
{
	public function __construct()
	{
		$this->resources = array(ResourceResponse::Example());
	}
}