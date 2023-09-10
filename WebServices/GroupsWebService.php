<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require_once(ROOT_DIR . 'lib/WebService/namespace.php');
require_once(ROOT_DIR . 'WebServices/Responses/Group/GroupResponse.php');
require_once(ROOT_DIR . 'WebServices/Responses/Group/GroupsResponse.php');

class GroupsWebService
{
	/**
	 * @var IRestServer
	 */
	private $server;

	/**
	 * @var IGroupRepository
	 */
	private $groupRepository;

	/**
	 * @var IGroupViewRepository
	 */
	private $groupViewRepository;

	public function __construct(IRestServer $server, IGroupRepository $groupRepository,
								IGroupViewRepository $groupViewRepository)
	{
		$this->server = $server;
		$this->groupRepository = $groupRepository;
		$this->groupViewRepository = $groupViewRepository;
	}

	/**
	 * @name GetAllGroups
	 * @description Loads all groups
	 * @response GroupsResponse
	 * @return Response
	 */
	public function GetGroups(Request $request, Response $response, $args)
	{
		$pageable = $this->groupViewRepository->GetList(null, null);
		$groups = $pageable->Results();

		return $this->server->WriteResponse(new GroupsResponse($this->server, $groups), $response);
	}

	/**
	 * @name GetGroup
	 * @description Loads a specific group by id
	 * @response GroupResponse
	 * @param int $groupId
	 * @return Response
	 */
	public function GetGroup(Request $request, Response $response, $args)
	{
	    $groupId = $args['groupId'];
		$group = $this->groupRepository->LoadById($groupId);

		if ($group != null)
		{
			return $this->server->WriteResponse(new GroupResponse($this->server, $group), $response);
		}
		else
		{
			return $this->server->WriteResponse(RestResponse::NotFound(), $response,RestResponse::NOT_FOUND_CODE);
		}
	}
}

