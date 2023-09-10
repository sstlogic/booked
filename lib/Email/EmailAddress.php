<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

class EmailAddress
{
	private $address;
	private $name;

	public function Address()
	{
		return $this->address;
	}

	public function Name()
	{
		return $this->name;
	}

	public function __construct($address, $name = '')
	{
		$this->address = $address;
		$this->name = $name;
	}

    public function __toString()
    {
        return "{$this->address}<{$this->name}>";
    }
}
