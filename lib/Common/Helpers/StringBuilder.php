<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

class StringBuilder
{
	private $_string = array();

	public function Append($string)
	{
		$this->_string[] = $string;
	}

	public function AppendLine($string = '')
	{
		$this->_string[] = $string . "\n";
	}

	public function PrependLine($string = '')
	{
		array_unshift($this->_string, $string . "\n");
	}

	public function Count()
	{
		return count($this->_string);
	}

	public function ToString($glue = '')
	{
		return join($glue, $this->_string);
	}
}