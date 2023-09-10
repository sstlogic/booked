<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

class IntConverter implements IConvert
{
    public function Convert($value)
	{
        if (empty($value) || trim($value . '') == "") {
            return 0;
        }
		return intval($value);
	}
}