<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/namespace.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Graphics/namespace.php');
require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'lib/Application/Admin/ImageUploadDirectory.php');
require_once(ROOT_DIR . 'lib/Application/Admin/ResourceImportCsv.php');
require_once(ROOT_DIR . 'lib/Application/Admin/CsvImportResult.php');
require_once(ROOT_DIR . 'lib/Email/Messages/ResourceStatusChangeEmail.php');
require_once(ROOT_DIR . 'Presenters/ApiDtos/namespace.php');

class ManageResourcesActions
{
    const ActionPrintQR = 'printQR';
    const ActionUpdateImages = 'updateImages';
}

class ManageResourcesPresenter extends ActionPresenter
{
    /**
     * @var IManageResourcesPage
     */
    private $page;

    /**
     * @var IResourceRepository
     */
    private $resourceRepository;

    /**
     * @var IScheduleRepository
     */
    private $scheduleRepository;

    /**
     * @var IGroupViewRepository
     */
    private $groupRepository;

    /**
     * @var IAttributeService
     */
    private $attributeService;

    /**
     * @var IReservationViewRepository
     */
    private $reservationViewRepository;

    /**
     * @var IUserRepository
     */
    private $userRepository;

    public function __construct(
        IManageResourcesPage       $page,
        IResourceRepository        $resourceRepository,
        IScheduleRepository        $scheduleRepository,
        IGroupViewRepository       $groupRepository,
        IAttributeService          $attributeService,
        IReservationViewRepository $reservationViewRepository,
        IUserRepository            $userRepository)
    {
        parent::__construct($page);

        $this->page = $page;
        $this->resourceRepository = $resourceRepository;
        $this->scheduleRepository = $scheduleRepository;
        $this->groupRepository = $groupRepository;
        $this->attributeService = $attributeService;
        $this->reservationViewRepository = $reservationViewRepository;
        $this->userRepository = $userRepository;

        $this->AddAction(ManageResourcesActions::ActionPrintQR, 'PrintQRCode');
        $this->AddAction(ManageResourcesActions::ActionUpdateImages, 'ApiChangeImages');

        $this->AddApi('load', 'ApiLoad');
        $this->AddApi('updateStatus', 'ApiUpdateStatus');
        $this->AddApi('updateCommon', 'ApiUpdateCommon');
        $this->AddApi('updateAccess', 'ApiUpdateAccess');
        $this->AddApi('updateDisplay', 'ApiUpdateDisplay');
        $this->AddApi('updateDuration', 'ApiUpdateDuration');
        $this->AddApi('updateCredits', 'ApiUpdateCredits');
        $this->AddApi('updateRelationships', 'ApiUpdateRelationships');
        $this->AddApi('updateGroups', 'ApiUpdateGroups');
        $this->AddApi('getPermissions', 'ApiGetPermissions');
        $this->AddApi('updatePermissions', 'ApiUpdatePermissions');
        $this->AddApi('updateAttributes', 'ApiUpdateAttributes');
        $this->AddApi('add', 'ApiAddResource');
        $this->AddApi('delete', 'ApiDeleteResource');
        $this->AddApi('bulkDelete', 'ApiBulkDeleteResource');
        $this->AddApi('import', 'ApiImportResources');
        $this->AddApi('updateGroupTree', 'ApiUpdateGroupTree');
        $this->AddApi('addResourceType', 'ApiAddResourceType');
        $this->AddApi('updateResourceType', 'ApiUpdateResourceType');
        $this->AddApi('deleteResourceType', 'ApiDeleteResourceType');
        $this->AddApi('addResourceStatusReason', 'ApiAddResourceStatusReason');
        $this->AddApi('updateResourceStatusReason', 'ApiUpdateResourceStatusReason');
        $this->AddApi('deleteResourceStatusReason', 'ApiDeleteResourceStatusReason');
        $this->AddApi('copyResource', 'ApiCopyResource');
    }

    public function ApiLoad(): ApiActionResult
    {
        Log::Debug("ApiLoad");
        $resources = $this->resourceRepository->GetResourceList();
        $schedules = $this->scheduleRepository->GetAll();
        $types = $this->resourceRepository->GetResourceTypes();
        $statusReasons = $this->resourceRepository->GetStatusReasons();
        $groups = $this->groupRepository->GetGroupsByRole(RoleLevel::RESOURCE_ADMIN);
        $attributes = $this->attributeService->GetByCategory(CustomAttributeCategory::RESOURCE);
        $typeAttributes = $this->attributeService->GetByCategory(CustomAttributeCategory::RESOURCE_TYPE);
        $resourceGroups = $this->resourceRepository->GetResourceGroupsList();

        foreach ($resources as $resource) {
            if (empty($resource->GetPublicId())) {
                $resource->WithPublicId(BookedStringHelper::Random(20));
                $this->resourceRepository->Update($resource);
            }
        }

        $user = $this->userRepository->LoadById(ServiceLocator::GetServer()->GetUserSession()->UserId);

        return new ApiActionResult(true, [
            'resources' => ResourceApiDto::FromList($resources),
            'schedules' => ScheduleApiDto::FromList($this->GetSelectableSchedules($user, $schedules)),
            'resourceTypes' => ResourceTypeApiDto::FromList($types),
            'resourceStatuses' => ResourceStatusApiDto::FromList($statusReasons),
            'groups' => GroupsApiDto::FromList($this->GetSelectableGroups($user, $groups)),
            'attributes' => AttributeApiDto::FromList($attributes),
            'resourceGroups' => ResourceGroupApiDto::FromList($resourceGroups),
            'resourceTypesAttributes' => AttributeApiDto::FromList($typeAttributes),
            'isAdminGroupRequired' => $user->IsResourceAdmin(),
        ]);
    }

    /**
     * @param User $user
     * @param GroupItemView[] $groups
     * @return GroupItemView[]
     */
    private function GetSelectableGroups(User $user, $groups)
    {
        if ($user->IsApplicationAdmin() || !$user->IsResourceAdmin()) {
            return $groups;
        }

        $selectable = [];
        foreach ($user->GetResourceAdminGroupIds() as $id) {
            foreach ($groups as $g) {
                if ($g->Id() == $id) {
                    $selectable[] = $g;
                }
            }
        }

        return $selectable;
    }

    /**
     * @param User $user
     * @param Schedule[] $schedules
     * @return Schedule[]
     */
    private function GetSelectableSchedules(User $user, $schedules)
    {
        if ($user->IsApplicationAdmin() || !$user->IsScheduleAdmin()) {
            return $schedules;
        }

        $selectable = [];
        foreach ($user->GetScheduleAdminGroupIds() as $id) {
            foreach ($schedules as $s) {
                if ($s->GetAdminGroupId() == $id) {
                    $selectable[] = $s;
                }
            }
        }

        return $selectable;
    }

    public function ApiUpdateStatus($json): ApiActionResult
    {
        /** @var ResourceStatusUpdateDto $update */
        $update = $json;

        Log::Debug("ApiUpdateStatus.", ['resourceId' => $update->resourceId, 'statusId' => $update->statusId]);

        $resource = $this->UpdateResourceStatus($update->resourceId,
            intval($update->statusId),
            empty($update->reasonId) ? null : intval($update->reasonId),
            apiencode($update->reasonCreated),
            intval($update->notifyUsers),
            empty($update->notificationDays) ? null : intval($update->notificationDays),
            apiencode($update->notificationMessage));
        $statusReasons = $this->resourceRepository->GetStatusReasons();
        return new ApiActionResult(true, [
            'resource' => ResourceApiDto::FromResource($resource),
            'resourceStatuses' => ResourceStatusApiDto::FromList($statusReasons)
        ]);
    }

    public function ApiChangeImages(): void
    {
        $resourceId = $this->page->GetApiResourceId();
        $images = $this->page->GetApiImageNames();
        $files = $this->page->GetApiUploadedImages();

        if (empty($images)) {
            $images = [];
        }
        if (empty($files)) {
            $files = [];
        }

        $resource = $this->resourceRepository->LoadById($resourceId);
        $currentImages = $resource->GetAllImages();

        $errors = [];

        if (!empty($files)) {
            foreach ($files as $uploadedImage) {
                if ($uploadedImage->IsError()) {
                    Log::Error('Error with uploaded image', ['resourceId' => $resourceId, 'error' => $uploadedImage->Error()]);
                    $errors[] = "Image error: " . $uploadedImage->Error();
                    continue;
                }

                $fileType = strtolower($uploadedImage->Extension());

                $supportedTypes = array('jpeg', 'gif', 'png', 'jpg', 'svg');

                if (!in_array($fileType, $supportedTypes)) {
                    Log::Error('Invalid image type', ['resourceId' => $resourceId, 'fileType' => $fileType]);
                    $errors[] = "Invalid image type: $fileType";
                    continue;
                }

                $imageSize = getimagesize($uploadedImage->TemporaryName());
                if ($imageSize) {
                    $bytesNeeded = $imageSize[0] * $imageSize[1] * 3;
                    $memoryLimit = ini_get('memory_limit');
                    $currentUsage = memory_get_usage();
                    $needed = ($bytesNeeded + $currentUsage) / 1048576;
                    $limit = str_replace('M', '', $memoryLimit);

                    if ($needed > $limit) {
                        Log::Error("Uploaded image is too big.", ['resourceId' => $resourceId, 'size' => $needed, 'limit' => $limit]);
                        $errors[] = "Image too big. Resize to a smaller size or reduce the resolution and try again.";
                        continue;
                    }
                }

                $time = BookedStringHelper::Random(10);
                $fileName = "resource{$resourceId}{$time}.$fileType";
                $path = $this->GetResourceImageDirectory($fileName);

                Log::Debug("Saving resource image", ['path' => $path]);

                @move_uploaded_file($uploadedImage->TemporaryName(), $path);

                foreach ($images as $key => $value) {
                    if ($value == $uploadedImage->OriginalName()) {
                        $images[$key] = $fileName;
                    }
                }
            }
        }

        if (!empty($errors)) {
            $this->page->SetJsonResponse(null, $errors, 400);
            return;
        }

        // remove all images that are no longer tied to resource
        foreach ($currentImages as $currentImage) {
            if (!in_array($currentImage, $images)) {
                @unlink($this->GetResourceImageDirectory($currentImage));
            }
        }

        $resource->ReplaceImages($images);

        $this->resourceRepository->Update($resource);
        $resource = $this->resourceRepository->LoadById($resourceId);

        $this->page->SetJsonResponse(['data' => ResourceApiDto::FromResource($resource)]);
    }

    public function ApiUpdateCommon($json): ApiActionResult
    {
        /** @var ResourceCommonUpdateDto $update */
        $update = $json;

        Log::Debug("ApiUpdateCommon.", ['id' => $update->id]);

        $typeId = $update->typeId;
        if (!empty($update->newType) && empty($typeId)) {
            $typeId = $this->resourceRepository->AddResourceType(new ResourceType(0, apiencode($update->newType), ""));
        }

        $resource = $this->resourceRepository->LoadById(intval($update->id));
        $resource->SetResourceTypeId($typeId);
        $resource->SetMinParticipants(!empty($update->minParticipants) ? intval($update->minParticipants) : 0);
        $resource->SetMaxParticipants(!empty($update->capacity) ? intval($update->capacity) : 0);
        $resource->SetLocation(apiencode($update->location));
        $resource->SetContact(apiencode($update->contact));
        $resource->SetAdminGroupId(!empty($update->adminId) ? intval($update->adminId) : null);
        $resource->SetDescription(apiencode($update->description));
        $resource->SetNotes(apiencode($update->notes));

        $this->resourceRepository->Update($resource);
        $types = $this->resourceRepository->GetResourceTypes();

        return new ApiActionResult(true, [
            'resource' => ResourceApiDto::FromResource($resource),
            'resourceTypes' => ResourceTypeApiDto::FromList($types)
        ]);
    }

    public function ApiUpdateDisplay($json): ApiActionResult
    {
        /** @var ResourceDisplayUpdateDto $update */
        $update = $json;

        Log::Debug("ApiUpdateDisplay.", ['id' => $update->id]);

        $resource = $this->resourceRepository->LoadById(intval($update->id));
        $resource->SetName(trim($update->name) == "" ? $resource->GetName() : apiencode($update->name));
        $resource->SetScheduleId(intval($update->scheduleId));
        $resource->SetSortOrder(!empty($update->sortOrder) ? intval($update->sortOrder) : 0);
        $resource->SetSlotLabel(trim($update->slotLabel . ''));
        $resource->SetColor(isset($update->color) ? trim($update->color) : null);
        if (BooleanConverter::ConvertValue($update->showToPublic)) {
            $resource->EnableSubscription();
        } else {
            $resource->DisableSubscription();
        }

        $this->resourceRepository->Update($resource);

        return new ApiActionResult(true, ResourceApiDto::FromResource($resource));
    }

    public function ApiUpdateAccess($json): ApiActionResult
    {
        /** @var ResourceAccessUpdateDto $update */
        $update = $json;

        Log::Debug("ApiUpdateAccess.", ['id' => $update->id]);

        $resource = $this->resourceRepository->LoadById(intval($update->id));
        $resource->SetRequiresApproval(intval($update->requiresApproval));
        $resource->SetMinNoticeAdd(TimeIntervalApiDto::FromApi($update->noticeAdd));
        $resource->SetMinNoticeUpdate(TimeIntervalApiDto::FromApi($update->noticeUpdate));
        $resource->SetMinNoticeDelete(TimeIntervalApiDto::FromApi($update->noticeDelete));
        $resource->SetMaxNotice(TimeIntervalApiDto::FromApi($update->noticeEnd));
        $resource->SetCheckin(
            BooleanConverter::ConvertValue($update->requiresCheckin),
            $update->autoReleaseMinutes,
            BooleanConverter::ConvertValue($update->enableAutoExtend),
            BooleanConverter::ConvertValue($update->checkinLimitedToAdmins),
            $update->autoReleaseAction);
        $resource->SetMaxConcurrentReservations($update->maxConcurrent);

        $this->resourceRepository->Update($resource);

        return new ApiActionResult(true, ResourceApiDto::FromResource($resource));
    }

    public function ApiUpdateDuration($json): ApiActionResult
    {
        /** @var ResourceDurationUpdateDto $update */
        $update = $json;

        Log::Debug("ApiUpdateDuration.", ['id' => $update->id]);

        $resource = $this->resourceRepository->LoadById(intval($update->id));
        $resource->SetAllowMultiday(BooleanConverter::ConvertValue($update->allowCrossDay));
        $resource->SetBufferTime(TimeIntervalApiDto::FromApi($update->buffer));
        $resource->SetMinLength(TimeIntervalApiDto::FromApi($update->minDuration));
        $resource->SetMaxLength(TimeIntervalApiDto::FromApi($update->maxDuration));
        $this->resourceRepository->Update($resource);

        return new ApiActionResult(true, ResourceApiDto::FromResource($resource));
    }

    public function ApiUpdateCredits($json): ApiActionResult
    {
        if (!Configuration::Instance()->GetSectionKey(ConfigSection::CREDITS, ConfigKeys::CREDITS_ENABLED, new BooleanConverter())) {
            return new ApiActionResult(false, null, "Credits not enabled");
        }

        /** @var ResourceCreditUpdateDto $update */
        $update = $json;

        Log::Debug("ApiUpdateCredits.", ['id' => $update->id]);

        $resource = $this->resourceRepository->LoadById(intval($update->id));
        $resource->ChangeCredits(floatval($update->creditsOffPeak), floatval($update->creditsPeak), intval($update->creditsCalculated),
            BooleanConverter::ConvertValue($update->creditsChargedForBlockedSlots));
        $this->resourceRepository->Update($resource);

        return new ApiActionResult(true, ResourceApiDto::FromResource($resource));
    }

    public function ApiUpdateRelationships($json): ApiActionResult
    {
        /** @var ResourceRelationshipUpdateDto $update */
        $update = $json;

        Log::Debug("ApiUpdateRelationships.", ['id' => $update->id]);

        $required = array_map('intval', $update->required);
        $requiredOneWay = array_map('intval', $update->requiredOneWay);
        $excluded = array_map('intval', $update->excluded);
        $excludedAtTime = array_map('intval', $update->excludedAtTime);

        $resource = $this->resourceRepository->LoadById(intval($update->id));

        $resource->ChangeRequiredResources($required);
        $resource->ChangeRequiredOneWayResources($requiredOneWay);
        $resource->ChangeExcludedResources($excluded);
        $resource->ChangeExcludedTimeResources($excludedAtTime);

        $this->resourceRepository->Update($resource);

        $resources = [];
        $ids = array_merge($required, $requiredOneWay, $excluded, $excludedAtTime, [$update->id]);
        foreach ($ids as $id) {
            $resources[] = ResourceApiDto::FromResource($this->resourceRepository->LoadById($id));
        }

        return new ApiActionResult(true, ['resources' => $resources]);
    }

    public function ApiUpdateGroups($json): ApiActionResult
    {
        /** @var ResourceGroupUpdateDto $update */
        $update = $json;

        Log::Debug("ApiUpdateGroups.", ['id' => $update->id]);

        $map = [];

        /** @var ResourceGroupApiDto $newGroup */
        foreach ($update->addedGroups as $newGroup) {
            $parentId = null;
            if (!BookedStringHelper::StartsWith($newGroup->parentId, "new")) {
                $parentId = $newGroup->parentId;
            }

            $group = $this->resourceRepository->AddResourceGroup(new ResourceGroup(null, apiencode($newGroup->name), $parentId));
            $map[$newGroup->id] = $group->id;
        }

        foreach ($update->addedGroups as $newGroup) {
            if (BookedStringHelper::StartsWith($newGroup->parentId, "new")) {
                $this->resourceRepository->UpdateResourceGroup(new ResourceGroup($map[$newGroup->id], apiencode($newGroup->name), $map[$newGroup->parentId]));
            }
        }

        $groupIds = [];
        foreach ($update->groupIds as $groupId) {
            if (BookedStringHelper::StartsWith($groupId, "new")) {
                $groupIds[] = $map[$groupId];
            } else {
                $groupIds[] = intval($groupId);
            }
        }

        $resource = $this->resourceRepository->LoadById(intval($update->id));
        $resource->SetResourceGroupIds($groupIds);
        $this->resourceRepository->Update($resource);

        $groups = $this->resourceRepository->GetResourceGroupsList();

        return new ApiActionResult(true, ['resource' => ResourceApiDto::FromResource($resource), 'resourceGroups' => ResourceGroupApiDto::FromList($groups)]);
    }

    public function ApiGetPermissions(): ApiActionResult
    {
        $allUsers = (new UserRepository())->GetAll();
        $allGroups = (new GroupRepository())->GetList();
        $users = $this->resourceRepository->GetUsersWithPermission($this->page->GetResourceId());
        $groups = $this->resourceRepository->GetGroupsWithPermission($this->page->GetResourceId());

        return new ApiActionResult(true, [
            'allUsers' => UserApiDto::FromList($allUsers, $allGroups->Results(), $this->AreDetailsHidden(), $this->IsNameShown(), false, false, ServiceLocator::GetServer()->GetUserSession()->UserId),
            'userPermissions' => ResourcePermissionApiDto::FromUserList($users->Results()),
            'allGroups' => GroupApiDto::FromList($allGroups->Results()),
            'groupPermissions' => ResourcePermissionApiDto::FromGroupList($groups->Results()),
        ]);
    }

    public function ApiUpdatePermissions($json): ApiActionResult
    {
        /** @var ResourcePermissionUpdateDto $update */
        $update = $json;

        Log::Debug("ApiUpdatePermissions.", ['id' => $update->id]);

        if (count($update->userPermissions) == 0) {
            $this->resourceRepository->ClearResourceUserPermissions($update->id);
        }
        foreach ($update->userPermissions as $p) {
            $this->resourceRepository->ChangeResourceUserPermission($update->id, $p->id, $p->permissionType);
        }

        if (count($update->groupPermissions) == 0) {
            $this->resourceRepository->ClearResourceGroupPermissions($update->id);
        }
        foreach ($update->groupPermissions as $p) {
            $this->resourceRepository->ChangeResourceGroupPermission($update->id, $p->id, $p->permissionType);
        }

        $resource = $this->resourceRepository->LoadById(intval($update->id));
        $resource->SetAutoAssign(BooleanConverter::ConvertValue($update->autoPermission));
        $this->resourceRepository->Update($resource);

        return new ApiActionResult(true, ResourceApiDto::FromResource($resource));
    }

    public function ApiUpdateAttributes($json): ApiActionResult
    {
        /** @var ResourceAttributesUpdateDto $update */
        $update = $json;

        Log::Debug("ApiUpdateAttributes.", ['id' => $update->id]);
        $resource = $this->resourceRepository->LoadById(intval($update->id));
        foreach ($update->attributeValues as $av) {
            $resource->ChangeAttribute(new AttributeValue(intval($av->id), apiencode($av->value)));
        }
        $this->resourceRepository->Update($resource);

        return new ApiActionResult(true, ResourceApiDto::FromResource($resource));
    }

    public function ApiAddResource($json): ApiActionResult
    {
        /** @var ResourceAddDto $add */
        $add = $json;

        Log::Debug("ApiAddResource", ['name' => $add->name]);

        $attributeValues = [];
        foreach ($add->attributeValues as $av) {
            $attributeValues[] = new AttributeValue(intval($av->id), apiencode($av->value));
        }

        $validation = $this->attributeService->Validate(CustomAttributeCategory::RESOURCE, $attributeValues, [], false, true);
        if (!$validation->IsValid()) {
            return new ApiActionResult(false, null, new ApiErrorList($validation->Errors()));
        }

        $typeId = isset($add->typeId) ? $add->typeId : null;
        if (!empty($add->newType) && empty($typeId)) {
            $typeId = $this->resourceRepository->AddResourceType(new ResourceType(0, apiencode($add->newType), ""));
        }

        $toAdd = BookableResource::CreateNew(apiencode($add->name), intval($add->scheduleId), BooleanConverter::ConvertValue($add->autoPermission));
        $id = $this->resourceRepository->Add($toAdd);
        $resource = $this->resourceRepository->LoadById($id);
        $resource->ChangeAttributes($attributeValues);
        $resource->SetResourceTypeId($typeId);
        $resource->SetRequiresApproval(BooleanConverter::ConvertValue($add->requiresApproval));

        if (!empty($add->adminGroupId)) {
            $resource->SetAdminGroupId(intval($add->adminGroupId));
        }
        $this->resourceRepository->Update($resource);

        return new ApiActionResult(true, ResourceApiDto::FromResource($resource));
    }

    public function ApiDeleteResource($json): ApiActionResult
    {
        Log::Debug("ApiDeleteResource.", ['id' => $json->id]);
        $id = $this->DeleteResourceById(intval($json->id));
        return new ApiActionResult(true, ["id" => $id]);
    }

    public function ApiBulkDeleteResource($json): ApiActionResult
    {
        Log::Debug("ApiDeleteResource");

        $deletedIds = [];
        foreach ($json->ids as $id) {
            $deletedIds[] = $this->DeleteResourceById(intval($id));
        }

        return new ApiActionResult(true, ["ids" => $deletedIds]);
    }

    private function DeleteResourceById($id)
    {
        $resource = $this->resourceRepository->LoadById($id);
        foreach ($resource->GetAllImages() as $imageName) {
            $path = $this->GetResourceImageDirectory($imageName);
            @unlink($path);
        }
        $this->resourceRepository->Delete($resource);
        return $resource->GetId();
    }

    public function ApiImportResources($json): ApiActionResult
    {
        Log::Debug("ApiImportResources");
        /** @var ResourceApiDto[] $resources */
        $resources = $json->resources;

        $updatedResources = [];

        foreach ($resources as $api) {
            $attributeValues = [];
            if (!empty($api->attributeValues)) {
                foreach ($api->attributeValues as $av) {
                    $attributeValues[] = new AttributeValue(intval($av->id), apiencode($av->value));
                }
            }

            $validation = $this->attributeService->Validate(CustomAttributeCategory::RESOURCE, $attributeValues, [], false, true);
            if (!$validation->IsValid()) {
                continue;
            }
            $id = intval($api->id);
            $name = apiencode($api->name);
            $scheduleId = intval($api->scheduleId);
            $autoPermission = BooleanConverter::ConvertValue($api->autoPermission);
            $sortOrder = intval($api->sortOrder);

            if (!empty($id)) {
                $resource = $this->resourceRepository->LoadById($id);
            } else {
                $newResource = BookableResource::CreateNew($name, $scheduleId, $autoPermission, $sortOrder);
                $newId = $this->resourceRepository->Add($newResource);
                $resource = $this->resourceRepository->LoadById($newId);
            }

            $resource->SetName(empty($name) ? "no name" : $name);
            $resource->SetScheduleId($scheduleId);
            $resource->SetAutoAssign($autoPermission);
            $resource->SetSortOrder($sortOrder);
            $resource->SetResourceTypeId($api->typeId);
            $resource->SetAdminGroupId($api->adminGroupId);
            $resource->SetRequiresApproval(BooleanConverter::ConvertValue($api->requiresApproval));
            $resource->SetCheckin(
                BooleanConverter::ConvertValue($api->requiresCheckin),
                $api->autoReleaseMinutes,
                BooleanConverter::ConvertValue($api->enableAutoExtend),
                BooleanConverter::ConvertValue($api->checkinLimitedToAdmins),
                $api->autoReleaseAction);
            $resource->SetAllowMultiday(BooleanConverter::ConvertValue($api->allowCrossDay));
            $resource->SetMaxConcurrentReservations(intval($api->maxConcurrent));
            $resource->SetBufferTime(TimeIntervalApiDto::FromApi($api->buffer));
            $resource->SetMaxLength(TimeIntervalApiDto::FromApi($api->maxDuration));
            $resource->SetMinLength(TimeIntervalApiDto::FromApi($api->minDuration));
            $resource->SetMinNoticeAdd(TimeIntervalApiDto::FromApi($api->noticeAdd));
            $resource->SetMinNoticeUpdate(TimeIntervalApiDto::FromApi($api->noticeUpdate));
            $resource->SetMinNoticeDelete(TimeIntervalApiDto::FromApi($api->noticeDelete));
            $resource->SetMaxNotice(TimeIntervalApiDto::FromApi($api->noticeEnd));
            $resource->SetColor(apiencode($api->color));
            $resource->SetContact(apiencode($api->contact));
            $resource->SetDescription(apiencode($api->description));
            $resource->SetLocation(apiencode($api->location));
            $resource->SetNotes(apiencode($api->notes));
            $resource->SetMaxParticipants(intval($api->capacity));
            $resource->SetMinParticipants(intval($api->minParticipants));
            $resource->SetResourceGroupIds(array_map('intval', $api->resourceGroupIds));
            $resource->SetSlotLabel(apiencode($api->slotLabel));
            $resource->ChangeRequiredResources(array_map('intval', $api->relationships->required));
            $resource->ChangeExcludedResources(array_map('intval', $api->relationships->excluded));
            $resource->ChangeExcludedTimeResources(array_map('intval', $api->relationships->excludedAtTime));
            $resource->ChangeAttributes($attributeValues);
            $resource->ChangeCredits(floatval($api->creditsOffPeak), floatval($api->creditsPeak), intval($api->creditsCalculated), BooleanConverter::ConvertValue($api->creditsChargedForBlockedSlots));
            if (BooleanConverter::ConvertValue($api->showToPublic)) {
                $resource->EnableSubscription();
            } else {
                $resource->DisableSubscription();
            }
            $this->resourceRepository->Update($resource);
            $updatedResources[] = ResourceApiDto::FromResource($resource);
        }

        return new ApiActionResult(true, $updatedResources);
    }

    public function ApiUpdateGroupTree($json): ApiActionResult
    {
        Log::Debug("ApiUpdateGroupTree");
        /** @var ResourceTreeUpdateApiDto[] $update */
        $update = $json->tree;

        $existingGroups = $this->resourceRepository->GetResourceGroupsList();
        $existingIds = [];
        $updatedIds = [];
        foreach ($existingGroups as $group) {
            $existingIds[] = $group->id;
        }

        $newGroupMap = [];

        foreach ($update as $item) {
            $parentId = null;
            if (BookedStringHelper::Contains($item->id, "new")) {
                // create all new group
                $group = $this->resourceRepository->AddResourceGroup(new ResourceGroup(null, apiencode($item->name)));
                $newGroupMap[$item->id] = $group->id;
                Log::Debug("Adding new resource group.", ['name' => $item->name, 'id' => $item->id, 'groupId' => $group->id]);
            }
        }

        foreach ($update as $item) {
            $id = $item->id;
            $parentId = $item->parentId;
            if (BookedStringHelper::Contains($item->id, "new")) {
                $id = $newGroupMap[$item->id];
            }
            if (BookedStringHelper::Contains($item->parentId, "new")) {
                $parentId = $newGroupMap[$item->parentId];
            }
            $updatedGroup = new ResourceGroup($id, apiencode($item->name), $parentId);
            $this->resourceRepository->UpdateResourceGroup($updatedGroup);
            $updatedIds[] = $id;
            $this->resourceRepository->UpdateResourceGroupAssignments($id, $item->resourceIds);
        }

        $toDelete = array_diff($existingIds, $updatedIds);
        foreach ($toDelete as $id) {
            $this->resourceRepository->DeleteResourceGroup($id);
        }

        $resources = $this->resourceRepository->GetResourceList();
        $groups = $this->resourceRepository->GetResourceGroupsList();

        return new ApiActionResult(true, [
            'resources' => ResourceApiDto::FromList($resources),
            'resourceGroups' => ResourceGroupApiDto::FromList($groups),
        ]);
    }

    public function ApiAddResourceType($json): ApiActionResult
    {
        Log::Debug("ApiAddResourceType");

        /** @var ResourceTypeApiDto $add */
        $add = $json;

        $attributeValues = [];
        foreach ($add->attributeValues as $av) {
            $attributeValues[] = new AttributeValue(intval($av->id), apiencode($av->value));
        }

        $validation = $this->attributeService->Validate(CustomAttributeCategory::RESOURCE_TYPE, $attributeValues, [], false, true);
        if (!$validation->IsValid()) {
            return new ApiActionResult(false, null, new ApiErrorList($validation->Errors()));
        }

        $type = ResourceType::CreateNew(apiencode($add->name), apiencode($add->description));
        $typeId = $this->resourceRepository->AddResourceType($type);
        $type->WithId($typeId);
        $type->ChangeAttributes($attributeValues);
        $this->resourceRepository->UpdateResourceType($type);

        return new ApiActionResult(true, ResourceTypeApiDto::FromType($type));
    }

    public function ApiUpdateResourceType($json): ApiActionResult
    {
        Log::Debug("ApiUpdateResourceType");

        /** @var ResourceTypeApiDto $update */
        $update = $json;

        $attributeValues = [];
        foreach ($update->attributeValues as $av) {
            $attributeValues[] = new AttributeValue(intval($av->id), apiencode($av->value));
        }

        $validation = $this->attributeService->Validate(CustomAttributeCategory::RESOURCE_TYPE, $attributeValues, [], false, true);
        if (!$validation->IsValid()) {
            return new ApiActionResult(false, null, new ApiErrorList($validation->Errors()));
        }

        $type = new ResourceType(intval($update->id), apiencode($update->name), apiencode($update->description));
        $type->ChangeAttributes($attributeValues);
        $this->resourceRepository->UpdateResourceType($type);

        return new ApiActionResult(true, ResourceTypeApiDto::FromType($type));
    }

    public function ApiDeleteResourceType($json): ApiActionResult
    {
        Log::Debug("ApiDeleteResourceType");

        $typeId = $json->id;

        $this->resourceRepository->RemoveResourceType(intval($typeId));

        return new ApiActionResult(true, ["id" => $typeId]);
    }

    public function ApiAddResourceStatusReason($json): ApiActionResult
    {
        Log::Debug("ApiAddResourceStatusReason");
        /** @var ResourceStatusReasonUpdateApiDto $add */
        $add = $json;

        $this->resourceRepository->AddStatusReason(intval($add->statusId), apiencode($add->description));

        $statusReasons = $this->resourceRepository->GetStatusReasons();

        return new ApiActionResult(true, ResourceStatusApiDto::FromList($statusReasons));
    }

    public function ApiUpdateResourceStatusReason($json): ApiActionResult
    {
        Log::Debug("ApiUpdateResourceStatusReason");
        /** @var ResourceStatusReasonUpdateApiDto $update */
        $update = $json;

        $this->resourceRepository->UpdateStatusReason(intval($update->id), apiencode($update->description));

        $statusReasons = $this->resourceRepository->GetStatusReasons();

        return new ApiActionResult(true, ResourceStatusApiDto::FromList($statusReasons));
    }

    public function ApiDeleteResourceStatusReason($json): ApiActionResult
    {
        Log::Debug("ApiDeleteResourceStatusReason");
        $id = $json->id;

        $this->resourceRepository->RemoveStatusReason(intval($id));

        $statusReasons = $this->resourceRepository->GetStatusReasons();

        return new ApiActionResult(true, ResourceStatusApiDto::FromList($statusReasons));
    }

    public function ApiCopyResource($json): ApiActionResult
    {
        Log::Debug("ApiCopyResource");
        $id = intval($json->resourceId);
        $name = apiencode($json->resourceName);

        $resource = $this->resourceRepository->LoadById($id);
        $resource->AsCopy($name);
        $attributes = $this->attributeService->GetByCategory(CustomAttributeCategory::RESOURCE);
        foreach ($attributes as $attribute) {
            if (!$attribute->HasSecondaryEntities()) {
                $resource->AddAttributeValue(new AttributeValue($attribute->Id(), $resource->GetAttributeValue($attribute->Id())));
            }
        }
        $this->resourceRepository->Add($resource);
        $this->resourceRepository->Update($resource);

        $resourceId = $resource->GetResourceId();

        foreach ($resource->GetResourceGroupIds() as $groupId) {
            $this->resourceRepository->AddResourceToGroup($resourceId, $groupId);
        }

        $groups = $this->resourceRepository->GetGroupsWithPermission($id);

        /** @var GroupPermissionItemView $group */
        foreach ($groups->Results() as $group) {
            $this->resourceRepository->ChangeResourceGroupPermission($resourceId, $group->Id(), $group->PermissionType());
        }

        $users = $this->resourceRepository->GetUsersWithPermission($id);
        /** @var UserPermissionItemView $user */
        foreach ($users->Results() as $user) {
            $this->resourceRepository->ChangeResourceUserPermission($resourceId, $user->Id, $user->PermissionType());
        }

        $resource = $this->resourceRepository->LoadById($resource->GetId());
        return new ApiActionResult(true, ResourceApiDto::FromResource($resource));
    }

    public function PrintQRCode()
    {
        $qrGenerator = new QRGenerator();

        $resourceId = $this->page->GetResourceId();
        $resource = $this->resourceRepository->LoadByPublicId($resourceId);

        $imageUploadDir = new ImageUploadDirectory();
        $imageName = "/resourceqr{$resource->GetPublicId()}.png";
        $url = $imageUploadDir->GetPath() . $imageName;
        $savePath = $imageUploadDir->GetDirectory() . $imageName;

        $qrPath = sprintf('%s/%s?%s=%s', Configuration::Instance()->GetScriptUrl(), Pages::RESOURCE_QR_ROUTER, QueryStringKeys::PUBLIC_ID, $resourceId);
        $qrGenerator->SavePng($qrPath, $savePath);

        $this->page->ShowQRCode($url, $resource->GetName());
    }

    /**
     * @param string $fileName
     * @return string
     */
    private function GetResourceImageDirectory($fileName)
    {
        $imageUploadDirectory = Configuration::Instance()->GetKey(ConfigKeys::IMAGE_UPLOAD_DIRECTORY);

        $path = '';

        if (is_dir($imageUploadDirectory)) {
            $path = $imageUploadDirectory;
        } else {
            if (is_dir(ROOT_DIR . $imageUploadDirectory)) {
                $path = ROOT_DIR . $imageUploadDirectory;
            }
        }
        return $path = "$path/$fileName";
    }

    /**
     * @param int $resourceId
     * @param int $statusId
     * @param int|null $statusReasonId
     * @param string|null $statusReason
     * @param bool $notifyUsers
     * @param int|null $notifyDays
     * @param string|null $notifyMessage
     * @return BookableResource
     */
    private function UpdateResourceStatus(int     $resourceId, int $statusId, ?int $statusReasonId, ?string $statusReason, bool $notifyUsers, ?int $notifyDays,
                                          ?string $notifyMessage): BookableResource
    {
        Log::Debug('Changing resource status.', ['resourceId' => $resourceId]);

        $resource = $this->resourceRepository->LoadById($resourceId);

        if (empty($statusReasonId) && !empty($statusReason)) {
            $statusReasonId = $this->resourceRepository->AddStatusReason($statusId, $statusReason);
        }

        $resource->ChangeStatus($statusId, $statusReasonId);
        $this->resourceRepository->Update($resource);

        if ($notifyUsers) {
            $emails = array();
            $days = max(1, min($notifyDays, 180));

            Log::Debug("Sending resource status changed email to users.", ['days' => $days]);

            $reservations = $this->reservationViewRepository->GetReservations(Date::Now(), Date::Now()->AddDays($days), null, null, null, $resourceId);

            foreach ($reservations as $reservation) {
                $email = $reservation->OwnerEmailAddress;
                if (!array_key_exists($email, $emails)) {
                    $emails[$email] = 1;
                    ServiceLocator::GetEmailService()->Send(new ResourceStatusChangeEmail($email, $resource, $notifyMessage, $reservation->OwnerLanguage));
                }

            }
        }

        return $resource;
    }

    /**
     * @return bool
     */
    private function AreDetailsHidden(): bool
    {
        return !ServiceLocator::GetServer()->GetUserSession()->IsAdmin && Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_HIDE_USER_DETAILS, new BooleanConverter());
    }

    /**
     * @return bool
     */
    private function IsNameShown(): bool
    {
        return ServiceLocator::GetServer()->GetUserSession()->IsAdmin || Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_ALWAYS_SHOW_USER_NAME, new BooleanConverter());
    }
}