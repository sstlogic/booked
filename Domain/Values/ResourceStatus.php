<?php
/**
Copyright 2013-2023 Twinkle Toes Software, LLC
*/

class ResourceStatus
{
	const HIDDEN = 0;
	const AVAILABLE = 1;
	const UNAVAILABLE = 2;
}

class ResourceStatusReason
{
	/**
	 * @var string
	 */
	private $description;

	/**
	 * @var int|null
	 */
	private $id;

	/**
	 * @var int|ResourceStatus
	 */
	private $statusId;

	/**
	 * @param int|null $id
	 * @param int|ResourceStatus $statusId
	 * @param string|null $description
	 */
	public function __construct($id, $statusId, $description = null)
	{
		$this->description = $description;
		$this->id = $id;
		$this->statusId = $statusId;
	}

	/**
	 * @return int|null
	 */
	public function Id()
	{
		return $this->id;
	}

	/**
	 * @return int|ResourceStatus
	 */
	public function StatusId()
	{
		return $this->statusId;
	}

	/**
	 * @return string
	 */
	public function Description()
	{
		return $this->description;
	}
}