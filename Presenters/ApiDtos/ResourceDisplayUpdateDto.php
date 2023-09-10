<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ResourceDisplayUpdateDto
{
	/**
	 * @var int
	 */
	public $id;
	/**
	 * @var int
	 */
	public $scheduleId;
	/**
	 * @var string
	 */
	public $name;
	/**
	 * @var int|null
	 */
	public $sortOrder;
	/**
	 * @var string|null
	 */
	public $slotLabel;
    /**
     * @var bool
     */
	public $showToPublic = false;
	/**
	 * @var string|null
	 */
	public $color;
}
