<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ResourceDurationUpdateDto
{
	/**
	 * @var int
	 */
	public $id;
	/**
	 * @var TimeIntervalApiDto|null
	 */
	public $minDuration;
	/**
	 * @var TimeIntervalApiDto|null
	 */
	public $maxDuration;
	/**
	 * @var TimeIntervalApiDto|null
	 */
	public $buffer;
	/**
	 * @var bool
	 */
	public $allowCrossDay;
}
