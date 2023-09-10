<?php

/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

class ReportColumns implements IReportColumns
{
	private $knownColumns = array();
	private $attributeColumns = array();

	/**
	 * @param $columnName string
	 */
	public function Add($columnName)
	{
		$this->knownColumns[] = $columnName;
	}

	/**
	 * @param $attributeTypeId int|CustomAttributeCategory
	 * @param $attributeId int
	 * @param $label string
	 */
	public function AddAttribute($attributeTypeId, $attributeId, $label)
	{
		$this->attributeColumns[] = new AttributeReportColumn("{$attributeTypeId}attribute{$attributeId}", $label);
	}

	public function Exists($columnName)
	{
		return in_array($columnName, $this->knownColumns);
	}

	/**
	 * @return string[]
	 */
	public function GetAll()
	{
		return $this->knownColumns;
	}

	/**
	 * @return string[]
	 */
	public function GetCustomAttributes()
	{
		return $this->attributeColumns;
	}
}