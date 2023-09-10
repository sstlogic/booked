<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ResourcePermissionApiDto
{
	/**
	 * @var int
	 */
	public $id;
	/**
	 * @var int
	 */
	public $permissionType;

	/**
	 * @param UserPermissionItemView[] $users
	 * @return ResourcePermissionApiDto[]
	 */
	public static function FromUserList($users): array
	{
		$dtos = [];
		foreach ($users as $user)
		{
			$dto = new ResourcePermissionApiDto();
			$dto->id = intval($user->Id);
			$dto->permissionType = intval($user->PermissionType);
			$dtos[] = $dto;
		}
		return $dtos;
	}

	/**
	 * @param GroupPermissionItemView[] $groups
	 * @return ResourcePermissionApiDto[]
	 */
	public static function FromGroupList($groups): array
	{
		$dtos = [];
		foreach ($groups as $group)
		{
			$dto = new ResourcePermissionApiDto();
			$dto->id = intval($group->Id);
			$dto->permissionType = intval($group->PermissionType);
            $dtos[] = $dto;
		}
		return $dtos;
	}
}