<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'lib/Application/Authentication/namespace.php');
require_once(ROOT_DIR . 'lib/Application/User/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Admin/UserImportCsv.php');
require_once(ROOT_DIR . 'lib/Application/Admin/CsvImportResult.php');
require_once(ROOT_DIR . 'lib/Email/Messages/InviteUserEmail.php');
require_once(ROOT_DIR . 'lib/Email/Messages/AccountCreationForUserEmail.php');
require_once(ROOT_DIR . 'lib/Email/Messages/PasswordUpdatedByAdminEmail.php');
require_once(ROOT_DIR . 'lib/Email/Messages/ReservationOwnershipChangedEmail.php');
require_once(ROOT_DIR . 'Domain/Values/CountryCodes.php');

class ManageUsersActions
{
    const Activate = 'activate';
    const AddUser = 'addUser';
    const ChangeAttribute = 'changeAttribute';
    const Deactivate = 'deactivate';
    const DeleteUser = 'deleteUser';
    const Password = 'password';
    const Permissions = 'permissions';
    const UpdateUser = 'updateUser';
    const ChangeColor = 'changeColor';
    const ImportUsers = 'importUsers';
    const ChangeCredits = 'changeCredits';
    const InviteUsers = 'inviteUsers';
    const DeleteMultipleUsers = 'deleteMultipleUsers';
    const GetCredits = 'getCredits';
    const ReassignOwnership = 'reassignOwnership';
}

interface IManageUsersPresenter
{
    public function AddUser();

    public function UpdateUser();
}

class ManageUsersPresenter extends ActionPresenter implements IManageUsersPresenter
{
    /**
     * @var IManageUsersPage
     */
    private $page;

    /**
     * @var IUserRepository
     */
    private $userRepository;

    /**
     * @var ResourceRepository
     */
    private $resourceRepository;

    /**
     * @var IPassword
     */
    private $password;

    /**
     * @var IManageUsersService
     */
    private $manageUsersService;

    /**
     * @var IAttributeService
     */
    private $attributeService;

    /**
     * @var IGroupRepository
     */
    private $groupRepository;

    /**
     * @var IGroupViewRepository
     */
    private $groupViewRepository;

    /**
     * @param IGroupRepository $groupRepository
     */
    public function SetGroupRepository($groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    /**
     * @param IGroupViewRepository $groupViewRepository
     */
    public function SetGroupViewRepository($groupViewRepository)
    {
        $this->groupViewRepository = $groupViewRepository;
    }

    /**
     * @param IAttributeService $attributeService
     */
    public function SetAttributeService($attributeService)
    {
        $this->attributeService = $attributeService;
    }

    /**
     * @param IManageUsersService $manageUsersService
     */
    public function SetManageUsersService($manageUsersService)
    {
        $this->manageUsersService = $manageUsersService;
    }

    /**
     * @param ResourceRepository $resourceRepository
     */
    public function SetResourceRepository($resourceRepository)
    {
        $this->resourceRepository = $resourceRepository;
    }

    /**
     * @param UserRepository $userRepository
     */
    public function SetUserRepository($userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param IManageUsersPage $page
     * @param UserRepository $userRepository
     * @param IResourceRepository $resourceRepository
     * @param IPassword $password
     * @param IManageUsersService $manageUsersService
     * @param IAttributeService $attributeService
     * @param IGroupRepository $groupRepository
     * @param IGroupViewRepository $groupViewRepository
     */
    public function __construct(IManageUsersPage     $page,
                                IUserRepository      $userRepository,
                                IResourceRepository  $resourceRepository,
                                IPassword            $password,
                                IManageUsersService  $manageUsersService,
                                IAttributeService    $attributeService,
                                IGroupRepository     $groupRepository,
                                IGroupViewRepository $groupViewRepository)
    {
        parent::__construct($page);

        $this->page = $page;
        $this->userRepository = $userRepository;
        $this->resourceRepository = $resourceRepository;
        $this->password = $password;
        $this->manageUsersService = $manageUsersService;
        $this->attributeService = $attributeService;
        $this->groupRepository = $groupRepository;
        $this->groupViewRepository = $groupViewRepository;

        $this->AddAction(ManageUsersActions::Activate, 'Activate');
        $this->AddAction(ManageUsersActions::AddUser, 'AddUser');
        $this->AddAction(ManageUsersActions::Deactivate, 'Deactivate');
        $this->AddAction(ManageUsersActions::DeleteUser, 'DeleteUser');
        $this->AddAction(ManageUsersActions::Password, 'ResetPassword');
        $this->AddAction(ManageUsersActions::Permissions, 'ChangePermissions');
        $this->AddAction(ManageUsersActions::UpdateUser, 'UpdateUser');
        $this->AddAction(ManageUsersActions::ChangeAttribute, 'ChangeAttribute');
        $this->AddAction(ManageUsersActions::ChangeColor, 'ChangeColor');
        $this->AddAction(ManageUsersActions::ImportUsers, 'ImportUsers');
        $this->AddAction(ManageUsersActions::ChangeCredits, 'ChangeCredits');
        $this->AddAction(ManageUsersActions::InviteUsers, 'InviteUsers');
        $this->AddAction(ManageUsersActions::DeleteMultipleUsers, 'DeleteMultipleUsers');
        $this->AddAction(ManageUsersActions::GetCredits, 'GetCredits');
        $this->AddAction(ManageUsersActions::ReassignOwnership, 'ReassignOwnership');
    }

    public function PageLoad()
    {
        $attributeList = $this->attributeService->GetByCategory(CustomAttributeCategory::USER);

        $filter = $this->GetFilter($attributeList);
        $userList = $this->userRepository->GetList($this->page->GetPageNumber(), $this->page->GetPageSize(),
            $this->page->GetSortField(),
            $this->page->GetSortDirection(), $filter->GetFilter(), $this->page->GetFilterStatusId());

        $this->page->BindFilters($filter);

        $this->page->BindUsers($userList->Results());
        $this->page->BindPageInfo($userList->PageInfo());

        $groups = $this->groupViewRepository->GetList();
        $this->page->BindGroups($groups->Results());

        $user = $this->userRepository->LoadById(ServiceLocator::GetServer()->GetUserSession()->UserId);

        $resources = $this->GetResourcesThatCurrentUserCanAdminister($user);
        $this->page->BindResources($resources);


        $this->page->BindAttributeList($attributeList);

        $this->page->BindStatusDescriptions();
        $this->page->BindCountryCodes(CountryCodes::All(), CountryCodes::Guess(Configuration::Instance()->GetKey(ConfigKeys::LANGUAGE)));
    }

    public function Deactivate()
    {
        if ($this->page->GetUserId() != ServiceLocator::GetServer()->GetUserSession()->UserId) {
            $user = $this->userRepository->LoadById($this->page->GetUserId());
            $user->Deactivate();
            $this->userRepository->Update($user);
            $this->page->SetJsonResponse(Resources::GetInstance()->GetString('Inactive'));
        } else {
            $this->page->SetJsonResponse(Resources::GetInstance()->GetString('Active'));
        }
    }

    public function Activate()
    {
        $user = $this->userRepository->LoadById($this->page->GetUserId());
        $user->Activate();
        $this->userRepository->Update($user);
        $this->page->SetJsonResponse(Resources::GetInstance()->GetString('Active'));
    }

    public function AddUser()
    {
        $extraAttributes = [
            UserAttribute::Organization => $this->page->GetOrganization(),
            UserAttribute::Phone => $this->page->GetPhone(),
            UserAttribute::Position => $this->page->GetPosition(),
            UserAttribute::PhoneCountryCode => CountryCodes::Get($this->page->GetPhoneCountryCode(), $this->page->GetPhone(), Configuration::Instance()->GetKey(ConfigKeys::LANGUAGE))->code,
        ];

        $useColor = !$this->page->GetNoColor();

        $user = $this->manageUsersService->AddUser(
            $this->page->GetUserName(),
            $this->page->GetEmail(),
            $this->page->GetFirstName(),
            $this->page->GetLastName(),
            $this->page->GetPassword(),
            $this->page->GetTimezone(),
            Configuration::Instance()->GetKey(ConfigKeys::LANGUAGE),
            $this->page->GetHomepage(),
            $extraAttributes,
            $this->GetAttributeValues(),
            $this->page->GetIsApiOnly(),
            $useColor ? $this->page->GetReservationColor() : "");

        $userId = $user->Id();
        $groupId = $this->page->GetUserGroup();

        if (!empty($groupId)) {
            $group = $this->groupRepository->LoadById($groupId);
            $group->AddUser($userId);
            $this->groupRepository->Update($group);
        }

        if ($this->page->SendEmailNotification()) {
            ServiceLocator::GetEmailService()->Send(new AccountCreationForUserEmail($user, $this->page->GetPassword(),
                ServiceLocator::GetServer()->GetUserSession()));
        }
    }

    public function UpdateUser()
    {
        Log::Debug('Updating user', ['userId' => $this->page->GetUserId()]);

        $extraAttributes = [
            UserAttribute::Organization => $this->page->GetOrganization(),
            UserAttribute::Phone => $this->page->GetPhone(),
            UserAttribute::Position => $this->page->GetPosition(),
            UserAttribute::PhoneCountryCode => CountryCodes::Get($this->page->GetPhoneCountryCode(), $this->page->GetPhone(), Configuration::Instance()->GetKey(ConfigKeys::LANGUAGE))->code,
        ];

        $useColor = !$this->page->GetNoColor();

        $this->manageUsersService->UpdateUser($this->page->GetUserId(),
            $this->page->GetUserName(),
            $this->page->GetEmail(),
            $this->page->GetFirstName(),
            $this->page->GetLastName(),
            $this->page->GetTimezone(),
            $extraAttributes,
            $this->GetAttributeValues(),
            $this->page->GetIsApiOnly(),
            $useColor ? $this->page->GetReservationColor() : "",
            $this->page->GetHomepage(),
        );
    }

    public function DeleteUser()
    {
        $userId = $this->page->GetUserId();
        Log::Debug('Deleting user', ['userId' => $userId]);

        $this->manageUsersService->DeleteUser($userId);
    }

    public function ChangePermissions()
    {
        $currentUser = $this->userRepository->LoadById(ServiceLocator::GetServer()->GetUserSession()->UserId);
        $resources = $this->GetResourcesThatCurrentUserCanAdminister($currentUser);

        $acceptableResourceIds = array();

        foreach ($resources as $resource) {
            $acceptableResourceIds[] = $resource->GetId();
        }

        $user = $this->userRepository->LoadById($this->page->GetUserId());
        $resourcePermissions = array();

        if (is_array($this->page->GetResourcePermissions())) {
            $resourcePermissions = $this->page->GetResourcePermissions();
        }

        $allowed = [];
        $view = [];
        $none = [];
        foreach ($resourcePermissions as $resource) {
            $split = explode('_', $resource);
            $resourceId = intval($split[0]);
            $permissionType = $split[1];

            if ($permissionType == 'full') {
                $allowed[] = $resourceId;
            }
            if ($permissionType == 'view') {
                $view[] = $resourceId;
            }
            if ($permissionType == 'none') {
                $none[] = $resourceId;
            }
        }

        $allowedUnchanged = array_diff($user->GetAllowedResourceIds(), $acceptableResourceIds);
        $viewUnchanged = array_diff($user->GetAllowedViewResourceIds(), $acceptableResourceIds);
        $noneUnchanged = array_diff($user->GetNoPermissionResourceIds(), $acceptableResourceIds);

        $user->ChangeAllowedPermissions(array_merge($allowedUnchanged, $allowed));
        $user->ChangeViewPermissions(array_merge($viewUnchanged, $view));
        $user->ChangeNonePermissions(array_merge($noneUnchanged, $none));
        $this->userRepository->Update($user);
    }

    public function ResetPassword()
    {
        $password = $this->page->GetPassword();
        $mustChange = $this->page->GetUserMustChangePassword();
        $userId = $this->page->GetUserId();
        $sendToUser = $this->page->GetSendPasswordInEmail();

        $user = $this->userRepository->LoadById($userId);

        if (!empty($password)) {
            Log::Debug("Updating user password", ['userId' => $userId, 'forceUpdate' => $mustChange]);
            $encryptedPassword = $this->password->Encrypt($password);
            $user->ChangePassword($encryptedPassword);

            if ($sendToUser) {
                ServiceLocator::GetEmailService()->Send(new PasswordUpdatedByAdminEmail($user, $password));
            }
        }

        $user->SetMustChangePassword($mustChange);
        $user->ClearSecuritySettings();
        $this->userRepository->Update($user);
    }

    public function ChangeAttribute()
    {
        $this->manageUsersService->ChangeAttribute($this->page->GetUserId(), $this->GetInlineAttributeValue());
    }

    public function ExportUsers()
    {
        $this->PageLoad();
        $this->page->ShowExportCsv();
    }

    public function ProcessDataRequest($dataRequest)
    {
        if ($dataRequest == 'permissions') {
            $this->page->SetJsonResponse($this->GetUserResourcePermissions());
        } elseif ($dataRequest == 'groups') {
            $this->page->SetJsonResponse($this->GetUserGroups());
        } elseif ($dataRequest == 'all') {
            $users = $this->userRepository->GetAll();
            $this->page->SetJsonResponse($users);
        } elseif ($dataRequest == 'template') {
            $this->ShowTemplateCSV();
        } elseif ($dataRequest == 'export') {
            $this->ExportUsers();
        } elseif ($dataRequest == 'update') {
            $this->ShowUpdate();
        }
    }

    /**
     * @return int[] all resource ids the user has permission to
     */
    public function GetUserResourcePermissions()
    {
        $user = $this->userRepository->LoadById($this->page->GetUserId());
        return array('full' => $user->GetAllowedResourceIds(), 'view' => $user->GetAllowedViewResourceIds(), 'none' => $user->GetNoPermissionResourceIds());

    }

    /**
     * @return array|AttributeValue[]
     */
    private function GetAttributeValues()
    {
        $attributes = array();
        foreach ($this->page->GetAttributes() as $attribute) {
            $attributes[] = new AttributeValue($attribute->Id, $attribute->Value);
        }
        return $attributes;
    }

    private function GetInlineAttributeValue()
    {
        $value = $this->page->GetValue();
        if (is_array($value)) {
            $value = $value[0];
        }
        $id = str_replace(FormKeys::ATTRIBUTE_PREFIX, '', $this->page->GetName());

        return new AttributeValue($id, $value);
    }

    protected function LoadValidators($action)
    {
        Log::Debug('Loading validators', ['action' => $action]);

        if ($action == ManageUsersActions::UpdateUser) {
            $this->page->RegisterValidator('emailformat', new EmailValidator($this->page->GetEmail()));
            $this->page->RegisterValidator('uniqueemail',
                new UniqueEmailValidator($this->userRepository, $this->page->GetEmail(), $this->page->GetUserId()));
            $this->page->RegisterValidator('uniqueusername',
                new UniqueUserNameValidator($this->userRepository, $this->page->GetUserName(), $this->page->GetUserId()));
            $this->page->RegisterValidator('updateAttributeValidator',
                new AttributeValidator($this->attributeService, CustomAttributeCategory::USER,
                    $this->GetAttributeValues(), $this->page->GetUserId(), true, true));
        }

        if ($action == ManageUsersActions::AddUser) {
            $this->page->RegisterValidator('addUserEmailformat', new EmailValidator($this->page->GetEmail()));
            $this->page->RegisterValidator('addUserUniqueemail',
                new UniqueEmailValidator($this->userRepository, $this->page->GetEmail()));
            $this->page->RegisterValidator('addUserUsername',
                new UniqueUserNameValidator($this->userRepository, $this->page->GetUserName()));
            $this->page->RegisterValidator('addAttributeValidator',
                new AttributeValidator($this->attributeService, CustomAttributeCategory::USER,
                    $this->GetAttributeValues(), null, true, true));
        }

        if ($action == ManageUsersActions::ChangeAttribute) {
            $this->page->RegisterValidator('attributeValidator',
                new AttributeValidatorInline($this->attributeService, CustomAttributeCategory::USER,
                    $this->GetInlineAttributeValue(), $this->page->GetUserId(),
                    true, true));
        }

        if ($action == ManageUsersActions::ImportUsers) {
            $this->page->RegisterValidator('fileExtensionValidator', new FileExtensionValidator('csv', $this->page->GetImportFile()));
        }
    }

    /***
     * @return array|int[]
     */
    public function GetUserGroups()
    {
        $userId = $this->page->GetUserId();

        $user = $this->userRepository->LoadById($userId);

        $groups = array();
        foreach ($user->Groups() as $group) {
            $groups[] = $group->GroupId;
        }

        return $groups;
    }

    public function ChangeColor()
    {
        $userId = $this->page->GetUserId();
        Log::Debug('Changing reservation color', ['userId' => $userId]);

        $color = $this->page->GetReservationColor();

        $user = $this->userRepository->LoadById($userId);
        $user->ChangePreference(UserPreferences::RESERVATION_COLOR, $color);

        $this->userRepository->Update($user);
    }

    public function ChangeCredits()
    {
        $userId = $this->page->GetUserId();
        $creditCount = $this->page->GetCredits();

        Log::Debug('Changing credit count', ['userId' => $userId, 'creditCount' => $creditCount]);

        $user = $this->userRepository->LoadById($userId);
        $user->ChangeCurrentCredits($creditCount,
            Resources::GetInstance()->GetString('CreditsUpdatedLog', array(ServiceLocator::GetServer()->GetUserSession())));
        $this->userRepository->Update($user);
    }

    /**
     * @param User $user
     * @return BookableResource[]
     */
    private function GetResourcesThatCurrentUserCanAdminister($user)
    {
        $resources = array();
        $allResources = $this->resourceRepository->GetResourceList();
        foreach ($allResources as $resource) {
            if ($user->IsResourceAdminFor($resource)) {
                $resources[] = $resource;
            }
        }
        return $resources;
    }

    public function ImportUsers()
    {
        ini_set('max_execution_time', 600);

        $attributes = $this->attributeService->GetByCategory(CustomAttributeCategory::USER);
        /** @var CustomAttribute[] $attributesIndexed */
        $attributesIndexed = array();
        /** @var CustomAttribute $attribute */
        foreach ($attributes as $attribute) {
            if (!$attribute->UniquePerEntity()) {
                $attributesIndexed[strtolower($attribute->Label())] = $attribute;
            }
        }

        $groupsList = $this->groupViewRepository->GetList();
        /** @var GroupItemView[] $groups */
        $groups = $groupsList->Results();
        $groupsIndexed = array();
        foreach ($groups as $group) {
            $groupsIndexed[$group->Name()] = $group->Id();
        }

        $importFile = $this->page->GetImportFile();
        $csv = new UserImportCsv($importFile, $attributesIndexed);

        $importCount = 0;
        $messages = array();

        $rows = $csv->GetRows();

        if (count($rows) == 0) {
            $this->page->SetImportResult(new CsvImportResult(0, array(), 'Empty file or missing header row'));
            return;
        }

        for ($i = 0; $i < count($rows); $i++) {
            $shouldUpdate = $this->page->GetUpdateOnImport();

            $row = $rows[$i];
            try {
                $emailValidator = new EmailValidator(trim($row->email));
                $uniqueEmailValidator = new UniqueEmailValidator($this->userRepository, trim($row->email));
                $uniqueUsernameValidator = new UniqueUserNameValidator($this->userRepository, trim($row->username));

                $emailValidator->Validate();
                if (!$emailValidator->IsValid()) {
                    $evMsgs = $emailValidator->Messages();
                    $messages[] = $evMsgs[0] . " ({$row->email})";
                    continue;
                }

                if (!$shouldUpdate) {
                    $uniqueEmailValidator->Validate();
                    $uniqueUsernameValidator->Validate();

                    if (!$uniqueEmailValidator->IsValid()) {
                        $uevMsgs = $uniqueEmailValidator->Messages();
                        $messages[] = $uevMsgs[0] . " ({$row->email})";
                        continue;
                    }
                    if (!$uniqueUsernameValidator->IsValid()) {
                        $uuvMsgs = $uniqueUsernameValidator->Messages();
                        $messages[] = $uuvMsgs[0] . " ({$row->username})";
                        continue;
                    }
                }

                $timezone = empty($row->timezone) ? Configuration::Instance()->GetKey(ConfigKeys::DEFAULT_TIMEZONE) : $row->timezone;
                $language = empty($row->language) ? 'en_us' : $row->language;
                $status = empty($row->status) ? AccountStatus::ACTIVE : $this->DetermineStatus($row->status);

                if ($shouldUpdate) {
                    $user = $this->manageUsersService->LoadUser($row->email);
                    if ($user->Id() == null) {
                        $shouldUpdate = false;
                    } else {
                        $user->ChangeName($row->firstName, $row->lastName);
                        if ($row->password !== "password" && !empty($row->password)) {
                            $encrypted = $this->password->Encrypt($row->password);
                            $user->ChangePassword($encrypted);
                        }
                        $user->ChangeTimezone($timezone);
                        $countryCode = empty($row->phoneCountryCode) && !empty($user->CountryCode()) ? $user->CountryCode() : $row->phoneCountryCode;
                        $user->ChangeAttributes($row->phone, $row->organization, $row->position, empty($countryCode) ? CountryCodes::Guess($language) : $countryCode);
                        if ($status == AccountStatus::ACTIVE) {
                            $user->Activate();
                        } else {
                            $user->Deactivate();
                        }
                    }

                }
                if (!$shouldUpdate) {
                    $user = $this->manageUsersService->AddUser($row->username, $row->email, $row->firstName, $row->lastName, $row->password, $timezone, $language,
                        Configuration::Instance()->GetKey(ConfigKeys::DEFAULT_HOMEPAGE),
                        array(UserAttribute::Phone => $row->phone, UserAttribute::Organization => $row->organization, UserAttribute::Position => $row->position, UserAttribute::PhoneCountryCode => CountryCodes::Get($row->phoneCountryCode, $row->phone, $language)),
                        array(), false, '');
                }

                $userGroups = array();
                foreach ($row->groups as $groupName) {
                    if (array_key_exists($groupName, $groupsIndexed)) {
                        Log::Debug('Importing user with group', ['username' => $row->username, 'groupName' => $groupName]);
                        $userGroups[] = new UserGroup($groupsIndexed[$groupName], $groupName);
                    }
                }

                if (count($userGroups) > 0) {
                    $user->ChangeGroups($userGroups);
                }

                if ($row->credits != null) {
                    $user->ChangeCurrentCredits($row->credits);
                }

                if ($row->color != null) {
                    $user->ChangePreference(UserPreferences::RESERVATION_COLOR, $row->color);
                }

                foreach ($row->attributes as $label => $value) {
                    if (empty($value)) {
                        continue;
                    }
                    if (array_key_exists($label, $attributesIndexed)) {
                        $attribute = $attributesIndexed[$label];
                        $user->ChangeCustomAttribute(new AttributeValue($attribute->Id(), $value));
                    }
                }

                if (count($userGroups) > 0 || count($row->attributes) > 0 || $shouldUpdate) {
                    $this->userRepository->Update($user);
                }

                $importCount++;
            } catch (Exception $ex) {
                Log::Error('Error importing users.', ['exception' => $ex]);
            }
        }

        $this->page->SetImportResult(new CsvImportResult($importCount, $csv->GetSkippedRowNumbers(), $messages));
    }

    public function InviteUsers()
    {
        $emailList = $this->page->GetInvitedEmails();
        $emails = preg_split('/[,;\s\n]+/', $emailList);
        foreach ($emails as $email) {
            ServiceLocator::GetEmailService()->Send(new InviteUserEmail(trim($email), ServiceLocator::GetServer()->GetUserSession()));
        }
    }

    public function DeleteMultipleUsers()
    {
        $ids = $this->page->GetDeletedUserIds();
        Log::Debug('User multiple delete.', ['ids' => $ids]);
        foreach ($ids as $id) {
            $this->manageUsersService->DeleteUser($id);
        }
    }

    private function ShowTemplateCSV()
    {
        $attributes = $this->attributeService->GetByCategory(CustomAttributeCategory::USER);
        $importAttributes = array();
        foreach ($attributes as $attribute) {
            if (!$attribute->UniquePerEntity()) {
                $importAttributes[] = $attribute;
            }
        }
        $this->page->ShowTemplateCSV($importAttributes);
    }

    private function DetermineStatus($status)
    {
        if ($status == AccountStatus::INACTIVE || strtolower($status) == 'inactive') {
            return AccountStatus::INACTIVE;
        }

        return AccountStatus::ACTIVE;
    }

    public function ShowUpdate()
    {
        $userId = $this->page->GetUserId();
        $user = $this->userRepository->LoadById($userId);
        $attributes = $this->attributeService->GetAttributes(CustomAttributeCategory::USER, ServiceLocator::GetServer()->GetUserSession(), array($userId));

        $this->page->ShowUserUpdate($user, $attributes->GetDefinitions($userId));
    }

    public function GetCredits()
    {
        $userId = $this->page->GetUserId();
        $creditRepository = new CreditRepository();
        $credits = $creditRepository->GetList(null, null, $userId);
        $this->page->BindCredits($credits->Results());
    }

    public function ReassignOwnership()
    {
        $userId = $this->page->GetUserId();
        $targetId = $this->page->GetTargetUserId();
        $scope = $this->page->GetReassignScope();
        $message = $this->page->GetReassignMessage();

        Log::Debug('Reassigning reservation ownership', ['from' => $userId, 'to' => $targetId, 'scope'=> $scope]);

        if ($scope == 'all') {
            $this->userRepository->ChangeReservationOwnershipAll($userId, $targetId);
        }

        if ($scope == 'future') {
            $this->userRepository->ChangeReservationOwnershipFuture($userId, $targetId);
        }

        if (!empty($message)) {
            $sourceUser = $this->userRepository->GetById($userId);
            $targetUser = $this->userRepository->GetById($targetId);
            ServiceLocator::GetEmailService()->Send(new ReservationOwnershipChangedEmail($targetUser, $sourceUser, $message, ServiceLocator::GetServer()->GetUserSession()));
        }

    }

    /**
     * @param $attributeList CustomAttribute[]
     * @return ManageUsersFilter
     */
    private function GetFilter($attributeList)
    {
        $userId = $this->page->GetUserId();
        $statusId = $this->page->GetFilterStatusId();
        $groupId = $this->page->GetGroupId();
        $attributes = $this->page->GetAttributesFilter();

        $filters = [];
        foreach ($attributes as $a) {
            $filters[$a->Id] = $a->Value;
        }

        $attributeFilters = array();
        foreach ($attributeList as $attribute) {
            $attributeValue = null;
            if (array_key_exists($attribute->Id(), $filters)) {
                $attributeValue = $filters[$attribute->Id()];
            }
            $attributeFilters[] = new \Booked\Attribute($attribute, $attributeValue);
        }

        return new ManageUsersFilter($userId, $statusId, $groupId, $attributeFilters);
    }
}

class ManageUsersFilter
{
    /**
     * @var int|null
     */
    private $userId;
    /**
     * @var int|null
     */
    private $statusId;
    /**
     * @var int|null
     */
    private $groupId;
    /**
     * @var \Booked\Attribute[]
     */
    private $attributes;

    /**
     * @param $userId int|null
     * @param $statusId int|null
     * @param $groupId int|null
     * @param $attributes \Booked\Attribute[]
     */
    public function __construct($userId, $statusId, $groupId, $attributes)
    {
        $this->userId = $userId;
        $this->statusId = $statusId;
        $this->groupId = $groupId;
        $this->attributes = $attributes;
    }

    /**
     * @return int|null
     */
    public function UserId()
    {
        return $this->userId;
    }

    /**
     * @return int|null
     */
    public function StatusId()
    {
        return $this->statusId;
    }

    /**
     * @return int|null
     */
    public function GroupId()
    {
        return $this->groupId;
    }

    /**
     * @param int $id
     * @return mixed|null
     */
    public function AttributeValue($id)
    {
        foreach ($this->attributes as $a) {
            if ($a->Id() == $id) {
                return $a->Value();
            }
        }

        return null;
    }

    /**
     * @return ISqlFilter
     */
    public function GetFilter()
    {
        $filter = new SqlFilterNull();

        if (!empty($this->userId)) {
            $filter->_And(new SqlFilterEquals(ColumnNames::USER_ID, $this->userId));
        }

        if (!empty($this->attributes)) {
            $attributeFilter = AttributeFilter::Create(TableNames::USERS_ALIAS . '.' . ColumnNames::USER_ID, $this->attributes);

            if ($attributeFilter != null) {
                $filter->_And($attributeFilter);
            }
        }

        if (!empty($this->groupId)) {
            $filter->_And(UserGroupFilter::Create('`' . TableNames::USERS_ALIAS . '`.`' . ColumnNames::USER_ID . '`', $this->groupId));
        }

        return $filter;
    }

    public function IsFiltered()
    {
        return !empty($this->userId) || !empty($this->groupId) || !empty($this->statusId) || !$this->AttributesAreEmpty();
    }

    private function AttributesAreEmpty()
    {
        if (empty($this->attributes)) {
            return true;
        }

        foreach ($this->attributes as $a) {
            if (!empty($a->Value())) {
                return false;
            }
        }

        return true;
    }
}