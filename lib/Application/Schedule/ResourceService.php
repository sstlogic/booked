<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Application/Attributes/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Schedule/ScheduleResourceFilter.php');

interface IResourceService
{
    /**
     * @param int $scheduleId
     * @param bool $includeInaccessibleResources
     * @param UserSession $user
     * @param ScheduleResourceFilter|null $filter
     * @return array|ResourceDto[]
     */
    public function GetScheduleResources($scheduleId, $includeInaccessibleResources, UserSession $user, $filter = null);

    /**
     * @param int|null $scheduleId
     * @param bool $includeInaccessibleResources
     * @param UserSession $user
     * @return array|BookableResource[]
     */
    public function GetScheduleBookableResources($scheduleId, $includeInaccessibleResources, UserSession $user);

    /**
     * @param bool $includeInaccessibleResources
     * @param UserSession $user
     * @param ScheduleResourceFilter|null $filter
     * @param null $pageNumber
     * @param null $pageSize
     * @return array|ResourceDto[]
     */
    public function GetAllResources($includeInaccessibleResources, UserSession $user, $filter = null, $pageNumber = null, $pageSize = null);

    /**
     * @param $includeInaccessibleResources
     * @param UserSession $user
     * @return array|ResourceDto[]
     */
    public function GetRecentlyUsedAvailableResources($includeInaccessibleResources, UserSession $user);

    /**
     * @param UserSession $user
     * @return array|ResourceDto[]
     */
    public function GetFavoriteResources(UserSession $user);

    /**
     * @return Accessory[]
     */
    public function GetAccessories();

    /**
     * @return Accessory
     */
    public function GetAccessoryById($id);

    /**
     * @param int $scheduleId
     * @param UserSession $user
     * @return ResourceGroupTree
     */
    public function GetResourceGroups($scheduleId, UserSession $user);

    /**
     * @return ResourceGroup[]
     */
    public function GetResourceGroupList();

    /**
     * @return ResourceType[]
     */
    public function GetResourceTypes();

    /**
     * @return Attribute[]
     */
    public function GetResourceAttributes(UserSession $userSession, $resourceIds = array());

    /**
     * @return Attribute[]
     */
    public function GetResourceTypeAttributes(UserSession $userSession);

    /**
     * @param int|string $resourceId
     * @return BookableResource
     */
    public function GetResource($resourceId);

    /**
     * @param int[] $resourceIds
     * @return BookableResource[]
     */
    public function GetResources($resourceIds);
}

class ResourceService implements IResourceService
{
    /**
     * @var IResourceRepository
     */
    private $_resourceRepository;

    /**
     * @var IPermissionService
     */
    private $_permissionService;

    /**
     * @var IAttributeService
     */
    private $_attributeService;

    /**
     * @var IUserRepository
     */
    private $_userRepository;

    /**
     * @var IAccessoryRepository
     */
    private $_accessoryRepository;

    public function __construct(IResourceRepository  $resourceRepository,
                                IPermissionService   $permissionService,
                                IAttributeService    $attributeService,
                                IUserRepository      $userRepository,
                                IAccessoryRepository $accessoryRepository)
    {
        $this->_resourceRepository = $resourceRepository;
        $this->_permissionService = $permissionService;
        $this->_attributeService = $attributeService;
        $this->_userRepository = $userRepository;
        $this->_accessoryRepository = $accessoryRepository;
    }

    /**
     * @return ResourceService
     */
    public static function Create()
    {
        return new ResourceService(new ResourceRepository(),
            PluginManager::Instance()->LoadPermission(),
            new AttributeService(new AttributeRepository()),
            new UserRepository(), new AccessoryRepository());
    }

    public function GetScheduleBookableResources($scheduleId, $includeInaccessibleResources, UserSession $user)
    {
        if (!empty($scheduleId)) {
            $resources = $this->_resourceRepository->GetScheduleResources($scheduleId);
        } else {
            $resources = $this->_resourceRepository->GetResourceList();
        }

        $allowedResources = array();

        $permissionFilter = new ResourcePermissionFilter($this->_permissionService, $user);
        $statusFilter = new ResourceStatusFilter($this->_userRepository, $user);

        foreach ($resources as $resource) {
            $canAccess = $permissionFilter->ShouldInclude($resource);

            if (!$includeInaccessibleResources && !$canAccess) {
                continue;
            }

            if ($canAccess) {
                $canAccess = $statusFilter->ShouldInclude($resource);
                if (!$includeInaccessibleResources && !$canAccess) {
                    continue;
                }
            }

            $allowedResources[] = $resource;
        }
        return $allowedResources;
    }

    public function GetScheduleResources($scheduleId, $includeInaccessibleResources, UserSession $user, $filter = null)
    {
        if ($filter == null) {
            $filter = new ScheduleResourceFilter();
        }

        $resources = $this->_resourceRepository->GetScheduleResources($scheduleId);
        $resourceIds = $filter->FilterResources($resources, $this->_resourceRepository, $this->_attributeService, $user);

        return $this->Filter($resources, $user, $includeInaccessibleResources, $resourceIds);
    }

    public function GetAllResources($includeInaccessibleResources, UserSession $user, $filter = null, $pageNumber = null, $pageSize = null)
    {
        if ($filter == null) {
            $filter = new ScheduleResourceFilter();
        }

        if ($pageNumber != null || $pageSize != null) {
            $resources = $this->_resourceRepository->GetList($pageNumber, $pageSize, null, null, $filter->AsSqlFilter());
            $resources = $resources->Results();
        } else {
            if (!empty($filter->ScheduleId)) {
                $resources = $this->_resourceRepository->GetScheduleResources($filter->ScheduleId);
            } else {
                $resources = $this->_resourceRepository->GetResourceList();
            }
        }
        $resourceIds = $filter->FilterResources($resources, $this->_resourceRepository, $this->_attributeService, $user);

        return $this->Filter($resources, $user, $includeInaccessibleResources, $resourceIds);
    }

    /**
     * @param $resources array|BookableResource[]
     * @param $user UserSession
     * @param $includeInaccessibleResources bool
     * @param int[] $resourceIds
     * @return array|ResourceDto[]
     */
    private function Filter($resources, $user, $includeInaccessibleResources, $resourceIds = null)
    {
        $filter = new ResourcePermissionFilter($this->_permissionService, $user);
        $statusFilter = new ResourceStatusFilter($this->_userRepository, $user);

        $resourceDtos = array();
        foreach ($resources as $resource) {
            if (is_array($resourceIds) && !in_array($resource->GetId(), $resourceIds)) {
                continue;
            }

            if ($resource->IsHidden()) {
                continue;
            }

            $canAccess = $filter->ShouldInclude($resource);

            if (!$includeInaccessibleResources && !$canAccess) {
                continue;
            }

            if ($canAccess) {
                $canAccess = $statusFilter->ShouldInclude($resource);
                if (!$includeInaccessibleResources && !$canAccess) {
                    continue;
                }
            }

            $resourceDtos[] = new ResourceDto($resource->GetResourceId(),
                $resource->GetName(),
                $canAccess,
                $canAccess && $filter->CanBook($resource),
                $resource->GetScheduleId(),
                $resource->GetMinLength(),
                $resource->GetResourceTypeId(),
                $resource->GetAdminGroupId(),
                $resource->GetScheduleAdminGroupId(),
                $resource->GetStatusId(),
                $resource->GetRequiresApproval(),
                $resource->IsCheckInEnabled(),
                $resource->IsAutoReleased(),
                $resource->GetAutoReleaseMinutes(),
                $resource->GetColor(),
                $resource->GetMaxConcurrentReservations(),
                $resource->GetRequiredRelationships(),
                $resource->GetPublicId(),
                $resource->GetResourceGroupIds(),
                $resource->GetAutoReleaseAction(),);
        }

        return $resourceDtos;
    }

    public function GetAccessories()
    {
        return $this->_accessoryRepository->LoadAll();
    }

    public function GetAccessoryById($id)
    {
        return $this->_accessoryRepository->LoadById($id);
    }

    public function GetResourceGroups($scheduleId, UserSession $user)
    {
        $filter = new CompositeResourceFilter();
        if (!Configuration::Instance()->GetSectionKey(ConfigSection::SCHEDULE, ConfigKeys::SCHEDULE_SHOW_INACCESSIBLE_RESOURCES, new BooleanConverter())) {
            $filter->Add(new ResourcePermissionFilter($this->_permissionService, $user));
        }
        $filter->Add(new ResourceStatusFilter($this->_userRepository, $user));

        $groups = $this->_resourceRepository->GetResourceGroups($scheduleId, $filter);

        return $groups;
    }

    public function GetResourceGroupList()
    {
        return $this->_resourceRepository->GetResourceGroupsList();
    }

    public function GetResourceTypes()
    {
        return $this->_resourceRepository->GetResourceTypes();
    }


    public function GetResourceAttributes(UserSession $userSession, $resourceIds = array())
    {
        $customAttributes = $this->_attributeService->GetByCategory(CustomAttributeCategory::RESOURCE);
        $attributes = [];
        foreach ($customAttributes as $ca) {
            if (!$ca->IsPrivate() && !$ca->AdminOnly() && $ca->AppliesToEntity($resourceIds)) {
                $attributes[] = new \Booked\Attribute($ca);
            }
        }

        return $attributes;
    }

    public function GetResourceTypeAttributes(UserSession $userSession)
    {
        $customAttributes = $this->_attributeService->GetByCategory(CustomAttributeCategory::RESOURCE_TYPE);
        $attributes = [];
        foreach ($customAttributes as $ca) {
            if (!$ca->IsPrivate() && !$ca->AdminOnly()) {
                $attributes[] = new \Booked\Attribute($ca);
            }
        }

        return $attributes;
    }

    public function GetResource($resourceId)
    {
        if (!is_int($resourceId) && !ctype_digit($resourceId)) {
            return $this->_resourceRepository->LoadByPublicId($resourceId);
        }
        return $this->_resourceRepository->LoadById($resourceId);
    }

    public function GetRecentlyUsedAvailableResources($includeInaccessibleResources, UserSession $user)
    {
        $resources = $this->_resourceRepository->GetRecentlyUsed($user->UserId);
        return $this->Filter($resources, $user, $includeInaccessibleResources);
    }

    public function GetFavoriteResources(UserSession $user)
    {
        $resources = $this->_resourceRepository->GetFavorites($user->UserId);
        return $this->Filter($resources, $user, false);
    }

    public function GetResources($resourceIds)
    {
        return $this->_resourceRepository->LoadByIds($resourceIds);
    }
}

class PublicOnlyResourceService extends ResourceService
{
    public function GetScheduleResources($scheduleId, $includeInaccessibleResources, UserSession $user, $filter = null)
    {
        return parent::GetScheduleResources($scheduleId, $includeInaccessibleResources, $user, new PublicOnlyResourceFilter($filter));
    }

    public function GetAllResources($includeInaccessibleResources, UserSession $user, $filter = null, $pageNumber = null, $pageSize = null)
    {
        return parent::GetAllResources($includeInaccessibleResources, $user, new PublicOnlyResourceFilter($filter), $pageNumber, $pageSize);
    }
}

class PublicOnlyResourceFilter extends ScheduleResourceFilter
{
    /**
     * @var ScheduleResourceFilter|null
     */
    private $filter;

    /**
     * @param $filter ScheduleResourceFilter|null
     */
    public function __construct($filter)
    {
        $this->filter = $filter;
        parent::__construct();
    }

    public function FilterResources($resources, IResourceRepository $resourceRepository, IAttributeService $attributeService, UserSession $user)
    {
        $filtered = array_filter($resources, function (BookableResource $r) {
            return $r->GetIsCalendarSubscriptionAllowed();
        });

        if (!empty($this->filter)) {
            return $this->filter->FilterResources($filtered, $resourceRepository, $attributeService, $user);
        }

        return array_map(function (BookableResource $r) {
            return $r->GetId();
        }, $filtered);
    }

    public function HasFilter()
    {
        return true;
    }

    public function AsSqlFilter()
    {
        $hasPublicId = new SqlFilterNotNull(ColumnNames::PUBLIC_ID);

        if (!empty($this->filter)) {
            $filter = $this->filter->AsSqlFilter();
            return $filter->_And($hasPublicId);
        } else {
            return $hasPublicId;
        }
    }
}