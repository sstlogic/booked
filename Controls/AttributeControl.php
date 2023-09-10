<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'Controls/Control.php');

class AttributeControl extends Control
{
	public function __construct(SmartyPage $smarty)
	{
		parent::__construct($smarty);
	}

	public function PageLoad()
	{
		/** @var $attribute \Booked\Attribute|CustomAttribute */
		$attribute = $this->Get('attribute');

		if (is_a($attribute, 'CustomAttribute'))
		{
			$attributeVal = $this->Get('value');
			$attribute = new \Booked\Attribute($attribute, $attributeVal);
			$this->Set('attribute', $attribute);
		}
        else if (method_exists($attribute, "Value")) {
            $this->Set('value', $attribute->Value());
        }
//
//		$prefix = $this->Get('namePrefix');
//		$idPrefix = $this->Get('idPrefix');
//
//		$this->Set('attributeName', sprintf('%s%s[%s]', $prefix, FormKeys::ATTRIBUTE_PREFIX, $attribute->Id()));
//		$this->Set('attributeId', sprintf('%s%s%s', $idPrefix, FormKeys::ATTRIBUTE_PREFIX, $attribute->Id()));

        $readonly = $this->Get('readonly');
        $this->Set('readonly', isset($readonly) ? $readonly : false);

        $searchmode = $this->Get('searchmode');
        $this->Set('searchmode', isset($searchmode) ? $searchmode : false);

        $class = $this->Get('class');
        $this->Set('class', isset($class) ? $class : '');
        $this->Set('className', isset($class) ? $class : '');

        $prefix = $this->Get('prefix');
        $this->Set('prefix', $prefix . '');

        $this->Set('firstDayOfWeek', Configuration::Instance()->GetKey(ConfigKeys::FIRST_DAY_OF_WEEK, new IntConverter()));

//        $inputClass = $this->Get('inputClass');
//        $this->Set('inputClass', isset($inputClass) ? $inputClass : '');

        if ($readonly) {
            $this->Display('Controls/Attributes/ReadOnlyAttribute.tpl');
        }
        else {
            $this->Display('Controls/Attributes/ReactAttribute.tpl');
        }
	}
}