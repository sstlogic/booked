<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ResourceRelationshipUpdateDto {
	/**
	 * @var int
	 */
	public $id;
	/**
	 * @var int[]
	 */
	public $required;
    /**
	 * @var int[]
	 */
	public $requiredOneWay;
	/**
	 * @var int[]
	 */
	public $excluded;
	/**
	 * @var int[]
	 */
	public $excludedAtTime;
}