<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

class BooleanConverter implements IConvert
{
	public function Convert($value)
	{
		return self::ConvertValue($value);
	}

	/**
	 * @param mixed $value
	 * @return bool
	 */
	public static function ConvertValue($value)
	{
        if (empty($value)) {
            return false;
        }
		return $value === true || strtolower(trim($value . '')) == 'true' || $value === 1 || trim($value . '') === '1';
	}
}