<?php
/**
 * Copyright 2013-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'WebServices/Requests/Resource/ResourceRequest.php');
require_once(ROOT_DIR . 'WebServices/Validators/ResourceRequestValidator.php');
require_once(ROOT_DIR . 'Domain/Access/ResourceRepository.php');

interface IResourceSaveController
{
    /**
     * @param ResourceRequest $request
     * @param WebServiceUserSession $session
     * @return ResourceControllerResult
     */
    public function Create($request, $session);

    /**
     * @param int $resourceId
     * @param ResourceRequest $request
     * @param WebServiceUserSession $session
     * @return ResourceControllerResult
     */
    public function Update($resourceId, $request, $session);

    /**
     * @param int $resourceId
     * @param WebServiceUserSession $session
     * @return ResourceControllerResult
     */
    public function Delete($resourceId, $session);
}

class ResourceSaveController implements IResourceSaveController
{
    /**
     * @var IResourceRepository
     */
    private $repository;

    /**
     * @var IResourceRequestValidator
     */
    private $validator;

    public function __construct(IResourceRepository $repository, IResourceRequestValidator $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    public function Create($request, $session)
    {
        $errors = $this->validator->ValidateCreateRequest($request);
        if (!empty($errors)) {
            return new ResourceControllerResult(null, $errors);
        }

        $newResource = BookableResource::CreateNew(apiencode($request->name), intval($request->scheduleId),
            intval($request->autoAssignPermissions), $request->sortOrder);
        $resourceId = $this->repository->Add($newResource);

        $resource = $this->BuildResource($request, $resourceId);
        $this->UpdateResource($request, $resource);
        $this->repository->Update($resource);

        return new ResourceControllerResult($resourceId, null);
    }

    public function Update($resourceId, $request, $session)
    {
        $errors = $this->validator->ValidateUpdateRequest($resourceId, $request);
        if (!empty($errors)) {
            return new ResourceControllerResult(null, $errors);
        }

        $resource = $this->repository->LoadById($resourceId);
        $this->UpdateResource($request, $resource);
        $this->repository->Update($resource);

        return new ResourceControllerResult($resourceId);
    }

    /**
     * @param int $resourceId
     * @param WebServiceUserSession $session
     * @return ResourceControllerResult
     */
    public function Delete($resourceId, $session)
    {
        $errors = $this->validator->ValidateDeleteRequest($resourceId);
        if (!empty($errors)) {
            return new ResourceControllerResult(null, $errors);
        }
        $resource = $this->repository->LoadById($resourceId);
        $this->repository->Delete($resource);

        return new ResourceControllerResult($resourceId);
    }

    /**
     * @param ResourceRequest $request
     * @param int $resourceId
     * @return BookableResource
     */
    private function BuildResource($request, $resourceId)
    {
        return new BookableResource($resourceId,
            apiencode($request->name),
            apiencode($request->location),
            apiencode($request->contact),
            apiencode($request->notes),
            $request->minLength,
            $request->maxLength,
            intval($request->autoAssignPermissions),
            intval($request->requiresApproval),
            intval($request->allowMultiday),
            intval($request->maxParticipants),
            $request->minNotice,
            $request->maxNotice,
            apiencode($request->description),
            intval($request->scheduleId));
    }

    /**
     * @param ResourceRequest $request
     * @param BookableResource $resource
     */
    private function UpdateResource($request, $resource)
    {
        $resource->SetName(apiencode($request->name));
        $resource->SetLocation(apiencode($request->location));
        $resource->SetContact(apiencode($request->contact));
        $resource->SetNotes(apiencode($request->notes));
        $resource->SetDescription(apiencode($request->description));
        $resource->SetScheduleId(intval($request->scheduleId));
        $resource->SetSortOrder($request->sortOrder);
        $resource->SetMaxNotice($request->maxNotice);
        $resource->SetMinLength($request->minLength);
        $resource->SetMaxLength($request->maxLength);
        $resource->SetAutoAssign(intval($request->autoAssignPermissions));
        $resource->SetRequiresApproval(intval($request->requiresApproval));
        $resource->SetAllowMultiday(intval($request->allowMultiday));
        $resource->SetMaxParticipants(intval($request->maxParticipants));
        $resource->SetCheckin($request->requiresCheckIn,
            $request->autoReleaseMinutes,
            $request->extendIfMissedCheckout,
            $request->checkinLimitedToAdmins,
            $request->autoReleaseAction);
        $resource->SetColor($request->color);
        $resource->ChangeCredits($request->credits, $request->peakCredits, $request->creditApplicability, $request->creditsChargedAllSlots);

        $attributes = [];
        foreach ($request->GetCustomAttributes() as $attribute) {
            $attributes[] = new AttributeValue($attribute->attributeId, apiencode($attribute->attributeValue));
        }
        $resource->ChangeAttributes($attributes);

        if (isset($request->statusId)) {
            $resource->ChangeStatus($request->statusId, $request->statusReasonId);
        }
        if (isset($request->maxConcurrentReservations)) {
            $resource->SetMaxConcurrentReservations(intval($request->maxConcurrentReservations));
        }
        if (isset($request->minParticipants)) {
            $resource->SetMinParticipants($request->minParticipants);
        }
        if (isset($request->typeId)) {
            $resource->SetResourceTypeId($request->typeId);
        }
        if (isset($request->slotLabel)) {
            $resource->SetSlotLabel($request->slotLabel);
        }
        if (isset($request->minNotice)) {
            $resource->SetMinNoticeAdd($request->minNotice);
        }
        if (isset($request->minNoticeAdd)) {
            $resource->SetMinNoticeAdd($request->minNoticeAdd);
        }
        if (isset($request->minNoticeUpdate)) {
            $resource->SetMinNoticeUpdate($request->minNoticeUpdate);
        }
        if (isset($request->minNoticeDelete)) {
            $resource->SetMinNoticeDelete($request->minNoticeDelete);
        }
        if (isset($request->adminGroupId)) {
            $resource->SetAdminGroupId($request->adminGroupId);
        }
        if (isset($request->bufferTime)) {
            $resource->SetBufferTime($request->bufferTime);
        }

        return $resource;
    }
}

class ResourceControllerResult
{
    private $resourceId;
    private $errors = array();

    public function __construct($resourceId, $errors = array())
    {
        $this->resourceId = $resourceId;
        $this->errors = $errors;
    }

    /**
     * @return bool
     */
    public function WasSuccessful()
    {
        return !empty($this->resourceId) && empty($this->errors);
    }

    /**
     * @return int
     */
    public function ResourceId()
    {
        return $this->resourceId;
    }

    /**
     * @return string[]
     */
    public function Errors()
    {
        return $this->errors;
    }
}