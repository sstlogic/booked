<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

class RestServiceLink
{
	public $href;
	public $title;

	public function __construct($href, $title)
	{
		$this->href = $href;
		$this->title = $title;
	}
}
