<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class GroupApiDto {
	/**
	 * @var int
	 */
	public $id;
	/**
	 * @var string
	 */
	public $name;

	/**
	 * @param GroupItemView[] $groups
	 * @return GroupApiDto[]
	 */
	public static function FromList($groups): array {
		$dtos = [];
		foreach ($groups as $group) {
			$dto = new GroupApiDto();
			$dto->id = intval($group->Id);
			$dto->name = apidecode($group->Name);

			$dtos[] = $dto;
		}
		return $dtos;
	}
}