<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/SecurePage.php');
require_once(ROOT_DIR . 'Pages/Reports/IDisplayableReportPage.php');
require_once(ROOT_DIR . 'Pages/Ajax/AutoCompletePage.php');
require_once(ROOT_DIR . 'Presenters/Reports/GenerateReportPresenter.php');
require_once(ROOT_DIR . 'Presenters/Reports/ReportCsvColumnView.php');

interface IGenerateReportPage extends IDisplayableReportPage, IActionPage
{
    /**
     * @return string
     */
    public function GetReportName();

    /**
     * @param array|BookableResource[] $resources
     */
    public function BindResources($resources);

    /**
     * @param array|AccessoryDto[] $accessories
     */
    public function BindAccessories($accessories);

    /**
     * @param array|Schedule[] $schedules
     */
    public function BindSchedules($schedules);

    /**
     * @param array|GroupItemView[] $groups
     */
    public function BindGroups($groups);

    /**
     * @param ResourceType[] $resourceTypes
     */
    public function BindResourceTypes($resourceTypes);

    /**
     * @return string
     */
    public function GetSelectedColumns();

    /**
     * @param UserDto[] $users
     */
    public function BindUsers($users);

    /**
     * @param CustomAttribute[] $customAttributes
     */
    public function BindAttributes($customAttributes);

    /**
     * @return int
     */
    public function GetReportId();

    /**
     * @param SavedReport|null $report
     */
    public function BindSavedReport($report);

    /**
     * @return int
     */
    public function GetUpdatingReportId();
}

class GenerateReportPage extends ActionPage implements IGenerateReportPage
{
    /**
     * @var GenerateReportPresenter
     */
    private $presenter;

    public function __construct()
    {
        parent::__construct('Reports', 1);
        $this->presenter = new GenerateReportPresenter(
            $this,
            ServiceLocator::GetServer()->GetUserSession(),
            new ReportingService(new ReportingRepository()),
            new ResourceRepository(),
            new ScheduleRepository(),
            new GroupRepository(),
            new UserRepository(),
            new AttributeRepository());
    }

    public function ProcessAction()
    {
        $this->presenter->ProcessAction();
    }

    public function ProcessDataRequest($dataRequest)
    {
        // no-op
    }

    public function ProcessPageLoad()
    {
        $this->Set('SavedReportName', '');
        $this->Set('SavedReportId', 0);

        $this->presenter->PageLoad();
        $this->Set('DateAxisFormat', Resources::GetInstance()->GetDateFormat('report_date'));
        $this->Display('Reports/generate-report.tpl');
    }

    public function BindReport(IReport $report, IReportDefinition $definition, $selectedColumns)
    {
        $this->Set('Definition', $definition);
        $this->Set('Report', $report);
        $this->Set('SelectedColumns', $selectedColumns);
    }

    public function BindResources($resources)
    {
        $this->Set('Resources', $resources);
    }

    public function BindResourceTypes($resourceTypes)
    {
        $this->Set('ResourceTypes', $resourceTypes);
    }

    public function BindAccessories($accessories)
    {
        $this->Set('Accessories', $accessories);
    }

    public function BindSchedules($schedules)
    {
        $this->Set('Schedules', $schedules);
    }

    public function GetReportName()
    {
        return $this->GetForm(FormKeys::REPORT_NAME);
    }

    private function GetValue($key)
    {
        $postValue = $this->GetForm($key);

        if (empty($postValue)) {
            return $this->GetQuerystring($key);
        }

        return $postValue;
    }

    public function ShowCsv()
    {
        $this->Set('ReportCsvColumnView', new ReportCsvColumnView($this->GetVar('SelectedColumns')));
        $this->DisplayCsv('Reports/custom-csv.tpl', 'report.csv');
    }

    public function DisplayError()
    {
        $this->Display('Reports/error.tpl');
    }

    public function ShowResults()
    {
        $this->Set('HideSave', false);
        $this->Display('Reports/results-custom.tpl');
    }

    public function PrintReport()
    {
        $this->Set('ReportCsvColumnView', new ReportCsvColumnView($this->GetVar('SelectedColumns')));
        $this->Display('Reports/print-custom-report.tpl');
    }

    public function BindGroups($groups)
    {
        $this->Set('Groups', $groups);
    }

    public function GetSelectedColumns()
    {
        return $this->GetForm(FormKeys::SELECTED_COLUMNS);
    }

    public function BindUsers($users)
    {
        $this->Set('Users', $users);
    }

    public function BindAttributes($customAttributes)
    {
        $this->Set('Attributes', $customAttributes);
    }

    public function GetReportId()
    {
        return $this->GetQuerystring(QueryStringKeys::REPORT_ID);
    }

    public function BindSavedReport($report)
    {
        if (!empty($report)) {
            $jsonReport = [
                "resourceIds" => array_map('intval', $report->ResourceIds()),
                "resourceTypeIds" =>  array_map('intval', $report->ResourceTypeIds()),
                "scheduleIds" =>  array_map('intval', $report->ScheduleIds()),
                "accessoryIds" =>  array_map('intval', $report->AccessoryIds()),
                "groupIds" =>  array_map('intval', $report->GroupIds()),
                "ownerIds" =>  array_map('intval', $report->Filter()->UserIds()),
                "coOwnerIds" =>  array_map('intval', $report->Filter()->CoOwnerIds()),
                "participantIds" => array_map('intval',  $report->Filter()->ParticipantIds()),
                "includeDeleted" => $report->IncludeDeleted(),
                "rangeStart" => $report->RangeStart()->ToIso(true),
                "rangeEnd" => $report->RangeEnd()->ToIso(true),
                "reportRange" => $report->Range()->__toString(),
                "reportSelection" => $report->Selection()->__toString(),
                "reportUsage" => $report->Usage()->__toString(),
                "reportGroupBy" => $report->GroupBy()->__toString(),
                "attributes" => array_map(function ($av) {
                    return ['id' => $av->AttributeId, 'value' => $av->Value];
                }, $report->AttributeValues()),
            ];
            $this->Set('SavedReport', json_encode($jsonReport));
            $this->Set("SavedReportId", intval($report->Id()));
            $this->Set("SavedReportName", $report->ReportName());
        }
    }

    public function GetUpdatingReportId()
    {
        return $this->GetForm(FormKeys::REPORT_ID);
    }
}

