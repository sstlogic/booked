<?php
/**
Copyright 2013-2023 Twinkle Toes Software, LLC
 */

class JsonRequest
{
	public function __construct($jsonObject = null)
	{
		$this->Hydrate($jsonObject);
	}

	private function Hydrate($jsonObject)
	{
		if (empty($jsonObject))
		{
			return;
		}

		foreach ($jsonObject as $key => $value)
		{
			$this->$key = $value;
		}
	}
}