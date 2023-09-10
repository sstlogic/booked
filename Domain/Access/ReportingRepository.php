<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'Domain/Access/ReportCommandBuilder.php');
require_once(ROOT_DIR . 'Domain/SavedReport.php');

interface IReportingRepository
{
    /**
     * @param ReportCommandBuilder $commandBuilder
     * @return array
     */
    public function GetCustomReport(ReportCommandBuilder $commandBuilder);

    /**
     * @param SavedReport $savedReport
     */
    public function SaveCustomReport(SavedReport $savedReport);

    /**
     * @param int $userId
     * @return array|SavedReport[]
     */
    public function LoadSavedReportsForUser($userId);

    /**
     * @param int $reportId
     * @param int $userId
     * @return SavedReport
     */
    public function LoadSavedReportForUser($reportId, $userId);

    /**
     * @return array|SavedReport[]
     */
    public function LoadScheduledSavedReports();

    /**
     * @param int $reportId
     * @param int $userId
     */
    public function DeleteSavedReport($reportId, $userId);

    /**
     * @param SavedReport $savedReport
     */
    public function UpdateSavedReport(SavedReport $savedReport);

    /**
     * @param int $reportId
     */
    public function DeleteCustomReportSchedule($reportId);
}

class ReportingRepository implements IReportingRepository
{
    public function GetCustomReport(ReportCommandBuilder $commandBuilder)
    {
        $query = $commandBuilder->Build();
        $reader = ServiceLocator::GetDatabase()->Query($query);
        $rows = [];
        while ($row = $reader->GetRow()) {
            if ($commandBuilder->GetIncludeDuration()) {
                $row[ColumnNames::DURATION_HOURS] = round($row[ColumnNames::DURATION_ALIAS] / 3600, 2);
            }
            if ($commandBuilder->GetIncludeTotalTime()) {
                $row[ColumnNames::DURATION_HOURS] = round($row[ColumnNames::TOTAL_TIME] / 3600, 2);
            }
            $rows[] = $row;
        }
        $reader->Free();

        return $rows;
    }

    public function SaveCustomReport(SavedReport $report)
    {
        $serialized = ReportSerializer::Serialize($report);
        ServiceLocator::GetDatabase()->ExecuteInsert(new AddSavedReportCommand($report->ReportName(), $report->OwnerId(), $report->DateCreated(), $serialized));
    }

    public function UpdateSavedReport(SavedReport $report)
    {
        $serializedConfiguration = ReportSerializer::Serialize($report);
        ServiceLocator::GetDatabase()->Execute(new UpdateSavedReportCommand($report->Id(), $report->ReportName(), $serializedConfiguration, $report->SerializedSchedule(), $report->LastSent()));
    }

    public function DeleteCustomReportSchedule($reportId)
    {
        ServiceLocator::GetDatabase()->Execute(new DeleteSavedReportScheduleCommand($reportId));
    }

    public function LoadSavedReportsForUser($userId)
    {
        $reader = ServiceLocator::GetDatabase()->Query(new GetAllSavedReportsForUserCommand($userId));
        $reports = array();
        while ($row = $reader->GetRow()) {
            $reports[] = SavedReport::FromDatabase(
                $row[ColumnNames::REPORT_NAME],
                $row[ColumnNames::USER_ID],
                Date::FromDatabase($row[ColumnNames::DATE_CREATED]),
                $row[ColumnNames::REPORT_DETAILS],
                $row[ColumnNames::REPORT_ID],
                $row[ColumnNames::REPORT_SCHEDULE],
                Date::FromDatabase($row[ColumnNames::REPORT_LAST_SENT]),
            );
        }
        $reader->Free();

        return $reports;
    }

    public function LoadScheduledSavedReports()
    {
        $reader = ServiceLocator::GetDatabase()->Query(new GetAllSavedReportsScheduled());
        $reports = array();
        while ($row = $reader->GetRow()) {
            $reports[] = SavedReport::FromDatabase(
                $row[ColumnNames::REPORT_NAME],
                $row[ColumnNames::USER_ID],
                Date::FromDatabase($row[ColumnNames::DATE_CREATED]),
                $row[ColumnNames::REPORT_DETAILS],
                $row[ColumnNames::REPORT_ID],
                $row[ColumnNames::REPORT_SCHEDULE],
                Date::FromDatabase($row[ColumnNames::REPORT_LAST_SENT]),
            );
        }
        $reader->Free();

        return $reports;
    }

    public function LoadSavedReportForUser($reportId, $userId)
    {
        $reader = ServiceLocator::GetDatabase()->Query(new GetSavedReportForUserCommand($reportId, $userId));

        if ($row = $reader->GetRow()) {
            $reader->Free();
            return SavedReport::FromDatabase(
                $row[ColumnNames::REPORT_NAME],
                $row[ColumnNames::USER_ID],
                Date::FromDatabase($row[ColumnNames::DATE_CREATED]),
                $row[ColumnNames::REPORT_DETAILS],
                $row[ColumnNames::REPORT_ID],
                $row[ColumnNames::REPORT_SCHEDULE],
                Date::FromDatabase($row[ColumnNames::REPORT_LAST_SENT]),
            );
        }
        $reader->Free();
        return null;
    }

    public function DeleteSavedReport($reportId, $userId)
    {
        ServiceLocator::GetDatabase()->Execute(new DeleteSavedReportCommand($reportId, $userId));
    }
}