<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Application/Reporting/namespace.php');

class CustomReportData implements IReportData
{
	private $rows;

	public function __construct($rows)
	{
		$this->rows = $rows;
	}

	public function Rows()
	{
		return $this->rows;
	}
}