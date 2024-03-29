<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Application/Reporting/IReport.php');
require_once(ROOT_DIR . 'lib/Application/Reporting/IReportColumns.php');
require_once(ROOT_DIR . 'lib/Application/Reporting/IReportData.php');

require_once(ROOT_DIR . 'lib/Application/Reporting/ReportDefinition.php');
require_once(ROOT_DIR . 'lib/Application/Reporting/GeneratedSavedReport.php');
require_once(ROOT_DIR . 'lib/Application/Reporting/CustomReport.php');
require_once(ROOT_DIR . 'lib/Application/Reporting/CustomReportData.php');
require_once(ROOT_DIR . 'Domain/Values/Report_Filter.php');
require_once(ROOT_DIR . 'Domain/Values/Report_GroupBy.php');
require_once(ROOT_DIR . 'Domain/Values/Report_Range.php');
require_once(ROOT_DIR . 'Domain/Values/Report_ResultSelection.php');
require_once(ROOT_DIR . 'Domain/Values/Report_Usage.php');
require_once(ROOT_DIR . 'lib/Application/Reporting/ReportColumns.php');
require_once(ROOT_DIR . 'lib/Application/Reporting/ReportingService.php');
require_once(ROOT_DIR . 'lib/Application/Reporting/CannedReport.php');
require_once(ROOT_DIR . 'lib/Application/Reporting/ReportUtilizationData.php');
