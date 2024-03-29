<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Values/ResourcePermissionType.php');

interface IScheduleUserRepository
{
    /**
     * @param $userId
     * @return IScheduleUser
     */
    function GetUser($userId);
}

class ScheduleUserRepository implements IScheduleUserRepository
{
    public function GetUser($userId)
    {
        $userPermissions = $this->GetUserPermissions($userId);
        return new ScheduleUser($userId,
            $userPermissions['full'],
            $userPermissions['view'],
            $userPermissions['none'],
            $this->GetGroupPermissions($userId),
            $this->GetGroupAdminPermissions($userId));
    }

    private function GetUserPermissions($userId)
    {
        $userCommand = new GetUserPermissionsCommand($userId);

        $reader = ServiceLocator::GetDatabase()->Query($userCommand);
        $resources['full'] = array();
        $resources['view'] = array();
        $resources['none'] = array();

        while ($row = $reader->GetRow()) {
            if ($row[ColumnNames::PERMISSION_TYPE] == ResourcePermissionType::Full) {
                $resources['full'][] = new ScheduleResource($row[ColumnNames::RESOURCE_ID], $row[ColumnNames::RESOURCE_NAME]);
            }
            if ($row[ColumnNames::PERMISSION_TYPE] == ResourcePermissionType::View) {
                $resources['view'][] = new ScheduleResource($row[ColumnNames::RESOURCE_ID], $row[ColumnNames::RESOURCE_NAME]);
            }
            if ($row[ColumnNames::PERMISSION_TYPE] == ResourcePermissionType::None) {
                $resources['none'][] = new ScheduleResource($row[ColumnNames::RESOURCE_ID], $row[ColumnNames::RESOURCE_NAME]);
            }
        }

        $reader->Free();

        return $resources;
    }

    /**
     * @param $userId
     * @return array|ScheduleGroup[]
     */
    private function GetGroupPermissions($userId)
    {
        $groupCommand = new SelectUserGroupPermissions($userId);

        $reader = ServiceLocator::GetDatabase()->Query($groupCommand);
        $groupList = array();

        while ($row = $reader->GetRow()) {
            $group_id = $row[ColumnNames::GROUP_ID];
            $resourceId = $row[ColumnNames::RESOURCE_ID];
            $resourceName = $row[ColumnNames::RESOURCE_NAME];
            $permissionType = $row[ColumnNames::PERMISSION_TYPE];

            $groupList[$group_id][] = array($resourceId, $resourceName, $permissionType);
        }

        $reader->Free();

        $groups = array();
        foreach ($groupList as $group_id => $resourceList) {
            $resources = array();
            $viewOnly = array();
            foreach ($resourceList as $resourceItem) {
                $permissionType = $resourceItem[2];
                if ($permissionType == ResourcePermissionType::View) {
                    $viewOnly[] = new ScheduleResource($resourceItem[0], $resourceItem[1]);
                } else {
                    $resources[] = new ScheduleResource($resourceItem[0], $resourceItem[1]);
                }
            }
            $groups[] = new ScheduleGroup($group_id, $resources, $viewOnly);
        }

        return $groups;
    }

    private function GetGroupAdminPermissions($userId)
    {
        $userCommand = new SelectUserGroupResourceAdminPermissions($userId);

        $reader = ServiceLocator::GetDatabase()->Query($userCommand);
        $resources = array();

        while ($row = $reader->GetRow()) {
            $resources[] = new ScheduleResource($row[ColumnNames::RESOURCE_ID], $row[ColumnNames::RESOURCE_NAME]);
        }

        $reader->Free();

        return $resources;
    }
}

interface IScheduleUser
{
    /**
     * @return int
     */
    public function Id();

    /**
     * The resources that the user or any of their groups has permission to
     * @return array|ScheduleResource[]
     */
    public function GetAllResources();

    /**
     * The resources that the user or any of their groups has bookable permission to
     * @return array|ScheduleResource[]
     */
    public function GetBookableResources();

    /**
     * The resources that the user or any of their groups has view permission to
     * @return array|ScheduleResource[]
     */
    public function GetViewOnlyResources();

    /**
     * The resources that the user or any of their groups has admin access to
     * @return array|ScheduleResource[]
     */
    public function GetAdminResources();
}

class ScheduleUser implements IScheduleUser
{
    private $userId;
    private $groupPermissions;
    private $userPermissionsFull;
    private $userPermissionsView;
    private $userPermissionNone;
    private $adminResources;

    /**
     * @param int $userId ;
     * @param array|ScheduleResource[] $userPermissionsFull
     * @param array|ScheduleResource[] $userPermissionsView
     * @param array|ScheduleResource[] $userPermissionsNone
     * @param array|ScheduleGroup[] $groupPermissions
     * @param array|ScheduleResource[] $groupAdminPermissions
     */
    public function __construct($userId, $userPermissionsFull, $userPermissionsView, $userPermissionsNone, $groupPermissions, $groupAdminPermissions)
    {
        $this->userId = $userId;
        $this->userPermissionsFull = $userPermissionsFull;
        $this->userPermissionsView = $userPermissionsView;
        $this->userPermissionNone = $userPermissionsNone;
        $this->groupPermissions = $groupPermissions;
        $this->adminResources = $groupAdminPermissions;
    }

    public function Id()
    {
        return $this->userId;
    }

    private function GetGroupPermissions()
    {
        return $this->groupPermissions;
    }

    private function GetUserPermissionsFull()
    {
        return $this->userPermissionsFull;
    }

    private function GetUserPermissionsView()
    {
        return $this->userPermissionsView;
    }

    private function GetUserPermissionsNone()
    {
        return $this->userPermissionNone;
    }

    public function GetAdminResources()
    {
        return $this->adminResources;
    }

    public function GetAllResources()
    {
        $resources = array();
        $noAccessIds = [];
        foreach ($this->GetUserPermissionsNone() as $resource) {
            $noAccessIds[] = $resource->Id();
        }

        foreach ($this->GetUserPermissionsFull() as $resource) {
            $resources[] = $resource;
        }

        foreach ($this->GetUserPermissionsView() as $resource) {
            $resources[] = $resource;
        }

        foreach ($this->GetGroupPermissions() as $group) {
            foreach ($group->GetAllResources() as $resource) {
                if (!in_array($resource->Id(), $noAccessIds)) {
                    $resources[] = $resource;
                }
            }
        }

        foreach ($this->GetAdminResources() as $resource) {
            $resources[] = $resource;
        }

        return array_unique($resources);
    }

    public function GetBookableResources()
    {
        $resources = array();
        $viewOnly = $this->GetUserPermissionsView();
        $noAccess = $this->GetUserPermissionsNone();

        foreach ($this->GetUserPermissionsFull() as $resource) {
            $resources[] = $resource;
        }

        foreach ($this->GetGroupPermissions() as $group) {
            foreach ($group->GetBookableResources() as $resource) {
                if (!in_array($resource, $noAccess) && !in_array($resource, $viewOnly)) {
                    $resources[] = $resource;
                }
            }
        }

        foreach ($this->GetAdminResources() as $resource) {
            $resources[] = $resource;
        }

        return array_unique($resources);
    }

    public function GetViewOnlyResources()
    {
        $resources = array();
        $bookable = $this->GetBookableResources();

        foreach ($this->GetUserPermissionsView() as $resource) {
            if (!in_array($resource, $bookable)) {
                $resources[] = $resource;
            }
        }

        foreach ($this->GetGroupPermissions() as $group) {
            foreach ($group->GetViewOnlyResources() as $resource) {
                if (!in_array($resource, $bookable)) {
                    $resources[] = $resource;
                }
            }
        }

        return array_unique($resources);
    }
}

class ScheduleGroup
{
    private $group_id;
    private $bookableResources;
    private $viewOnlyResources;
    private $allResources;

    /**
     * @param int $group_id
     * @param ScheduleResource[] $bookableResources
     * @param ScheduleResource[] $viewOnlyResources
     */
    public function __construct($group_id, $bookableResources, $viewOnlyResources)
    {
        $this->group_id = $group_id;
        $this->bookableResources = $bookableResources;
        $this->viewOnlyResources = $viewOnlyResources;
        $this->allResources = array_merge($bookableResources, $viewOnlyResources);
    }

    /**
     * @return int
     */
    public function Id()
    {
        return $this->group_id;
    }

    /**
     * @return array|ScheduleResource[]
     */
    public function GetAllResources()
    {
        return $this->allResources;
    }

    /**
     * @return array|ScheduleResource[]
     */
    public function GetBookableResources()
    {
        return $this->bookableResources;
    }

    /**
     * @return array|ScheduleResource[]
     */
    public function GetViewOnlyResources()
    {
        return $this->viewOnlyResources;
    }
}

class ScheduleResource
{
    private $_resourceId;
    private $_name;

    /**
     * @param int $resourceId
     * @param string $name
     */
    public function __construct($resourceId, $name)
    {
        $this->_resourceId = $resourceId;
        $this->_name = $name;
    }

    /**
     * @return int
     */
    public function Id()
    {
        return $this->_resourceId;
    }

    /**
     * @return string
     */
    public function Name()
    {
        return $this->_name;
    }

    public function __toString()
    {
        // needed for array_unique
        return (string)$this->_resourceId;
    }
}

class NullScheduleResource extends ScheduleResource
{
    public function __construct()
    {
        parent::__construct(0, null);
    }

    public function GetMinimumLength()
    {
        return null;
    }
}