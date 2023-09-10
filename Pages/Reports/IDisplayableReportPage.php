<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

interface IDisplayableReportPage
{
	public function BindReport(IReport $report, IReportDefinition $definition, $selectedColumns);

	public function DisplayError();

	public function ShowResults();

	public function PrintReport();

	public function ShowCsv();
}

