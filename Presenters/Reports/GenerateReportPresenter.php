<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/Reports/ReportActions.php');
require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'lib/Application/Reporting/namespace.php');
require_once(ROOT_DIR . 'Presenters/ApiDtos/namespace.php');

class GenerateReportPresenter extends ActionPresenter
{
    /**
     * @var IGenerateReportPage
     */
    private $page;
    /**
     * @var UserSession
     */
    private $user;
    /**
     * @var IReportingService
     */
    private $reportingService;
    /**
     * @var IResourceRepository
     */
    private $resourceRepo;
    /**
     * @var IScheduleRepository
     */
    private $scheduleRepo;
    /**
     * @var IGroupRepository
     */
    private $groupRepo;
    /**
     * @var IUserRepository
     */
    private $userRepository;
    /**
     * @var IAttributeRepository
     */
    private $attributeRepository;

    /**
     * @param IGenerateReportPage $page
     * @param UserSession $user
     * @param IReportingService $reportingService
     * @param IResourceRepository $resourceRepo
     * @param IScheduleRepository $scheduleRepo
     * @param IGroupViewRepository $groupRepo
     * @param IUserRepository $userRepository
     * @param IAttributeRepository $attributeRepository
     */
    public function __construct(
        IGenerateReportPage  $page,
        UserSession          $user,
        IReportingService    $reportingService,
        IResourceRepository  $resourceRepo,
        IScheduleRepository  $scheduleRepo,
        IGroupViewRepository $groupRepo,
        IUserRepository      $userRepository,
        IAttributeRepository $attributeRepository)
    {
        parent::__construct($page);
        $this->page = $page;
        $this->user = $user;
        $this->reportingService = $reportingService;
        $this->resourceRepo = $resourceRepo;
        $this->scheduleRepo = $scheduleRepo;
        $this->groupRepo = $groupRepo;
        $this->userRepository = $userRepository;
        $this->attributeRepository = $attributeRepository;

        $this->AddAction(ReportActions::PrintReport, 'PrintReport');
        $this->AddAction(ReportActions::Csv, 'ExportToCsv');
        $this->AddAction(ReportActions::Save, 'SaveReport');
        $this->AddAction(ReportActions::SaveColumns, 'SaveColumns');
        $this->AddAction(ReportActions::GenerateApi, 'GenerateCustomReportApi');
        $this->AddAction(ReportActions::Update, 'UpdateReport');
    }

    public function PageLoad()
    {
        $this->page->BindResources($this->resourceRepo->GetResourceList());
        $this->page->BindResourceTypes($this->resourceRepo->GetResourceTypes());
        $this->page->BindAccessories($this->resourceRepo->GetAccessoryList());
        $this->page->BindSchedules($this->scheduleRepo->GetAll());
        $this->page->BindGroups($this->groupRepo->GetList()->Results());
        $this->page->BindUsers($this->userRepository->GetAll());
        $this->page->BindAttributes($this->attributeRepository->GetByCategory(CustomAttributeCategory::RESERVATION));

        $reportId = $this->page->GetReportId();
        if (!empty($reportId)) {
            $report = $this->reportingService->GetSavedReport($this->user->UserId, $reportId);
            $this->page->BindSavedReport($report);
        }
    }

    public function ProcessAction()
    {
        try {
            parent::ProcessAction();
        } catch (Exception $ex) {
            Log::Error('Error getting report', ['exception' => $ex]);
            $this->page->DisplayError();
        }
    }

    public function PrintReport()
    {
        $this->BindReportFromApi(ServiceLocator::GetServer()->GetSession('ReportFilter'));
        $this->page->PrintReport();
    }

    public function GenerateCustomReportApi()
    {
        $reportFilterDto = ReportFilterDto::FromJson($this->page->GetJsonPost());
        ServiceLocator::GetServer()->SetSession('ReportFilter', $reportFilterDto);

        $this->BindReportFromApi($reportFilterDto);
        $this->page->ShowResults();
    }

    public function ExportToCsv()
    {
        $this->BindReportFromApi(ServiceLocator::GetServer()->GetSession('ReportFilter'));
        $this->page->ShowCsv();
    }

    public function SaveReport()
    {
        $reportName = $this->page->GetReportName();
        /** @var ReportFilterDto $reportFilterDto */
        $reportFilterDto = ServiceLocator::GetServer()->GetSession('ReportFilter');

        $usage = new Report_Usage($reportFilterDto->reportUsage);
        $selection = new Report_ResultSelection($reportFilterDto->reportSelection);
        $groupBy = new Report_GroupBy($reportFilterDto->reportGroupBy);
        $range = new Report_Range($reportFilterDto->reportRange, $reportFilterDto->rangeStart, $reportFilterDto->rangeEnd, $this->user->Timezone);
        $filter = new Report_Filter($reportFilterDto->resourceIds, $reportFilterDto->scheduleIds, $reportFilterDto->ownerIds, $reportFilterDto->groupIds, $reportFilterDto->accessoryIds, $reportFilterDto->participantIds, $reportFilterDto->includeDeleted, $reportFilterDto->resourceTypeIds, $this->GetAttributeValues($reportFilterDto), $reportFilterDto->coOwnerIds);

        $userId = $this->user->UserId;

        $this->reportingService->Save($reportName, $userId, $usage, $selection, $groupBy, $range, $filter);
    }

    public function UpdateReport()
    {
        $reportName = $this->page->GetReportName();
        $reportId = $this->page->GetUpdatingReportId();
        /** @var ReportFilterDto $reportFilterDto */
        $reportFilterDto = ServiceLocator::GetServer()->GetSession('ReportFilter');

        $usage = new Report_Usage($reportFilterDto->reportUsage);
        $selection = new Report_ResultSelection($reportFilterDto->reportSelection);
        $groupBy = new Report_GroupBy($reportFilterDto->reportGroupBy);
        $range = new Report_Range($reportFilterDto->reportRange, $reportFilterDto->rangeStart, $reportFilterDto->rangeEnd, $this->user->Timezone);
        $filter = new Report_Filter($reportFilterDto->resourceIds, $reportFilterDto->scheduleIds, $reportFilterDto->ownerIds, $reportFilterDto->groupIds, $reportFilterDto->accessoryIds, $reportFilterDto->participantIds, $reportFilterDto->includeDeleted, $reportFilterDto->resourceTypeIds, $this->GetAttributeValues($reportFilterDto), $reportFilterDto->coOwnerIds);

        $userId = $this->user->UserId;

        Log::Debug("Updating saved report", ['reportName' => $reportName, 'reportId' => $reportId]);

        $this->reportingService->Update($reportId, $reportName, $userId, $usage, $selection, $groupBy, $range, $filter);
    }

    public function SaveColumns()
    {
        $user = $this->userRepository->LoadById($this->user->UserId);
        $user->ChangePreference(UserPreferences::REPORT_COLUMNS, $this->page->GetSelectedColumns());
        $this->userRepository->Update($user);
    }

    /**
     * @param ReportFilterDto $reportFilterDto
     */
    private function BindReportFromApi($reportFilterDto)
    {
        $range = new Report_Range($reportFilterDto->reportRange, $reportFilterDto->rangeStart, $reportFilterDto->rangeEnd, $this->user->Timezone);
        $filter = new Report_Filter($reportFilterDto->resourceIds, $reportFilterDto->scheduleIds, $reportFilterDto->ownerIds, $reportFilterDto->groupIds, $reportFilterDto->accessoryIds, $reportFilterDto->participantIds, $reportFilterDto->includeDeleted, $reportFilterDto->resourceTypeIds, $this->GetAttributeValues($reportFilterDto), $reportFilterDto->coOwnerIds);
        $report = $this->reportingService->GenerateCustomReport(
            new Report_Usage($reportFilterDto->reportUsage),
            new Report_ResultSelection($reportFilterDto->reportSelection),
            new Report_GroupBy($reportFilterDto->reportGroupBy),
            $range,
            $filter,
            $this->user->Timezone);
        $reportDefinition = new ReportDefinition($report, $this->user->Timezone);

        $user = $this->userRepository->LoadById($this->user->UserId);

        $this->page->BindReport($report, $reportDefinition, $user->GetPreference(UserPreferences::REPORT_COLUMNS));
    }

    /**
     * @param ReportFilterDto $reportFilterDto
     * @return AttributeValue[]
     */
    private function GetAttributeValues(ReportFilterDto $reportFilterDto)
    {
        /** @var AttributeValue[] $av */
        $av = [];
        /** @var AttributeValueApiDto $a */
        foreach ($reportFilterDto->attributes as $a) {
            $av[] = new AttributeValue($a->id, $a->value);
        }

        return $av;
    }
}