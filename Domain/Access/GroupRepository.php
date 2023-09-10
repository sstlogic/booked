<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Values/ResourcePermissionType.php');
require_once(ROOT_DIR . 'Domain/Values/GroupCreditReplenishmentRule.php');

interface IGroupRepository
{
    /**
     * @param int $groupId
     * @return Group
     */
    public function LoadById($groupId);

    /**
     * @param Group $group
     * @return int newly inserted group id
     */
    public function Add(Group $group);

    /**
     * @param Group $group
     * @return void
     */
    public function Update(Group $group);

    /**
     * @param Group $group
     * @return void
     */
    public function Remove(Group $group);
}

interface IGroupViewRepository
{
    /**
     * @param int $pageNumber
     * @param int $pageSize
     * @param string $sortField
     * @param string $sortDirection
     * @param ISqlFilter $filter
     * @return PageableData|GroupItemView[]
     */
    public function GetList($pageNumber = null, $pageSize = null, $sortField = null, $sortDirection = null,
                            $filter = null);

    /**
     * @param int|array|int[] $groupIds
     * @param int $pageNumber
     * @param int $pageSize
     * @param ISqlFilter $filter
     * @param AccountStatus|int $accountStatus
     * @return PageableData|UserItemView[]
     */
    public function GetUsersInGroup($groupIds, $pageNumber = null, $pageSize = null, $filter = null,
                                    $accountStatus = AccountStatus::ALL);

    /**
     * @param $roleLevel int|RoleLevel
     * @return GroupItemView[]|array
     */
    public function GetGroupsByRole($roleLevel);

    /**
     * @return GroupResourcePermission[]
     */
    public function GetPermissionList();
}

class GroupRepository implements IGroupRepository, IGroupViewRepository
{
    /**
     * @var DomainCache
     */
    private $_cache;

    public function __construct()
    {
        $this->_cache = new DomainCache();
    }

    /**
     * @param int $pageNumber
     * @param int $pageSize
     * @param string $sortField
     * @param string $sortDirection
     * @param ISqlFilter $filter
     * @return PageableData|GroupItemView[]
     */
    public function GetList($pageNumber = null, $pageSize = null, $sortField = null, $sortDirection = null,
                            $filter = null)
    {
        $command = new GetAllGroupsCommand();

        if ($filter != null) {
            $command = new FilterCommand($command, $filter);
        }

        $builder = array('GroupItemView', 'Create');
        return PageableDataStore::GetList($command, $builder, $pageNumber, $pageSize, $sortField, $sortDirection);
    }

    /**
     * @param array|int|int[] $groupIds
     * @param null $pageNumber
     * @param null $pageSize
     * @param null $filter
     * @param AccountStatus|int $accountStatus
     * @return PageableData|UserItemView[]
     */
    public function GetUsersInGroup($groupIds, $pageNumber = null, $pageSize = null, $filter = null,
                                    $accountStatus = AccountStatus::ACTIVE)
    {
        $command = new GetAllGroupUsersCommand($groupIds, $accountStatus);

        if ($filter != null) {
            $command = new FilterCommand($command, $filter);
        }

        $builder = array('UserItemView', 'Create');
        return PageableDataStore::GetList($command, $builder, $pageNumber, $pageSize);
    }

    public function LoadById($groupId)
    {
        if ($this->_cache->Exists($groupId)) {
            return $this->_cache->Get($groupId);
        }

        $group = null;
        $db = ServiceLocator::GetDatabase();

        $reader = $db->Query(new GetGroupByIdCommand($groupId));
        if ($row = $reader->GetRow()) {
            $group = new Group($row[ColumnNames::GROUP_ID], $row[ColumnNames::GROUP_NAME], $row[ColumnNames::GROUP_ISDEFAULT]);
            $group->WithGroupAdmin($row[ColumnNames::GROUP_ADMIN_GROUP_ID]);
            $group->SetShowOnReservation($row[ColumnNames::GROUP_LIMIT_ON_RESERVATION]);

            $replenishmentRuleId = $row[ColumnNames::GROUP_CREDIT_REPLENISHMENT_RULE_ID];
            if (isset($replenishmentRuleId) && $replenishmentRuleId != null) {
                $group->WithReplenishment(new GroupCreditReplenishmentRule(
                        $row[ColumnNames::GROUP_CREDIT_REPLENISHMENT_RULE_ID],
                        $row[ColumnNames::GROUP_ID],
                        $row[ColumnNames::GROUP_CREDIT_REPLENISHMENT_RULE_TYPE],
                        $row[ColumnNames::GROUP_CREDIT_REPLENISHMENT_RULE_AMOUNT],
                        $row[ColumnNames::GROUP_CREDIT_REPLENISHMENT_RULE_DAY_OF_MONTH],
                        $row[ColumnNames::GROUP_CREDIT_REPLENISHMENT_RULE_INTERVAL],
                        Date::FromDatabase($row[ColumnNames::GROUP_CREDIT_REPLENISHMENT_RULE_LAST_DATE]),
                    )
                );
            }
        }
        $reader->Free();

        $reader = $db->Query(new GetAllGroupUsersCommand($groupId, AccountStatus::ACTIVE));
        while ($row = $reader->GetRow()) {
            $group->WithUser($row[ColumnNames::USER_ID]);
        }
        $reader->Free();

        $reader = $db->Query(new GetAllGroupPermissionsCommand($groupId));
        while ($row = $reader->GetRow()) {
            if ($row[ColumnNames::PERMISSION_TYPE] == ResourcePermissionType::Full) {
                $group->WithFullPermission($row[ColumnNames::RESOURCE_ID]);
            } else {
                $group->WithViewablePermission($row[ColumnNames::RESOURCE_ID]);
            }
        }
        $reader->Free();

        $reader = $db->Query(new GetAllGroupRolesCommand($groupId));
        while ($row = $reader->GetRow()) {
            $group->WithRole($row[ColumnNames::ROLE_ID]);
        }
        $reader->Free();

        $this->_cache->Add($groupId, $group);
        return $group;
    }

    /**
     * @param Group $group
     * @return void
     */
    public function Update(Group $group)
    {
        $db = ServiceLocator::GetDatabase();

        $groupId = $group->Id();
        foreach ($group->RemovedUsers() as $userId) {
            $db->Execute(new DeleteUserGroupCommand($userId, $groupId));
        }

        foreach ($group->AddedUsers() as $userId) {
            $db->Execute(new AddUserGroupCommand($userId, $groupId));
        }

        foreach ($group->RemovedPermissions() as $resourceId) {
            $db->Execute(new DeleteGroupResourcePermission($groupId, $resourceId));
        }

        foreach ($group->AddedPermissions() as $resourceId) {
            $db->Execute(new AddGroupResourcePermission($group->Id(), $resourceId, ResourcePermissionType::Full));
        }

        foreach ($group->AddedViewPermissions() as $resourceId) {
            $db->Execute(new AddGroupResourcePermission($group->Id(), $resourceId, ResourcePermissionType::View));
        }

        foreach ($group->RemovedRoles() as $roleId) {
            $db->Execute(new DeleteGroupRoleCommand($groupId, $roleId));
        }

        foreach ($group->AddedRoles() as $roleId) {
            $db->Execute(new AddGroupRoleCommand($groupId, $roleId));
        }

        $db->Execute(new UpdateGroupCommand($groupId, $group->Name(), $group->AdminGroupId(), $group->IsDefault(), $group->GetShowOnReservation()));

        $this->_cache->Add($groupId, $group);
    }

    public function Remove(Group $group)
    {
        ServiceLocator::GetDatabase()->Execute(new DeleteGroupCommand($group->Id()));

        $this->_cache->Remove($group->Id());
    }

    public function Add(Group $group)
    {
        $groupId = ServiceLocator::GetDatabase()->ExecuteInsert(new AddGroupCommand($group->Name(), $group->IsDefault()));
        $group->WithId($groupId);

        return $groupId;
    }

    /**
     * @param $roleLevel int|RoleLevel
     * @return GroupItemView[]|array
     */
    public function GetGroupsByRole($roleLevel)
    {
        $reader = ServiceLocator::GetDatabase()->Query(new GetAllGroupsByRoleCommand($roleLevel));
        $groups = array();
        while ($row = $reader->GetRow()) {
            $groups[] = GroupItemView::Create($row);
        }
        $reader->Free();

        return $groups;
    }

    public function GetPermissionList()
    {
        $reader = ServiceLocator::GetDatabase()->Query(new GetAllGroupResourcePermissions());

        $groups = array();
        while ($row = $reader->GetRow()) {
            $groups[] = GroupResourcePermission::Create($row);
        }
        $reader->Free();

        return $groups;
    }

    public function UpdateCreditsReplenishment($id, $groupId, $type, $amount, $interval, $dayOfMonth)
    {
        if ($type == GroupCreditReplenishmentRuleType::NONE) {
            ServiceLocator::GetDatabase()->Execute(new DeleteGroupCreditReplenishmentCommand($groupId));
            return;
        }

        if (empty($id)) {
            ServiceLocator::GetDatabase()->Execute(new AddGroupCreditsReplenishmentCommand($groupId, $type, $amount, $interval, $dayOfMonth));
            return;
        }

        ServiceLocator::GetDatabase()->Execute(new UpdateGroupCreditReplenishmentRuleCommand($id, $groupId, $type, $amount, $interval, $dayOfMonth, null));

    }

    public function AddCreditsToUsers($groupId, $amount, $note)
    {
        ServiceLocator::GetDatabase()->Execute(new AddGroupUserCreditsCommand($groupId, $amount, $note));
    }

    public function GetAllReplenishmentRules()
    {
        $reader = ServiceLocator::GetDatabase()->Query(new GetAllGroupCreditReplenishmentRules());
        $rules = array();
        while ($row = $reader->GetRow()) {
            $rules[] = new GroupCreditReplenishmentRule(
                $row[ColumnNames::GROUP_CREDIT_REPLENISHMENT_RULE_ID],
                $row[ColumnNames::GROUP_ID],
                $row[ColumnNames::GROUP_CREDIT_REPLENISHMENT_RULE_TYPE],
                $row[ColumnNames::GROUP_CREDIT_REPLENISHMENT_RULE_AMOUNT],
                $row[ColumnNames::GROUP_CREDIT_REPLENISHMENT_RULE_DAY_OF_MONTH],
                $row[ColumnNames::GROUP_CREDIT_REPLENISHMENT_RULE_INTERVAL],
                Date::FromDatabase($row[ColumnNames::GROUP_CREDIT_REPLENISHMENT_RULE_LAST_DATE]),
            );
        }
        $reader->Free();

        return $rules;
    }

    public function UpdateReplenishmentRule(GroupCreditReplenishmentRule $rule)
    {
        ServiceLocator::GetDatabase()->Execute(new UpdateGroupCreditReplenishmentRuleCommand(
            $rule->Id(),
            $rule->GroupId(),
            $rule->Type(),
            $rule->Amount(),
            $rule->Interval(),
            $rule->DayOfMonth(),
            $rule->LastReplenishmentDate()));
    }
}

class GroupUserView
{
    public static function Create($row)
    {
        return new GroupUserView(
            $row[ColumnNames::USER_ID],
            $row[ColumnNames::FIRST_NAME],
            $row[ColumnNames::LAST_NAME]);
    }

    public $UserId;
    public $FirstName;
    public $LastName;
    public $GroupId;

    public function __construct($userId, $firstName, $lastName)
    {
        $this->UserId = $userId;
        $this->FirstName = $firstName;
        $this->LastName = $lastName;
    }
}

class GroupItemView
{
    public static function Create($row)
    {
        $adminName = isset($row[ColumnNames::GROUP_ADMIN_GROUP_NAME]) ? $row[ColumnNames::GROUP_ADMIN_GROUP_NAME] : null;
        $isDefault = intval($row[ColumnNames::GROUP_ISDEFAULT]);
        $roles = explode(',', $row[ColumnNames::GROUP_ROLE_LIST] . '');
        $showOnReservation = isset($row[ColumnNames::GROUP_LIMIT_ON_RESERVATION]) ? $row[ColumnNames::GROUP_LIMIT_ON_RESERVATION] : null;
        return new GroupItemView($row[ColumnNames::GROUP_ID], $row[ColumnNames::GROUP_NAME], $adminName, $isDefault, $roles, $showOnReservation);
    }

    /**
     * @var int
     */
    public $Id;

    /**
     * @return int
     */
    public function Id()
    {
        return $this->Id;
    }

    /**
     * @var string
     */
    public $Name;

    /**
     * @return string
     */
    public function Name()
    {
        return $this->Name;
    }

    /**
     * @var string
     */
    public $AdminGroupName;

    /**
     * @return string|null
     */
    public function AdminGroupName()
    {
        return $this->AdminGroupName;
    }

    /**
     * @var int
     */
    public $IsDefault;

    public function IsDefault()
    {
        return $this->IsDefault;
    }

    /**
     * @var int[]
     */
    public $Roles;

    /**
     * @var bool
     */
    public $ShowOnReservation = true;

    /**
     * @return bool
     */
    public function LimitedOnReservation()
    {
        return $this->ShowOnReservation === 1 || $this->ShowOnReservation === "1";
    }

    /**
     * @return bool
     */
    public function IsAdmin()
    {
        return in_array(RoleLevel::APPLICATION_ADMIN, $this->Roles);
    }

    /**
     * @return bool
     */
    public function IsGroupAdmin()
    {
        return in_array(RoleLevel::GROUP_ADMIN, $this->Roles);
    }

    /**
     * @return bool
     */
    public function IsResourceAdmin()
    {
        return in_array(RoleLevel::RESOURCE_ADMIN, $this->Roles);
    }

    /**
     * @return bool
     */
    public function IsScheduleAdmin()
    {
        return in_array(RoleLevel::SCHEDULE_ADMIN, $this->Roles);
    }

    /**
     * @return bool
     */
    public function IsExtendedAdmin()
    {
        return $this->IsGroupAdmin() || $this->IsScheduleAdmin() || $this->IsResourceAdmin();
    }

    public function __construct($groupId, $groupName, $adminGroupName = null, $isDefault = 0, $roles = array(), $showOnReservation = null)
    {
        $this->Id = $groupId;
        $this->Name = $groupName;
        $this->AdminGroupName = $adminGroupName;
        $this->IsDefault = $isDefault;
        $this->Roles = $roles;
        $this->ShowOnReservation = $showOnReservation;
    }

    public function RoleIds()
    {
        return $this->Roles;
    }
}

class GroupPermissionItemView extends GroupItemView
{
    public $PermissionType;

    public function __construct($groupId, $groupName, $adminGroupName = null, $isDefault = 0)
    {
        parent::__construct($groupId, $groupName, $adminGroupName, $isDefault);
        $this->PermissionType = ResourcePermissionType::None;
    }

    public function PermissionType()
    {
        return $this->PermissionType;
    }

    public static function Create($row)
    {
        $item = GroupItemView::Create($row);
        $me = new GroupPermissionItemView($item->Id, $item->Name, $item->AdminGroupName, $item->IsDefault);
        $me->PermissionType = $row[ColumnNames::PERMISSION_TYPE];
        return $me;
    }
}

class RoleDto
{
    /**
     * @var int
     */
    public $Id;

    /**
     * @var string
     */
    public $Name;

    /**
     * @var int|RoleLevel
     */
    public $Level;

    /**
     * @param $id int
     * @param $name string
     * @param $level RoleLevel|int
     */
    public function __construct($id, $name, $level)
    {
        $this->Id = $id;
        $this->Name = $name;
        $this->Level = $level;
    }
}

class GroupResourcePermission
{
    private $groupId;
    private $resourceId;
    private $resourceName;
    private $permissionType;

    /**
     * @return int
     */
    public function GroupId()
    {
        return $this->groupId;
    }

    /**
     * @return int
     */
    public function ResourceId()
    {
        return $this->resourceId;
    }

    /**
     * @return string
     */
    public function ResourceName()
    {
        return $this->resourceName;
    }

    /**
     * @return int|ResourcePermissionType
     */
    public function PermissionType()
    {
        return $this->permissionType;
    }

    /**
     * @param array $row
     * @return GroupResourcePermission
     */
    public static function Create($row)
    {
        $grp = new GroupResourcePermission();
        $grp->groupId = $row[ColumnNames::GROUP_ID];
        $grp->resourceId = $row[ColumnNames::RESOURCE_ID];
        $grp->resourceName = $row[ColumnNames::RESOURCE_NAME];
        $grp->permissionType = $row[ColumnNames::PERMISSION_TYPE];
        return $grp;
    }
}