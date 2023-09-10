<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once ROOT_DIR . 'Presenters/ApiDtos/ApiHelperFunctions.php';

class ResourceApiDto
{
    public $id;
    public $name;
    public $statusId;
    public $statusReasonId;
    public $typeId;
    public $scheduleId;
    public $adminGroupId;
    public $sortOrder;
    public $location;
    public $contact;
    public $description;
    public $color;
    public $textColor;
    public $images = [];
    public $notes;
    public $slotLabel;
    public $publicId;
    public $showToPublic = false;
    public $noticeAdd;
    public $noticeUpdate;
    public $noticeDelete;
    public $noticeEnd;
    public $autoPermission = false;
    public $requiresApproval = false;
    public $requiresCheckin = false;
    public $enableAutoExtend = false;
    public $autoReleaseMinutes;
    public $autoReleaseAction = null;
    public $allowConcurrent = false;
    public $maxConcurrent;
    public $capacity;
    public $minDuration;
    public $maxDuration;
    public $buffer;
    public $allowCrossDay = true;
    public $creditsOffPeak;
    public $creditsPeak;
    public $creditsCalculated;
    public $creditsChargedForBlockedSlots = false;
    public $minParticipants;
    /**
     * @var AttributeValueApiDto[]
     */
    public $attributeValues = [];
    /**
     * @var int[]
     */
    public $resourceGroupIds = [];
    public $relationships;
    public $rssUrl;
    public $webCalUrl;
    public $resourceDisplayUrl;
    public $checkinLimitedToAdmins;

    /**
     * @param BookableResource[] $resources
     * @param CustomAttribute[]|null $limitedAttributes
     * @return ResourceApiDto[]
     */
    public static function FromList(array $resources, $limitedAttributes = null): array
    {
        $limitedAttributeIds = null;
        if ($limitedAttributes) {
            $limitedAttributeIds = [];
            foreach ($limitedAttributes as $a) {
                $limitedAttributeIds[] = $a->Id();
            }
        }
        $dtos = [];
        foreach ($resources as $r) {
            $dtos[] = self::FromResource($r, $limitedAttributeIds);
        }

        return $dtos;
    }

    /**
     * @param BookableResource $resource
     * @param int[]|null $limitedAttributeIds
     * @return ResourceApiDto
     */
    public static function FromResource(BookableResource $resource, $limitedAttributeIds = null): ResourceApiDto
    {
        /** @var AttributeValue[] $attributeValues */
        $attributeValues = [];
        $resourceAttributeValues = $resource->GetAttributeValuesAll();
        if ($limitedAttributeIds != null) {
            foreach ($resourceAttributeValues as $av) {
                if (in_array($av->AttributeId, $limitedAttributeIds)) {
                    $attributeValues[] = $av;
                }
            }
        } else {
            $attributeValues = $resourceAttributeValues;
        }


        $dto = new ResourceApiDto();
        $dto->id = intval($resource->GetId());
        $dto->name = apidecode($resource->GetName());
        $dto->showToPublic = intval($resource->GetIsCalendarSubscriptionAllowed()) === 1;
        $dto->publicId = $resource->GetPublicId();
        $dto->slotLabel = apidecode($resource->GetSlotLabel());
        $dto->allowConcurrent = intval($resource->GetAllowConcurrentReservations()) == 1;
        $dto->allowCrossDay = intval($resource->GetAllowMultiday()) == 1;
        $dto->adminGroupId = intval($resource->GetAdminGroupId());
        $dto->attributeValues = AttributeValueApiDto::FromList($attributeValues);
        $dto->autoReleaseMinutes = is_null($resource->GetAutoReleaseMinutes()) ? null : intval($resource->GetAutoReleaseMinutes());
        $dto->autoReleaseAction = is_null($resource->GetAutoReleaseAction()) ? null : intval($resource->GetAutoReleaseAction());
        $dto->buffer = TimeIntervalApiDto::Create($resource->GetBufferTime());
        $dto->autoPermission = intval($resource->GetAutoAssign()) == 1;
        $dto->capacity = intval($resource->GetMaxParticipants());
        $dto->minParticipants = intval($resource->GetMinParticipants());
        $dto->color = $resource->GetColor();
        $dto->textColor = apidecode($resource->GetTextColor());
        $dto->contact = apidecode($resource->GetContact());
        $dto->requiresApproval = intval($resource->GetRequiresApproval()) == 1;
        $dto->creditsCalculated = $resource->GetCreditApplicability();
        $dto->creditsChargedForBlockedSlots = intval($resource->GetCreditsAlwaysCharged()) == 1;
        $dto->creditsOffPeak = floatval($resource->GetCredits());
        $dto->creditsPeak = floatval($resource->GetPeakCredits());
        $dto->description = apidecode($resource->GetDescription());
        $dto->images = empty($resource->GetImage()) ? [] : array_merge([$resource->GetImage()], $resource->GetImages());
        $dto->location = apidecode($resource->GetLocation());
        $dto->maxConcurrent = intval($resource->GetMaxConcurrentReservations());
        $dto->maxDuration = TimeIntervalApiDto::Create($resource->GetMaxLength());
        $dto->minDuration = TimeIntervalApiDto::Create($resource->GetMinLength());
        $dto->notes = apidecode($resource->GetNotes());
        $dto->noticeAdd = TimeIntervalApiDto::Create($resource->GetMinNoticeAdd());
        $dto->noticeUpdate = TimeIntervalApiDto::Create($resource->GetMinNoticeUpdate());
        $dto->noticeDelete = TimeIntervalApiDto::Create($resource->GetMinNoticeDelete());
        $dto->relationships = ResourceRelationshipsApiDto::Create(
            $resource->GetRequiredRelationships(),
            $resource->GetExcludedRelationships(),
            $resource->GetExcludedTimeRelationships(),
            $resource->GetRequiredOneWayRelationships());
        $dto->noticeEnd = TimeIntervalApiDto::Create($resource->GetMaxNotice());
        $dto->requiresCheckin = intval($resource->IsCheckInEnabled()) == 1;
        $dto->enableAutoExtend = intval($resource->IsAutoExtendEnabled()) == 1;
        $dto->resourceGroupIds = array_map('intval', $resource->GetResourceGroupIds());
        $dto->scheduleId = intval($resource->GetScheduleId());
        $dto->sortOrder = intval($resource->GetSortOrder());
        $dto->statusId = intval($resource->GetStatusId());
        $dto->statusReasonId = intval($resource->GetStatusReasonId());
        $dto->typeId = intval($resource->GetResourceTypeId());
        $dto->checkinLimitedToAdmins = intval($resource->GetCheckinLimitedToAdmins()) == 1;
        if ($resource->GetIsCalendarSubscriptionAllowed()) {
            $dto->rssUrl = $resource->GetSubscriptionUrl()->GetAtomUrl();
            $dto->webCalUrl = $resource->GetSubscriptionUrl()->GetWebcalUrl();
            $dto->resourceDisplayUrl = $resource->GetResourceDisplayUrl();
        } else {
            $dto->rssUrl = null;
            $dto->webCalUrl = null;
            $dto->resourceDisplayUrl = null;
        }

        return $dto;
    }
}