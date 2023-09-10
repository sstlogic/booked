<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ResourcePermissionUpdateDto
{
	/**
	 * @var int
	 */
	public $id;
	/**
	 * @var ResourcePermissionApiDto[]
	 */
	public $userPermissions = [];
	/**
	 * @var ResourcePermissionApiDto[]
	 */
	public $groupPermissions = [];
	/**
	 * @var bool
	 */
	public $autoPermission;
}