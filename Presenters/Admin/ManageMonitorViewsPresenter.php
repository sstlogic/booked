<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'Pages/Admin/ManageMonitorViewsPage.php');
require_once(ROOT_DIR . 'Presenters/ApiDtos/namespace.php');

class ManageMonitorViewsPresenter extends ActionPresenter
{
    /**
     * @var IManageMonitorViewsPage
     */
    private $page;
    /**
     * @var IScheduleRepository
     */
    private $scheduleRepository;
    /**
     * @var IResourceRepository
     */
    private $resourceRepository;
    /**
     * @var IMonitorViewRepository
     */
    private $monitorViewRepository;
    /**
     * @var IAttributeService
     */
    private $attributeService;

    public function __construct(
        IManageMonitorViewsPage $page,
        IScheduleRepository $scheduleRepository,
        IResourceRepository $resourceRepository,
        IMonitorViewRepository $monitorViewRepository,
        IAttributeService $attributeService)
    {
        parent::__construct($page);
        $this->page = $page;
        $this->scheduleRepository = $scheduleRepository;
        $this->resourceRepository = $resourceRepository;
        $this->monitorViewRepository = $monitorViewRepository;
        $this->attributeService = $attributeService;

        $this->AddApi('load', 'ApiLoad');
        $this->AddApi('add', 'ApiAdd');
        $this->AddApi('update', 'ApiUpdate');
        $this->AddApi('delete', 'ApiDelete');
    }

    public function ApiLoad(): ApiActionResult
    {
        $schedules = ScheduleApiDto::FromList($this->scheduleRepository->GetAll());
        $resources = ResourceApiDto::FromList($this->resourceRepository->GetResourceList());
        $attributes = AttributeApiDto::FromList($this->attributeService->GetByCategory(CustomAttributeCategory::RESERVATION));
        $views = MonitorViewApiDto::FromList($this->monitorViewRepository->GetAll());
        $types = ResourceTypeApiDto::FromList($this->resourceRepository->GetResourceTypes());
        $groups = ResourceGroupApiDto::FromList($this->resourceRepository->GetResourceGroupsList());

        return new ApiActionResult(true,
            [
                'schedules' => $schedules,
                'resources' => $resources,
                'reservationAttributes' => $attributes,
                'monitorViews' => $views,
                'resourceGroups' => $groups,
                'types' => $types,
                ]);
    }

    public function ApiAdd($json): ApiActionResult
    {
        /** @var MonitorViewApiDto $view */
        $view = $json;

        $view = MonitorView::Create(trim($view->name), $this->GetSettingsApi($view->settings));
        $added = $this->monitorViewRepository->Add($view);

        Log::Debug("Added new monitor view.", ['name' => $view->Name()]);

        return new ApiActionResult(true, MonitorViewApiDto::Create($added));
    }

    public function ApiUpdate($json): ApiActionResult
    {
        /** @var MonitorViewApiDto $view */
        $view = $json;

        $existing = $this->monitorViewRepository->LoadByPublicId($view->publicId);
        $existing->SetName($view->name);
        $existing->SetSettings($this->GetSettingsApi($view->settings));
        $this->monitorViewRepository->Update($existing);

        Log::Debug("Updated monitor view.", ['name' => $existing->Name()]);

        return new ApiActionResult(true, MonitorViewApiDto::Create($existing));
    }

    public function ApiDelete($json): ApiActionResult
    {
        $id = $json->id;

        $existing = $this->monitorViewRepository->DeleteByPublicId($id);

        Log::Debug("Deleted monitor view.", ['id' => $json->id]);

        return new ApiActionResult(true, ["id" => $id]);
    }

    /**
     * @return MonitorViewSettings
     * @var MonitorViewSettings $jsonSettings
     */
    private function GetSettingsApi($jsonSettings)
    {
        $settings = new MonitorViewSettings();
        $settings->announcement = trim($jsonSettings->announcement . '');
        $style = intval($jsonSettings->style);
        $settings->style = $style;
        $settings->scrollInterval = max(30, intval($jsonSettings->scrollInterval));
        $settings->showReservations = BooleanConverter::ConvertValue($jsonSettings->showReservations);
        $settings->showLogo = BooleanConverter::ConvertValue($jsonSettings->showLogo);
        $settings->showDateTime = BooleanConverter::ConvertValue($jsonSettings->showDateTime);
        $settings->consolidateReservations = BooleanConverter::ConvertValue($jsonSettings->consolidateReservations);
        $settings->title = trim($jsonSettings->title . '');

        $settings->reservationsToShow = intval($jsonSettings->reservationsToShow);
        $settings->resourcesToShow = empty($jsonSettings->resourcesToShow) ?  MonitorViewResources::Resources : intval($jsonSettings->resourcesToShow);

        $settings->scheduleId = intval($jsonSettings->scheduleId);
        $settings->resourceIds = [];
        if ($settings->resourcesToShow == MonitorViewResources::Resources) {
            $rids = empty($jsonSettings->resourceIds) ? [] : array_map('intval', $jsonSettings->resourceIds);
            $resourceIds = [];
            foreach ($rids as $resourceId) {
                $resource = $this->resourceRepository->LoadById($resourceId);
                if ($resource->GetScheduleId() == $settings->scheduleId) {
                    $resourceIds[] = $resourceId;
                }
            }
            $settings->resourceIds = $resourceIds;
        }

        $settings->resourceTypeIds = [];
        if ($settings->resourcesToShow == MonitorViewResources::Types) {
            $settings->resourceTypeIds = empty($jsonSettings->resourceTypeIds) ? [] : array_map('intval', $jsonSettings->resourceTypeIds);
        }

        $settings->resourceGroupIds = [];
        if ($settings->resourcesToShow == MonitorViewResources::Groups) {
            $settings->resourceGroupIds = empty($jsonSettings->resourceGroupIds) ? [] : array_map('intval', $jsonSettings->resourceGroupIds);
        }

        if ($settings->reservationsToShow == MonitorViewReservations::DateRange) {
            $settings->startDate = Date::ParseExact($jsonSettings->startDate)->Format('Y-m-d');
            $settings->endDate = Date::ParseExact($jsonSettings->endDate)->Format('Y-m-d');
        }
        if ($settings->reservationsToShow == MonitorViewReservations::Days) {
            $settings->days = intval($jsonSettings->days);
        }
        if ($settings->reservationsToShow == MonitorViewReservations::Count) {
            $settings->count = intval($jsonSettings->count);
        }
        if ($settings->reservationsToShow == MonitorViewReservations::MatchingAttribute) {
            $settings->attributeId = intval($jsonSettings->attributeId);
            $settings->attributeValue = trim($jsonSettings->attributeValue);
        }
        $settings->pageSize = null;
        if ($settings->style == MonitorViewStyle::List) {
            $settings->pageSize = empty($jsonSettings->pageSize) ? 3 : intval($jsonSettings->pageSize);
        }

        return $settings;
    }
}
