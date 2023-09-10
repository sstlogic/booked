<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/WebService/namespace.php');

class CustomAttributeResponse extends RestResponse
{
	public $id;
	public $label;
	public $value;

	public function __construct(IRestServer $server, $attributeId, $attributeLabel, $attributeValue)
	{
		$this->id = intval($attributeId);
		$this->label = $attributeLabel;
		$this->value = apidecode($attributeValue);
		$this->AddService($server, WebServices::GetCustomAttribute, array(WebServiceParams::AttributeId => $attributeId));
	}

	public static function Example()
	{
		return new ExampleCustomAttributeResponse();
	}
}

class ExampleCustomAttributeResponse extends CustomAttributeResponse
{
	public function __construct()
	{
		$this->id = 123;
		$this->label = 'label';
		$this->value = 'value';
	}
}