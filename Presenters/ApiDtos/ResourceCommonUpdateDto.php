<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ResourceCommonUpdateDto
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
	 * @var int|null
	 */
	public $typeId;
	/**
	 * @var string|null
	 */
	public $newType;
	/**
	 * @var int|null
	 */
	public $adminId;
	/**
	 * @var int|null
	 */
	public $capacity;
    /**
	 * @var int|null
	 */
	public $minParticipants;
	/**
	 * @var string
	 */
	public $location;
	/**
	 * @var string
	 */
	public $contact;
	/**
	 * @var string
	 */
	public $description;
    /**
	 * @var string
	 */
	public $notes;
}
