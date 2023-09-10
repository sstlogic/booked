<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ResourceGroupUpdateDto
{
	/**
	 * @var int
	 */
	public $id;
	/**
	 * @var string[]
	 */
	public $groupIds;
	/**
	 * @var ResourceGroupApiDto
	 */
	public $addedGroups;
}