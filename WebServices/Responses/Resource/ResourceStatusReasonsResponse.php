<?php
/**
Copyright 2013-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'lib/WebService/namespace.php');

class ResourceStatusReasonsResponse extends RestResponse
{
	public $reasons = array();

	/**
	 * @param IRestServer $server
	 * @param ResourceStatusReason[] $reasons
	 */
	public function __construct(IRestServer $server, $reasons)
	{
		foreach($reasons as $reason)
		{
			$this->AddReason($reason->Id(), apidecode($reason->Description()), $reason->StatusId());
		}
	}

	protected function AddReason($id, $description, $statusId)
	{
		$this->reasons[] = ['id' => $id, 'description' => $description, 'statusId' => $statusId];
	}

	public static function Example()
	{
		return new ExampleResourceStatusReasonsResponse();
	}
}

class ExampleResourceStatusReasonsResponse extends ResourceStatusReasonsResponse
{
	public function __construct()
	{
		$this->AddReason(1, 'reason description', ResourceStatus::UNAVAILABLE);
	}
}