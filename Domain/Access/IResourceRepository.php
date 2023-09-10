<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

interface IResourceRepository
{
    /**
     * @param int $scheduleId
     * @return array|BookableResource[]
     */
    public function GetScheduleResources($scheduleId);

    /**
     * @param int $resourceId
     * @return BookableResource
     */
    public function LoadById($resourceId);

    /**
     * @param string $publicId
     * @return BookableResource
     */
    public function LoadByPublicId($publicId);

    /**
     * @param string $resourceName
     * @return BookableResource
     */
    public function LoadByName($resourceName);

    /**
     * @param BookableResource $resource
     * @return int ID of created resource
     */
    public function Add(BookableResource $resource);

    /**
     * @param BookableResource $resource
     */
    public function Update(BookableResource $resource);

    /**
     * @param BookableResource $resource
     */
    public function Delete(BookableResource $resource);

    /**
     * @return array|BookableResource[] array of all resources
     */
    public function GetResourceList();

    /**
     * @param int $pageNumber
     * @param int $pageSize
     * @param string|null $sortField
     * @param string|null $sortDirection
     * @param ISqlFilter $filter
     * @return PageableData|BookableResource[]
     */
    public function GetList($pageNumber, $pageSize, $sortField = null, $sortDirection = null, $filter = null);

    /**
     * @param null|string $sortField
     * @param null|string $sortDirection
     * @return AccessoryDto[]|array all accessories
     */
    public function GetAccessoryList($sortField = null, $sortDirection = null);

    /**
     * @param int|null $scheduleId
     * @param IResourceFilter|null $resourceFilter
     * @return ResourceGroupTree
     */
    public function GetResourceGroups($scheduleId = null, $resourceFilter = null);

    /**
     * @return ResourceGroup[]
     */
    public function GetResourceGroupsList();

    /**
     * @param int $resourceId
     * @param int $groupId
     */
    public function AddResourceToGroup($resourceId, $groupId);

    /**
     * @param int $resourceId
     * @param int $groupId
     */
    public function RemoveResourceFromGroup($resourceId, $groupId);

    /**
     * @param ResourceGroup $group
     * @return ResourceGroup
     */
    public function AddResourceGroup(ResourceGroup $group);

    /**
     * @param int $groupId
     * @return ResourceGroup
     */
    public function LoadResourceGroup($groupId);

    /**
     * @param string $publicResourceGroupId
     * @return ResourceGroup
     */
    public function LoadResourceGroupByPublicId($publicResourceGroupId);

    /**
     * @param ResourceGroup $group
     */
    public function UpdateResourceGroup(ResourceGroup $group);

    /**
     * @param int $groupId
     * @param int[] $resourceIds
     */
    public function UpdateResourceGroupAssignments($groupId, $resourceIds);

    /**
     * @param $groupId
     */
    public function DeleteResourceGroup($groupId);

    /**
     * @return ResourceType[]|array
     */
    public function GetResourceTypes();

    /**
     * @param int $resourceTypeId
     * @return ResourceType
     */
    public function LoadResourceType($resourceTypeId);

    /**
     * @param ResourceType $type
     * @return int
     */
    public function AddResourceType(ResourceType $type);

    /**
     * @param ResourceType $type
     */
    public function UpdateResourceType(ResourceType $type);

    /**
     * @param int $id
     */
    public function RemoveResourceType($id);

    /**
     * @return ResourceStatusReason[]
     */
    public function GetStatusReasons();

    /**
     * @param int $statusId
     * @param string $reasonDescription
     * @return int
     */
    public function AddStatusReason($statusId, $reasonDescription);

    /**
     * @param int $reasonId
     * @param string $reasonDescription
     */
    public function UpdateStatusReason($reasonId, $reasonDescription);

    /**
     * @param int $reasonId
     */
    public function RemoveStatusReason($reasonId);

    /**
     * @param int $resourceId
     * @param int|null $pageNumber
     * @param int|null $pageSize
     * @param ISqlFilter|null $filter
     * @param int $accountStatus
     * @return PageableData|UserPermissionItemView[]
     */
    public function GetUsersWithPermission($resourceId, $pageNumber = null, $pageSize = null, $filter = null, $accountStatus = AccountStatus::ACTIVE);

    /**
     * @param int $resourceId
     * @param int|null $pageNumber
     * @param int|null $pageSize
     * @param ISqlFilter|null $filter
     * @param int $accountStatus
     * @return PageableData|UserPermissionItemView[]
     */
    public function GetUsersWithPermissionsIncludingGroups($resourceId, $pageNumber = null, $pageSize = null, $filter = null, $accountStatus = AccountStatus::ACTIVE);

    /**
     * @param int $resourceId
     * @param int|null $pageNumber
     * @param int|null $pageSize
     * @param ISqlFilter|null $filter
     * @return PageableData|GroupPermissionItemView[]
     */
    public function GetGroupsWithPermission($resourceId, $pageNumber = null, $pageSize = null, $filter = null);

    /**
     * @param int $resourceId
     * @param int $userId
     * @param int $type
     */
    public function ChangeResourceUserPermission($resourceId, $userId, $type);

    /**
     * @param int $resourceId
     */
    public function ClearResourceUserPermissions($resourceId);

    /**
     * @param int $resourceId
     * @param int $groupId
     * @param int $type
     */
    public function ChangeResourceGroupPermission($resourceId, $groupId, $type);

    /**
     * @param int $resourceId
     */
    public function ClearResourceGroupPermissions($resourceId);

    /**
     * @return array all public resource ids in key value id=>publicid
     */
    public function GetPublicResourceIds();

    /**
     * @warning  These resources are not fully hydrated
     * @param int $userId
     * @return BookableResource[]
     */
    public function GetRecentlyUsed($userId);

    /**
     * @warning  These resources are not fully hydrated
     * @param int $userId
     * @return BookableResource[]
     */
    public function GetFavorites($userId);

    /**
     * @param int[] $resourceIds
     * @return BookableResource[]
     */
    public function LoadByIds($resourceIds);
}