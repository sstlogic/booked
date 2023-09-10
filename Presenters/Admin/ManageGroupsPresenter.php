<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'lib/Application/Authentication/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Admin/GroupImportCsv.php');
require_once(ROOT_DIR . 'lib/Application/Admin/CsvImportResult.php');
require_once(ROOT_DIR . 'Pages/Admin/ManageGroupsPage.php');

class ManageGroupsActions
{
    const Activate = 'activate';
    const Deactivate = 'deactivate';
    const Password = 'password';
    const Permissions = 'permissions';
    const RemoveUser = 'removeUser';
    const AddUser = 'addUser';
    const AddGroup = 'addGroup';
    const UpdateGroup = 'updateGroup';
    const DeleteGroup = 'deleteGroup';
    const Roles = 'roles';
    const GroupAdmin = 'groupAdmin';
    const AdminGroups = 'adminGroups';
    const ResourceGroups = 'resourceGroups';
    const ScheduleGroups = 'scheduleGroups';
    const Import = 'import';
    const GetCreditReplenishment = 'getCreditReplenishment';
    const UpdateCreditReplenishment = 'updateCreditReplenishment';
    const AddCredits = 'addCredits';
    const AddAllUsers = 'addAllUsers';
}

class ManageGroupsPresenter extends ActionPresenter
{
    /**
     * @var IManageGroupsPage
     */
    private $page;
    /**
     * @var GroupRepository
     */
    private $groupRepository;
    /**
     * @var IResourceRepository
     */
    private $resourceRepository;
    /**
     * @var IScheduleRepository
     */
    private $scheduleRepository;
    /**
     * @var IUserRepository
     */
    private $userRepository;

    /**
     * @param IManageGroupsPage $page
     * @param IGroupRepository $groupRepository
     * @param IResourceRepository $resourceRepository
     * @param IScheduleRepository $scheduleRepository
     * @param IUserRepository $userRepository
     */
    public function __construct(IManageGroupsPage   $page,
                                GroupRepository     $groupRepository,
                                IResourceRepository $resourceRepository,
                                IScheduleRepository $scheduleRepository,
                                IUserRepository     $userRepository)
    {
        parent::__construct($page);

        $this->page = $page;
        $this->groupRepository = $groupRepository;
        $this->resourceRepository = $resourceRepository;
        $this->scheduleRepository = $scheduleRepository;
        $this->userRepository = $userRepository;

        $this->AddAction(ManageGroupsActions::AddUser, 'AddUser');
        $this->AddAction(ManageGroupsActions::RemoveUser, 'RemoveUser');
        $this->AddAction(ManageGroupsActions::Permissions, 'ChangePermissions');
        $this->AddAction(ManageGroupsActions::AddGroup, 'AddGroup');
        $this->AddAction(ManageGroupsActions::UpdateGroup, 'UpdateGroup');
        $this->AddAction(ManageGroupsActions::DeleteGroup, 'DeleteGroup');
        $this->AddAction(ManageGroupsActions::Roles, 'ChangeRoles');
        $this->AddAction(ManageGroupsActions::GroupAdmin, 'ChangeGroupAdmin');
        $this->AddAction(ManageGroupsActions::AdminGroups, 'ChangeAdminGroups');
        $this->AddAction(ManageGroupsActions::ResourceGroups, 'ChangeResourceGroups');
        $this->AddAction(ManageGroupsActions::ScheduleGroups, 'ChangeScheduleGroups');
        $this->AddAction(ManageGroupsActions::Import, 'Import');
        $this->AddAction(ManageGroupsActions::UpdateCreditReplenishment, 'UpdateCreditReplenishment');
        $this->AddAction(ManageGroupsActions::AddCredits, 'AddCredits');
        $this->AddAction(ManageGroupsActions::AddAllUsers, 'AddAllUsers');
    }

    public function PageLoad()
    {
        $filter = $this->BuildFilter();

        $groupList = $this->groupRepository->GetList($this->page->GetPageNumber(), $this->page->GetPageSize(), $this->page->GetSortField(),
            $this->page->GetSortDirection(), $filter->GetFilter());

        $this->page->BindGroups($groupList->Results(), $filter->HasFilter());
        $this->page->BindPageInfo($groupList->PageInfo());
        $this->page->BindFilter($filter);

        $this->page->BindResources($this->resourceRepository->GetResourceList());
        $this->page->BindSchedules($this->scheduleRepository->GetAll());

        $this->page->BindRoles([
            new RoleDto(1, 'Group Admin', RoleLevel::GROUP_ADMIN),
            new RoleDto(2, 'Application Admin', RoleLevel::APPLICATION_ADMIN),
            new RoleDto(3, 'Resource Admin', RoleLevel::RESOURCE_ADMIN),
            new RoleDto(4, 'Schedule Admin', RoleLevel::SCHEDULE_ADMIN)
        ]);
        $this->page->BindAdminGroups($this->groupRepository->GetGroupsByRole(RoleLevel::GROUP_ADMIN));
    }

    public function ChangePermissions()
    {
        $group = $this->groupRepository->LoadById($this->page->GetGroupId());
        $resources = [];
        $allowed = [];
        $view = [];

        if (is_array($this->page->GetAllowedResourceIds())) {
            $resources = $this->page->GetAllowedResourceIds();
        }

        foreach ($resources as $resource) {
            $split = explode('_', $resource);
            $resourceId = $split[0];
            $permissionType = $split[1];

            if ($permissionType === ResourcePermissionType::Full . '') {
                $allowed[] = $resourceId;
            } else {
                if ($permissionType === ResourcePermissionType::View . '') {
                    $view[] = $resourceId;
                }
            }
        }

        $group->ChangeViewPermissions($view);
        $group->ChangeAllowedPermissions($allowed);
        $this->groupRepository->Update($group);
    }

    public function ChangeRoles()
    {
        $groupId = $this->page->GetGroupId();
        Log::Debug("Changing roles for group", ['groupId' => $groupId]);

        $group = $this->groupRepository->LoadById($groupId);
        $roles = [];

        if (is_array($this->page->GetRoleIds())) {
            $roles = $this->page->GetRoleIds();
        }
        $group->ChangeRoles($roles);
        $this->groupRepository->Update($group);
    }

    public function ProcessDataRequest()
    {
        $response = '';
        $request = $this->page->GetDataRequest();
        switch ($request) {
            case 'groupMembers' :
                $users = $this->groupRepository->GetUsersInGroup($this->page->GetGroupId());
                $response = new UserGroupResults($users->Results(), $users->PageInfo()->Total);
                break;
            case 'permissions' :
                $response = $this->GetGroupResourcePermissions();
                break;
            case 'roles' :
                $response = $this->GetGroupRoles();
                break;
            case ManageGroupsActions::AdminGroups :
                $response = $this->GetAdminGroups();
                break;
            case ManageGroupsActions::ResourceGroups :
                $response = $this->GetResourceAdminGroups();
                break;
            case ManageGroupsActions::ScheduleGroups :
                $response = $this->GetScheduleAdminGroups();
                break;
            case 'allUserCount':
                $response = ['count' =>  $this->userRepository->GetCount()];
                break;
            case ManageGroupsActions::GetCreditReplenishment:
                $response = $this->GetCreditReplenishment();
                break;
            case 'export':
                $this->Export();
                return;
            case 'template':
                $this->page->ShowTemplateCsv();
                return;
        }

        $this->page->SetJsonResponse($response);
    }

    /**
     * @return int[] all resource ids the user has permission to
     */
    public function GetGroupResourcePermissions()
    {
        $group = $this->groupRepository->LoadById($this->page->GetGroupId());
        return ['full' => $group->AllowedResourceIds(), 'view' => $group->AllowedViewResourceIds()];
    }

    public function AddUser()
    {
        $groupId = $this->page->GetGroupId();
        $userId = $this->page->GetUserId();

        Log::Debug('Adding user to group', ['userId' => $userId, 'groupId' => $groupId]);

        $group = $this->groupRepository->LoadById($groupId);
        $group->AddUser($userId);
        $this->groupRepository->Update($group);
    }

    public function AddAllUsers()
    {
        $groupId = $this->page->GetGroupId();

        Log::Debug('Adding all users to group', ['groupId' => $groupId]);

        $group = $this->groupRepository->LoadById($groupId);
        $users = $this->userRepository->GetAll();
        foreach ($users as $user) {
            $group->AddUser($user->Id());
        }
        $this->groupRepository->Update($group);
    }

    public function RemoveUser()
    {
        $groupId = $this->page->GetGroupId();
        $userId = $this->page->GetUserId();

        Log::Debug('Removing user from group', ['userId' => $userId, 'groupId' => $groupId]);

        $group = $this->groupRepository->LoadById($groupId);
        $group->RemoveUser($userId);
        $this->groupRepository->Update($group);
    }

    public function ChangeUsers()
    {
        $groupId = $this->page->GetGroupId();
        $userIds = $this->page->GetUserIds();

        Log::Debug('Changing group users', ['groupId' => $groupId]);

        $group = $this->groupRepository->LoadById($groupId);
        $group->ChangeUsers($userIds);
        $this->groupRepository->Update($group);
    }

    public function AddGroup()
    {
        $groupName = $this->page->GetGroupName();
        $isDefault = $this->page->AutomaticallyAddToGroup();
        Log::Debug('Adding new group', ['name' => $groupName, 'isDefault' => $isDefault]);

        $group = new Group(0, $groupName, $isDefault);
        return $this->groupRepository->Add($group);
    }

    public function UpdateGroup()
    {
        $groupId = $this->page->GetGroupId();
        $groupName = $this->page->GetGroupName();
        $isDefault = $this->page->AutomaticallyAddToGroup();
        Log::Debug('Renaming group', ['groupId' => $groupId, 'name' => $groupName]);

        $group = $this->groupRepository->LoadById($groupId);
        $group->Rename($groupName);
        $group->ChangeDefault($isDefault);

        $this->groupRepository->Update($group);
    }

    public function DeleteGroup()
    {
        $groupId = $this->page->GetGroupId();

        Log::Debug("Deleting group", ['groupId' => $groupId]);

        $group = $this->groupRepository->LoadById($groupId);
        $this->groupRepository->Remove($group);
    }

    public function ChangeGroupAdmin()
    {
        $groupId = $this->page->GetGroupId();
        $adminGroupId = $this->page->GetAdminGroupId();

        if ($groupId == $adminGroupId) {
            Log::Error("Cannot set a group as its own administrator", ['groupId' => $groupId]);
            return;
        }

        Log::Debug("Changing admin for group", ['groupId' => $groupId, 'adminGroupId' => $adminGroupId]);

        $group = $this->groupRepository->LoadById($groupId);

        $group->ChangeAdmin($adminGroupId);

        $this->groupRepository->Update($group);
    }

    /**
     * @return array|int[]
     */
    public function GetGroupRoles()
    {
        $groupId = $this->page->GetGroupId();
        $group = $this->groupRepository->LoadById($groupId);

        $ids = $group->RoleIds();

        return $ids;
    }

    public function GetAdminGroups()
    {
        $groupId = $this->page->GetGroupId();

        $result = $this->groupRepository->GetList(null, null, null, null,
            new SqlFilterEquals(new SqlFilterColumn(TableNames::GROUPS_ALIAS, ColumnNames::GROUP_ADMIN_GROUP_ID),
                $groupId));
        $ids = [];
        /** @var GroupItemView $group */
        foreach ($result->Results() as $group) {
            $ids[] = $group->Id();
        }

        return $ids;
    }

    public function GetResourceAdminGroups()
    {
        $groupId = $this->page->GetGroupId();

        $result = $this->resourceRepository->GetList(null, null, null, null,
            new SqlFilterEquals(new SqlFilterColumn(TableNames::RESOURCES_ALIAS, ColumnNames::RESOURCE_ADMIN_GROUP_ID),
                $groupId));
        $ids = [];
        /** @var BookableResource $resource */
        foreach ($result->Results() as $resource) {
            $ids[] = $resource->GetId();
        }

        return $ids;
    }

    public function GetScheduleAdminGroups()
    {
        $groupId = $this->page->GetGroupId();

        $result = $this->scheduleRepository->GetList(null, null, null, null,
            new SqlFilterEquals(new SqlFilterColumn(TableNames::SCHEDULES_ALIAS, ColumnNames::SCHEDULE_ADMIN_GROUP_ID),
                $groupId));
        $ids = [];
        /** @var Schedule $schedule */
        foreach ($result->Results() as $schedule) {
            $ids[] = $schedule->GetId();
        }

        return $ids;
    }

    public function ChangeAdminGroups()
    {
        $groupId = $this->page->GetGroupId();
        $groupIds = $this->page->GetGroupAdminIds();
        if (empty($groupIds)) {
            $groupIds = [];
        }
        Log::Debug('Changing group admins.', ['groupId' => $groupId, 'groupIds' => $groupIds]);

        $currentAdminGroups = $this->GetAdminGroups();
        foreach ($currentAdminGroups as $id) {
            $group = $this->groupRepository->LoadById($id);
            $group->ChangeAdmin(null);
            $this->groupRepository->Update($group);
        }

        foreach ($groupIds as $id) {
            $group = $this->groupRepository->LoadById($id);
            $group->ChangeAdmin($groupId);
            $this->groupRepository->Update($group);
        }
    }

    public function ChangeResourceGroups()
    {
        $groupId = $this->page->GetGroupId();
        $resourceIds = $this->page->GetResourceAdminIds();
        if (empty($resourceIds)) {
            $resourceIds = [];
        }
        Log::Debug('Changing resource admins.', ['groupId' => $groupId, 'resourceIds' => $resourceIds]);

        $currentAdminGroups = $this->GetResourceAdminGroups();
        foreach ($currentAdminGroups as $id) {
            $resource = $this->resourceRepository->LoadById($id);
            $resource->SetAdminGroupId(null);
            $this->resourceRepository->Update($resource);
        }

        foreach ($resourceIds as $id) {
            $resource = $this->resourceRepository->LoadById($id);
            $resource->SetAdminGroupId($groupId);
            $this->resourceRepository->Update($resource);
        }
    }

    public function ChangeScheduleGroups()
    {
        $groupId = $this->page->GetGroupId();
        $scheduleIds = $this->page->GetScheduleAdminIds();
        if (empty($scheduleIds)) {
            $scheduleIds = [];
        }
        Log::Debug('Changing schedule admins.', ['groupId' => $groupId, 'scheduleIds' => $scheduleIds]);

        $currentAdminGroups = $this->GetScheduleAdminGroups();
        foreach ($currentAdminGroups as $id) {
            $schedule = $this->scheduleRepository->LoadById($id);
            $schedule->SetAdminGroupId(null);
            $this->scheduleRepository->Update($schedule);
        }

        foreach ($scheduleIds as $id) {
            $schedule = $this->scheduleRepository->LoadById($id);
            $schedule->SetAdminGroupId($groupId);
            $this->scheduleRepository->Update($schedule);
        }
    }

    public function Export()
    {
        /** @var GroupItemView[] $groups */
        $groups = $this->groupRepository->GetList()->Results();
        /** @var UserItemView[] $userGroups */
        $userGroups = $this->userRepository->GetList(null, null, null, null, null, AccountStatus::ACTIVE)->Results();
        $groupPermissions = $this->groupRepository->GetPermissionList();

        $indexedPermissionsWrite = [];
        $indexedPermissionsRead = [];
        $indexedUsers = [];
        foreach ($groups as $group) {
            $indexedPermissionsWrite[$group->Id()] = [];
            $indexedPermissionsRead[$group->Id()] = [];
            $indexedUsers[$group->Id()] = [];
        }

        foreach ($userGroups as $user) {
            foreach ($user->GroupIds as $groupId) {
                $indexedUsers[$groupId][] = $user;
            }
        }

        foreach ($groupPermissions as $groupPermission) {
            if ($groupPermission->PermissionType() == ResourcePermissionType::Full) {
                $indexedPermissionsWrite[$groupPermission->GroupId()][] = $groupPermission;
            }
            if ($groupPermission->PermissionType() == ResourcePermissionType::View) {
                $indexedPermissionsRead[$groupPermission->GroupId()][] = $groupPermission;
            }
        }

        $this->page->Export($groups, $indexedUsers, $indexedPermissionsWrite, $indexedPermissionsRead);
    }

    public function Import()
    {
        Log::Debug('Importing groups');

        ini_set('max_execution_time', 600);

        $resources = $this->resourceRepository->GetResourceList();
        /** @var int[] $resourcesIndexed */
        $resourcesIndexed = [];
        foreach ($resources as $resource) {
            $resourcesIndexed[$resource->GetName()] = $resource->GetId();
        }

        /** @var UserItemView[] $users */
        $users = $this->userRepository->GetList(null, null, null, null, null, AccountStatus::ACTIVE)->Results();
        /** @var int[] $usersIndexed */
        $usersIndexed = [];
        foreach ($users as $user) {
            $usersIndexed[$user->Email] = $user->Id;
        }

        /** @var GroupItemView[] $groups */
        $groups = $this->groupRepository->GetList()->Results();
        /** @var int[] $groupsIndexed */
        $groupsIndexed = [];
        foreach ($groups as $group) {
            $groupsIndexed[$group->Name()] = $group->Id();
        }

        $importFile = $this->page->GetImportFile();
        $csv = new GroupImportCsv($importFile);

        $importCount = 0;
        $messages = [];

        $rows = $csv->GetRows();

        if (count($rows) == 0) {
            $this->page->SetImportResult(new CsvImportResult(0, array(), 'Empty file or missing header row'));
            return;
        }

        for ($i = 0; $i < count($rows); $i++) {
            $row = $rows[$i];

            $shouldUpdate = $this->page->GetUpdateOnImport() && array_key_exists($row->name, $groupsIndexed);

            try {
                if ($shouldUpdate) {
                    $group = $this->groupRepository->LoadById($groupsIndexed[$row->name]);
                    $group->ChangeDefault($row->autoAdd);

                    if (!empty($row->groupAdministrator) && array_key_exists($row->groupAdministrator, $groupsIndexed)) {
                        $group->ChangeAdmin($groupsIndexed[$row->groupAdministrator]);
                    }

                    $group->ChangeRoles($this->GetImportRoles($row));

                    if (!empty($row->members)) {
                        $memberIds = $this->GetImportMembers($row->members, $usersIndexed);
                        $group->ChangeUsers($memberIds);
                    }

                    if (!empty($row->permissionsFull)) {
                        $resourceIds = $this->GetImportResources($row->permissionsFull, $resourcesIndexed);
                        $group->ChangeAllowedPermissions($resourceIds);
                    }

                    if (!empty($row->permissionsView)) {
                        $resourceIds = $this->GetImportResources($row->permissionsRead, $resourcesIndexed);

                        $group->ChangeViewPermissions($resourceIds);
                    }

                    Log::Debug('Updating group from import.', ['groupId' => $group->Id()]);

                    $this->groupRepository->Update($group);
                } else {
                    Log::Debug('Adding group from import.', ['groupName' => $row->name]);
                    $group = new Group(0, $row->name, $row->autoAdd);
                    $this->groupRepository->Add($group);

                    if (!empty($row->groupAdministrator) && array_key_exists($row->groupAdministrator, $groupsIndexed)) {
                        $group->ChangeAdmin($groupsIndexed[$row->groupAdministrator]);
                    }

                    $group->ChangeRoles($this->GetImportRoles($row));
                    $group->ChangeUsers($this->GetImportMembers($row->members, $usersIndexed));
                    $group->ChangeAllowedPermissions($this->GetImportResources($row->permissionsFull, $resourcesIndexed));
                    $group->ChangeViewPermissions($this->GetImportResources($row->permissionsRead, $resourcesIndexed));

                    $this->groupRepository->Update($group);
                }

                $importCount++;
            } catch (Exception $ex) {
                Log::Error('Error importing groups. %s', $ex);
            }
        }

        $this->page->SetImportResult(new CsvImportResult($importCount, $csv->GetSkippedRowNumbers(), $messages));
    }

    public function LoadValidators($action)
    {
        if ($action == ManageGroupsActions::Import) {
            $this->page->RegisterValidator('fileExtensionValidator', new FileExtensionValidator('csv', $this->page->GetImportFile()));
        }
    }

    private function GetImportMembers($members, $usersIndexed)
    {
        $memberIds = [];
        foreach ($members as $member) {
            if (array_key_exists($member, $usersIndexed)) {
                $memberIds[] = $usersIndexed[$member];
            }
        }

        return $memberIds;
    }

    private function GetImportResources($resources, $resourcesIndexed)
    {
        $resourceIds = [];
        foreach ($resources as $resource) {
            if (array_key_exists($resource, $resourcesIndexed)) {
                $resourceIds[] = $resourcesIndexed[$resource];
            }
        }

        return $resourceIds;
    }

    private function GetImportRoles(GroupImportCsvRow $row)
    {
        $roles = [];
        if ($row->isAdmin) {
            $roles[] = RoleLevel::APPLICATION_ADMIN;
        }
        if ($row->isGroupAdmin) {
            $roles[] = RoleLevel::GROUP_ADMIN;
        }
        if ($row->isResourceAdmin) {
            $roles[] = RoleLevel::RESOURCE_ADMIN;
        }
        if ($row->isScheduleAdmin) {
            $roles[] = RoleLevel::SCHEDULE_ADMIN;
        }

        return $roles;
    }

    public function GetCreditReplenishment()
    {
        $group = $this->groupRepository->LoadById($this->page->GetGroupId());
        return new GroupCreditReplenishment($group);
    }

    public function UpdateCreditReplenishment()
    {
        $id = $this->page->GetReplenishmentId();
        $groupId = $this->page->GetGroupId();
        $type = $this->page->GetReplenishmentType();
        $amount = $this->page->GetReplenishmentAmount();
        $interval = $this->page->GetReplenishmentInterval();
        $dayOfMonth = $this->page->GetReplenishmentDayOfMonth();

        Log::Debug("Updating group credit replenishment rule.", ['groupid' => $groupId, 'type' => $type, 'amount' => $amount, 'interval' => $interval, 'dayofMonth' => $dayOfMonth]);

        $this->groupRepository->UpdateCreditsReplenishment($id, $groupId, $type, $amount, $interval, $dayOfMonth);
    }

    public function AddCredits()
    {
        $groupId = $this->page->GetGroupId();
        $amount = $this->page->GetAmountOfCreditsToAdd();

        Log::Debug("Adding credits", ['group' => $groupId, 'amount' => $amount]);

        $note = Resources::GetInstance()->GetString('CreditsAddedByAdminNote');
        $this->groupRepository->AddCreditsToUsers($groupId, $amount, $note);
    }

    private function BuildFilter()
    {
        $name = $this->page->GetGroupNameFilter();
        $roles = $this->page->GetRolesFilter();

        return new ManageGroupsFilter($name, $roles);
    }
}

class UserGroupResults
{
    /**
     * @param UserItemView[] $users
     * @param int $totalUsers
     */
    public function __construct($users, $totalUsers)
    {
        foreach ($users as $user) {
            $this->Users[] = new AutocompleteUser($user->Id, $user->First, $user->Last, $user->Email, $user->Username);
        }
        $this->Total = $totalUsers;
    }

    /**
     * @var int
     */
    public $Total;

    /**
     * @var AutocompleteUser[]
     */
    public $Users;
}

class GroupCreditReplenishment
{
    public $id = 0;
    public $type = 0;
    public $amount = 0;
    public $dayOfMonth = 0;
    public $interval = 0;
    public $lastReplenishment = '';

    public function __construct(Group $group)
    {
        $rule = $group->ReplenishmentRule();
        if ($rule != null) {
            $this->id = $rule->Id();
            $this->type = $rule->Type();
            $this->amount = $rule->Amount();
            $this->dayOfMonth = $rule->DayOfMonth();
            $this->interval = $rule->Interval();
            $this->lastReplenishment = $rule->LastReplenishmentDate()->Format(Resources::GetInstance()->GeneralDateTimeFormat());
        }

        if ($this->lastReplenishment == "") {
            $this->lastReplenishment = Resources::GetInstance()->GetString("Never");
        }
    }
}

class ManageGroupsFilter
{
    private $name;
    private $roles;

    public function __construct($name, $roles)
    {

        $this->name = $name;
        $this->roles = $roles;
    }

    public function GroupName()
    {
        return $this->name;
    }

    public function HasRole($id)
    {
        return in_array($id, $this->roles);
    }

    public function GetFilter()
    {
        $filter = new SqlFilterNull();

        if (!empty($this->name)) {
            $filter->_And(new SqlFilterLike(new SqlFilterColumn(TableNames::GROUPS_ALIAS, ColumnNames::GROUP_NAME), $this->name));
        }

        if (!empty($this->roles)) {
            $roleFilter = new SqlFilterFreeForm("`g`.`group_id` IN (select `group_id` from `group_roles` where `role_id` in (@InRoleIds))");
            $roleFilter->AddCriteria('InRoleIds', implode(',', $this->roles));
            $filter->_And($roleFilter);
        }

        return $filter;
    }

    public function HasFilter()
    {
        return !empty($this->name) || !empty($this->roles);
    }
}