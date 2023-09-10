<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

interface IReport
{
	/**
	 * @abstract
	 * @return IReportColumns
	 */
	public function GetColumns();

	/**
	 * @abstract
	 * @return IReportData
	 */
	public function GetData();

	/**
	 * @abstract
	 * @return int
	 */
	public function ResultCount();
}