<?php
/**
Copyright 2013-2023 Twinkle Toes Software, LLC
 */

class DeletedResponse extends RestResponse
{
	public function __construct()
	{
		$this->message = 'The item was deleted';
	}

	public static function Example()
	{
		return new DeletedResponse();
	}
}

