<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

class ReservationAccessoryRequest
{
	public $accessoryId;
	public $quantityRequested;

	public function __construct($accessoryId, $quantityRequested)
	{
		$this->accessoryId = $accessoryId;
		$this->quantityRequested = $quantityRequested;
	}
}

