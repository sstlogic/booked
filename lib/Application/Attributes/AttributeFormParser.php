<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

class AttributeFormElement
{
	/**
	 * @var int
	 */
	public $Id;

	/**
	 * @var mixed
	 */
	public $Value;

	public function __construct($id, $value)
	{
		$this->Id = $id;
		$this->Value = $value;
	}
}

class AttributeFormParser
{
	/**
	 * @static
	 * @param $attributes string|string[]|null The result of $this->GetForm(FormKeys::ATTRIBUTE_PREFIX)
	 * @return array|AttributeFormElement[]
	 */
	public static function GetAttributes($attributes)
	{
        if (is_array($attributes))
		{
			$af = array();

			foreach ($attributes as $id => $value)
			{
				$af[] = new AttributeFormElement($id, $value);
			}

            return $af;
		}

		return array();
	}
}