<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

class AttributeValueRequest
{
	public $attributeId;
	public $attributeValue;

	public function __construct($attributeId, $attributeValue)
	{
		$this->attributeId = $attributeId;
		$this->attributeValue = $attributeValue;
	}

	public static function Example()
	{
		return new AttributeValueRequest(1, 'attribute value');
	}
}