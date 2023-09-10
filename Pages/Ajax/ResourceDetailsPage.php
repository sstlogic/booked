<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/SecurePage.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Attributes/namespace.php');

class ResourceDetailsPage extends Page implements IResourceDetailsPage
{
    /**
     * @var \ResourceDetailsPresenter
     */
    private $presenter;

    public function __construct()
    {
        parent::__construct('', 1);
        $this->presenter = new ResourceDetailsPresenter($this, new ResourceRepository(), new AttributeService(new AttributeRepository()), new ScheduleRepository());
    }

    public function PageLoad()
    {
        $this->presenter->PageLoad();

        $this->smarty->display('Ajax/resourcedetails.tpl');
    }

    public function BindResource(BookableResource $resource)
    {
        $this->Set('resourceName', $resource->GetName());
        $this->Set('description', $resource->GetDescription());
        $this->Set('notes', $resource->GetNotes());
        $this->Set('contactInformation', $resource->GetContact());
        $this->Set('locationInformation', $resource->GetLocation());
        $this->Set('allowMultiday', $resource->GetAllowMultiday());
        $this->Set('minimumDuration', $resource->GetMinLength());
        $this->Set('maximumDuration', $resource->GetMaxLength());

        $this->Set('maxParticipants', $resource->GetMaxParticipants());
        $this->Set('maximumNotice', $resource->GetMaxNotice());
        $this->Set('minimumNotice', $resource->GetMinNoticeAdd());
        $this->Set('requiresApproval', $resource->GetRequiresApproval());
        $this->Set('autoAssign', $resource->GetAutoAssign());
        $this->Set('color', $resource->GetColor());
        $this->Set('textColor', $resource->GetTextColor());
        $this->Set('autoReleaseMinutes', $resource->GetAutoReleaseMinutes());
        $this->Set('isCheckInEnabled', $resource->IsCheckInEnabled());
        $this->Set('creditsEnabled', Configuration::Instance()->GetSectionKey(ConfigSection::CREDITS, ConfigKeys::CREDITS_ENABLED, new BooleanConverter()));
        $this->Set('peakCredits', $resource->GetPeakCredits());
        $this->Set('offPeakCredits', $resource->GetCredits());
        $this->Set('requiredResources', $resource->GetRequiredRelationships());
        $this->Set('excludedResources', $resource->GetExcludedRelationships());
        $this->Set('excludedTimeResources', $resource->GetExcludedTimeRelationships());

        $this->Set('resourceType', '');
        $this->Set('imageUrl', '');
        $this->Set('images', '');
        $this->Set('ResourceTypeAttributes', []);

        if ($resource->HasImage()) {
            $this->Set('imageUrl', $resource->GetImage());
            $this->Set('images', $resource->GetImages());
        }
    }

    public function BindAttributes($attributes)
    {
        $this->Set('Attributes', $attributes);
    }

    public function GetResourceId()
    {
        return ServiceLocator::GetServer()->GetQuerystring(QueryStringKeys::RESOURCE_ID);
    }

    /**
     * @param ResourceType $resourceType
     * @param Attribute[] $attributes
     */
    public function BindResourceType(ResourceType $resourceType, $attributes)
    {
        $this->Set('resourceType', $resourceType->Name());
        $this->Set('ResourceTypeAttributes', $attributes);
    }

    public function BindRelationships($relationships)
    {
        $this->Set('relationships', $relationships);
    }

    public function BindSchedule(Schedule $schedule)
    {
       $this->Set('scheduleName', $schedule->GetName());
    }
}

interface IResourceDetailsPage
{
    /**
     * @param BookableResource $resource
     */
    public function BindResource(BookableResource $resource);

    /**
     * @param Schedule $schedule
     */
    public function BindSchedule(Schedule $schedule);

    /**
     * @param Attribute[] $attributes
     */
    public function BindAttributes($attributes);

    /**
     * @param ResourceType $resourceType
     * @param Attribute[] $attributes
     */
    public function BindResourceType(ResourceType $resourceType, $attributes);

    /**
     * @return int
     */
    public function GetResourceId();

    public function BindRelationships($relationships);
}

class ResourceDetailsPresenter
{
    /**
     * @var ResourceRepository
     */
    private $resourceRepository;

    /**
     * @var IResourceDetailsPage
     */
    private $page;

    /**
     * @var IAttributeService
     */
    private $attributeService;
    /**
     * @var IScheduleRepository
     */
    private $scheduleRepository;

    /**
     * @param IResourceDetailsPage $page
     * @param IResourceRepository $resourceRepository
     * @param IAttributeService $attributeService
     */
    public function __construct(IResourceDetailsPage $page, IResourceRepository $resourceRepository, IAttributeService $attributeService, IScheduleRepository $scheduleRepository)
    {
        $this->page = $page;
        $this->resourceRepository = $resourceRepository;
        $this->attributeService = $attributeService;
        $this->scheduleRepository = $scheduleRepository;
    }

    public function PageLoad()
    {
        $user = ServiceLocator::GetServer()->GetUserSession();
        if ($user->IsLoggedIn() ||
            Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY,
                ConfigKeys::PRIVACY_VIEW_SCHEDULES,
                new BooleanConverter())) {

            $resourceId = $this->page->GetResourceId();
            $resource = $this->resourceRepository->LoadById($resourceId);
            $this->page->BindResource($resource);

            $schedule = $this->scheduleRepository->LoadById($resource->GetScheduleId());
            $this->page->BindSchedule($schedule);

            $attributeList = $this->attributeService->GetAttributes(CustomAttributeCategory::RESOURCE, $user, $resourceId);
            $this->page->BindAttributes($attributeList->GetAttributes($resourceId));

            if ($resource->HasResourceType()) {
                $resourceType = $this->resourceRepository->LoadResourceType($resource->GetResourceTypeId());
                $attributeList = $this->attributeService->GetAttributes(CustomAttributeCategory::RESOURCE_TYPE, $user, $resource->GetResourceTypeId());

                $this->page->BindResourceType($resourceType, $attributeList->GetAttributes($resource->GetResourceTypeId()));
            }

            $relationships = [];
            if (count($resource->GetRequiredRelationships()) > 0 || count($resource->GetExcludedRelationships()) > 0) {
                $pageInfo = $this->resourceRepository->GetList(null, null, null, null, new SqlFilterIn('`r`.`resource_id`', array_merge($resource->GetRequiredRelationships(), $resource->GetExcludedRelationships())));
                $results = $pageInfo->Results();
                /** @var BookableResource $r */
                foreach($results as $r) {
                    $relationships[$r->GetId()] = $r->GetName();
                }
            }
            $this->page->BindRelationships($relationships);
        }
    }
}

