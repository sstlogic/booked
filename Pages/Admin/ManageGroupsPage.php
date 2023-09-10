<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/Admin/AdminPage.php');
require_once(ROOT_DIR . 'Pages/IPageable.php');
require_once(ROOT_DIR . 'Presenters/Admin/ManageGroupsPresenter.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');

interface IManageGroupsPage extends IActionPage
{
    /**
     * @return int
     */
    public function GetGroupId();

    /**
     * @param $groups GroupItemView[]|array
     * @param $filtered boolean
     */
    public function BindGroups($groups, $filtered);

    /**
     * @param PageInfo $pageInfo
     */
    public function BindPageInfo(PageInfo $pageInfo);

    /**
     * @return int
     */
    public function GetPageNumber();

    /**
     * @return int
     */
    public function GetPageSize();

    /**
     * @return int
     */
    public function GetUserId();

    /**
     * @param $resources array|BookableResource[]
     */
    public function BindResources($resources);

    /**
     * @param $schedules Schedule[]
     */
    public function BindSchedules($schedules);

    /**
     * @param $roles array|RoleDto[]
     */
    public function BindRoles($roles);

    /**
     * @return int[]|array
     */
    public function GetAllowedResourceIds();

    /**
     * @return string
     */
    public function GetGroupName();

    /**
     * @return int[]|array
     */
    public function GetRoleIds();

    /**
     * @param $adminGroups GroupItemView[]|array
     */
    public function BindAdminGroups($adminGroups);

    /**
     * @return int
     */
    public function GetAdminGroupId();

    /**
     * @return bool
     */
    public function AutomaticallyAddToGroup();

    /**
     * @return int[]
     */
    public function GetUserIds();

    /**
     * @return int[]
     */
    public function GetGroupAdminIds();

    /**
     * @return int[]
     */
    public function GetResourceAdminIds();

    /**
     * @return int[]
     */
    public function GetScheduleAdminIds();

    /**
     * @param GroupItemView[] $groups
     * @param UserItemView[] $users
     * @param GroupResourcePermission[] $permissionsWrite
     * @param GroupResourcePermission[] $permissionsRead
     */
    public function Export($groups, $users, $permissionsWrite, $permissionsRead);

    /**
     * @return UploadedFile
     */
    public function GetImportFile();

    public function ShowTemplateCsv();

    /**
     * @param CsvImportResult $importResult
     */
    public function SetImportResult($importResult);

    /**
     * @return bool
     */
    public function GetUpdateOnImport();

    /**
     * @return int
     */
    public function GetReplenishmentId();

    /**
     * @return int
     */
    public function GetReplenishmentType();

    /**
     * @return int
     */
    public function GetReplenishmentAmount();

    /**
     * @return int
     */
    public function GetReplenishmentInterval();

    /**
     * @return int
     */
    public function GetReplenishmentDayOfMonth();

    /**
     * @return int
     */
    public function GetAmountOfCreditsToAdd();

    public function BindFilter(ManageGroupsFilter $filter);

    /**
     * @return string|null
     */
    public function GetGroupNameFilter();

    /**
     * @return int[]
     */
    public function GetRolesFilter();
}

class ManageGroupsPage extends ActionPage implements IManageGroupsPage, IPageWithId
{
    protected bool $CanChangeRoles = true;
    protected ManageGroupsPresenter $presenter;
    private PageablePage $pageable;

    public function __construct()
    {
        parent::__construct('ManageGroups', 1);
        $this->presenter = new ManageGroupsPresenter(
            $this,
            new GroupRepository(),
            new ResourceRepository(),
            new ScheduleRepository(),
            new UserRepository());

        $this->pageable = new PageablePage($this);
        $this->Set('PageId', $this->GetPageId());
    }

    public function ProcessPageLoad()
    {
        $this->presenter->PageLoad();
        $this->Set('chooseText', Resources::GetInstance()->GetString('Choose') . '...');
        $this->Set('CanChangeRoles', $this->CanChangeRoles);
        $this->Set('CreditsEnabled', Configuration::Instance()->GetSectionKey(ConfigSection::CREDITS, ConfigKeys::CREDITS_ENABLED, new BooleanConverter()));
        $this->Set('ExportUrl', ServiceLocator::GetServer()->GetUrl() . '&dr=export');
        $this->Display('Admin/Groups/manage_groups.tpl');
    }

    public function BindPageInfo(PageInfo $pageInfo)
    {
        $this->pageable->BindPageInfo($pageInfo);
    }

    public function GetPageNumber()
    {
        return $this->pageable->GetPageNumber();
    }

    public function GetPageSize()
    {
        return $this->pageable->GetPageSize();
    }

    public function BindGroups($groups, $filtered)
    {
        $this->Set('groups', $groups);
        $this->Set('isFiltered', $filtered);
    }

    public function ProcessAction()
    {
        $this->presenter->ProcessAction();
    }

    public function GetGroupId()
    {
        $groupId = $this->GetForm(FormKeys::GROUP_ID);
        if (!empty($groupId)) {
            return $groupId;
        }
        return $this->GetQuerystring(QueryStringKeys::GROUP_ID);
    }

    public function GetUserId()
    {
        return $this->GetForm(FormKeys::USER_ID);
    }

    public function BindResources($resources)
    {
        $this->Set('resources', $resources);
    }

    public function GetAllowedResourceIds()
    {
        return $this->GetForm(FormKeys::RESOURCE_ID);
    }

    public function GetGroupName()
    {
        return $this->GetForm(FormKeys::GROUP_NAME);
    }

    public function BindRoles($roles)
    {
        $this->Set('Roles', $roles);
    }

    public function GetRoleIds()
    {
        return $this->GetForm(FormKeys::ROLE_ID);
    }

    public function BindAdminGroups($adminGroups)
    {
        $this->Set('AdminGroups', $adminGroups);
    }

    public function GetAdminGroupId()
    {
        return $this->GetForm(FormKeys::GROUP_ADMIN);
    }

    public function ProcessDataRequest($dataRequest)
    {
        $this->presenter->ProcessDataRequest();
    }

    public function AutomaticallyAddToGroup()
    {
        return $this->GetCheckbox(FormKeys::IS_DEFAULT);
    }

    public function GetUserIds()
    {
        return array();
    }

    public function BindSchedules($schedules)
    {
        $this->Set('Schedules', $schedules);
    }

    public function GetGroupAdminIds()
    {
        return $this->GetForm(FormKeys::GROUP_ADMIN);
    }

    public function GetResourceAdminIds()
    {
        return $this->GetForm(FormKeys::RESOURCE_ID);
    }

    public function GetScheduleAdminIds()
    {
        return $this->GetForm(FormKeys::SCHEDULE_ID);
    }

    public function Export($groups, $users, $permissionsWrite, $permissionsRead)
    {
        $this->Set('Groups', $groups);
        $this->Set('Users', $users);
        $this->Set('PermissionsWrite', $permissionsWrite);
        $this->Set('PermissionsRead', $permissionsRead);
        $this->DisplayCsv('Admin/Groups/groups_csv.tpl', 'groups.csv');
    }

    public function GetImportFile()
    {
        return $this->server->GetFile(FormKeys::GROUP_IMPORT_FILE);
    }

    public function ShowTemplateCsv()
    {
        $this->DisplayCsv('Admin/Groups/groups_csv.tpl', 'groups.csv');
    }

    public function SetImportResult($importResult)
    {
        $this->SetJsonResponse($importResult);
    }

    public function GetUpdateOnImport()
    {
        return $this->GetCheckbox(FormKeys::UPDATE_ON_IMPORT);
    }

    public function GetReplenishmentId()
    {
        return intval($this->GetForm(FormKeys::CREDITS_REPLENISHMENT_ID));
    }

    public function GetReplenishmentType()
    {
        return intval($this->GetForm(FormKeys::CREDITS_FREQUENCY));
    }

    /**
     * @inheritDoc
     */
    public function GetReplenishmentAmount()
    {
        $type = $this->GetReplenishmentType();
        if ($type == GroupCreditReplenishmentRuleType::INTERVAL) {
            return intval($this->GetForm(FormKeys::CREDITS_AMOUNT_DAYS));
        }
        if ($type == GroupCreditReplenishmentRuleType::DAY_OF_MONTH) {
            return intval($this->GetForm(FormKeys::CREDITS_AMOUNT_DAY_OF_MONTH));
        }

        return 0;
    }

    public function GetReplenishmentInterval()
    {
        return intval($this->GetForm(FormKeys::CREDITS_DAYS));
    }

    public function GetReplenishmentDayOfMonth()
    {
        return intval($this->GetForm(FormKeys::CREDITS_DAY_OF_MONTH));
    }

    public function GetAmountOfCreditsToAdd()
    {
        return intval($this->GetForm(FormKeys::CREDITS_AMOUNT));
    }

    public function BindFilter(ManageGroupsFilter $filter)
    {
        $this->Set('Filter', $filter);
        $this->Set('IsFiltered', $filter->HasFilter());
    }

    public function GetGroupNameFilter()
    {
       return $this->GetQuerystring(FormKeys::GROUP_NAME);
    }

    public function GetRolesFilter()
    {
        return $this->GetQuerystring(FormKeys::ROLE_ID, true);
    }

    public function GetPageId(): int
    {
        return AdminPageIds::Groups;
    }
}

