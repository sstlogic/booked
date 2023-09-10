<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Common/namespace.php');
require_once(ROOT_DIR . 'Domain/Events/ReservationEvents.php');

class ExistingReservationSeries extends ReservationSeries
{
    /**
     * @var ISeriesUpdateScope
     */
    protected $seriesUpdateStrategy;

    /**
     * @var array|SeriesEvent[]
     */
    protected $events = array();

    /**
     * @var array|int[]
     */
    private $_deleteRequestIds = array();

    /**
     * @var bool
     */
    private $_seriesBeingDeleted = false;

    /**
     * @var array|int[]
     */
    private $_updateRequestIds = array();

    /**
     * @var array|int[]
     */
    private $_removedAttachmentIds = array();

    /**
     * @var array|int[]
     */
    protected $attachmentIds = array();

    /**
     * @var array|string[]
     */
    protected $attachmentNames = array();

    /**
     * @var string
     */
    private $_deleteReason;

    /**
     * @var bool
     */
    private $durationChanged = false;

    public function __construct()
    {
        parent::__construct();
        $this->ApplyChangesTo(SeriesUpdateScope::FullSeries);
    }

    public function SeriesUpdateScope()
    {
        return $this->seriesUpdateStrategy->GetScope();
    }

    /**
     * @internal
     */
    public function WithId($seriesId)
    {
        $this->SetSeriesId($seriesId);
    }

    /**
     * @internal
     */
    public function WithOwner($userId)
    {
        $this->_userId = $userId;
    }

    /**
     * @internal
     */
    public function WithPrimaryResource(BookableResource $resource)
    {
        $this->_resource = $resource;
    }

    /**
     * @internal
     */
    public function WithTitle($title)
    {
        $this->_title = $title;
    }

    /**
     * @internal
     */
    public function WithDescription($description)
    {
        $this->_description = $description;
    }

    /**
     * @internal
     */
    public function WithResource(BookableResource $resource)
    {
        $this->AddResource($resource);
    }

    /**
     * @var IRepeatOptions
     * @internal
     */
    private $_originalRepeatOptions;

    /**
     * @internal
     */
    public function WithRepeatOptions(IRepeatOptions $repeatOptions)
    {
        $this->_originalRepeatOptions = $repeatOptions;
        $this->repeatOptions = $repeatOptions;
    }

    /**
     * @internal
     */
    public function WithCurrentInstance(Reservation $reservation)
    {
        if (!array_key_exists($this->GetNewKey($reservation), $this->instances)) {
            $this->originalCreditsConsumed += $reservation->GetCreditsConsumed();
        }

        $this->AddInstance($reservation);
        $this->SetCurrentInstance($reservation);
    }

    /**
     * @internal
     */
    public function WithInstance(Reservation $reservation)
    {
        if (!array_key_exists($this->GetNewKey($reservation), $this->instances)) {
            $this->originalCreditsConsumed += $reservation->GetCreditsConsumed();
        }
        $this->AddInstance($reservation);
    }

    /**
     * @param $statusId int|ReservationStatus
     * @return void
     */
    public function WithStatus($statusId)
    {
        $this->statusId = $statusId;
    }

    /**
     * @param ReservationAccessory $accessory
     * @return void
     */
    public function WithAccessory(ReservationAccessory $accessory)
    {
        $this->_accessories[] = $accessory;
    }

    /**
     * @param AttributeValue $attributeValue
     */
    public function WithAttribute(AttributeValue $attributeValue)
    {
        $this->AddAttributeValue($attributeValue);
    }

    /**
     * @param $fileId int
     * @param $extension string
     * @param $fileName string
     */
    public function WithAttachment($fileId, $extension, $fileName, $fileSize, $fileType)
    {
        $this->attachmentIds[$fileId] = $extension;
        $shouldAdd = true;
        foreach ($this->allAttachments as $attachment) {
            if ($attachment->FileId() == $fileId) {
                $shouldAdd = false;
            }
        }
        if ($shouldAdd) {
            $attachment = ReservationAttachment::Create($fileName, $fileType, $fileSize, null, $extension, $this->seriesId);
            $attachment->WithFileId($fileId);
            $this->allAttachments[] = $attachment;
        }
    }

    public function RemoveInstance(Reservation $reservation)
    {
        // todo: should this check to see if the instance is already marked for removal?
        $toRemove = $reservation;

        foreach ($this->_Instances() as $instance) {
            if ($instance->ReferenceNumber() == $reservation->ReferenceNumber() ||
                ($instance->StartDate()->Equals($reservation->StartDate()) && $instance->EndDate()->Equals($reservation->EndDate()))) {
                $toRemove = $instance;
                break;
            }
        }
        Log::Debug("Removing instance", ['referenceNumber' => $toRemove->ReferenceNumber()]);
        $removed = parent::RemoveInstance($toRemove);

//		if ($removed) {
        $this->AddEvent(new InstanceRemovedEvent($toRemove, $this));
        $this->_deleteRequestIds[] = $toRemove->ReservationId();
        $this->RemoveEvent(new InstanceAddedEvent($toRemove, $this));
//        }
        return true;
    }

    public function RequiresNewSeries()
    {
        return $this->seriesUpdateStrategy->RequiresNewSeries() && (count($this->instances) > 1 || $this->repeatOptions->RepeatType() == RepeatType::Custom);
    }

    /**
     * @return int|ReservationStatus
     */
    public function StatusId()
    {
        return $this->statusId;
    }

    /**
     * @param int $userId
     * @param BookableResource $resource
     * @param string $title
     * @param string $description
     * @param UserSession $updatedBy
     */
    public function Update($userId, BookableResource $resource, $title, $description, UserSession $updatedBy)
    {
        $this->_bookedBy = $updatedBy;

        if ($this->seriesUpdateStrategy->RequiresNewSeries()) {
            $this->AddEvent(new SeriesBranchedEvent($this));
            $this->Repeats($this->seriesUpdateStrategy->GetRepeatOptions($this));
        }

        if ($this->_resource->GetId() != $resource->GetId()) {
            $this->AddEvent(new ResourceRemovedEvent($this->_resource, $this));
            $this->AddEvent(new ResourceAddedEvent($resource, ResourceLevel::Primary, $this));
        }

        if ($this->UserId() != $userId) {
            $this->AddEvent(new OwnerChangedEvent($this, $this->UserId(), $userId));
        }

        $this->_userId = $userId;
        $this->_resource = $resource;
        $this->_title = $title;
        $this->_description = $description;
    }

    /**
     * @param DateRange $reservationDate
     */
    public function UpdateDuration(DateRange $reservationDate)
    {
        $currentDuration = $this->CurrentInstance()->Duration();

        if ($currentDuration->Equals($reservationDate)) {
            $this->durationChanged = false;
            return;
        }

        $this->durationChanged = true;

        $currentBegin = $currentDuration->GetBegin();
        $currentEnd = $currentDuration->GetEnd();

        $startTimeAdjustment = $currentBegin->GetDifference($reservationDate->GetBegin());
        $endTimeAdjustment = $currentEnd->GetDifference($reservationDate->GetEnd());

        Log::Debug('Updating duration for series', ['seriesId' => $this->SeriesId()]);

        if ($currentBegin->DateEquals($reservationDate->GetBegin())) {
            // only adjusting times, dates are adjusted by repeat changes
            foreach ($this->Instances() as $instance) {
                $newStart = $instance->StartDate()->ApplyDifference($startTimeAdjustment);
                $newEnd = $instance->EndDate()->ApplyDifference($endTimeAdjustment);

                $this->UpdateInstance($instance, new DateRange($newStart, $newEnd));
            }
        } else {
            $this->UpdateInstance($this->CurrentInstance(), $reservationDate);
        }
    }

    /**
     * @param SeriesUpdateScope|string $seriesUpdateScope
     */
    public function ApplyChangesTo($seriesUpdateScope, $isAdmin = false)
    {
        if ($isAdmin) {
            $this->seriesUpdateStrategy = SeriesUpdateScope::CreateAdminStrategy($seriesUpdateScope);
        } else {
            $this->seriesUpdateStrategy = SeriesUpdateScope::CreateStrategy($seriesUpdateScope);
        }
    }

    /**
     * @param IRepeatOptions $repeatOptions
     */
    public function Repeats(IRepeatOptions $repeatOptions)
    {
        if ($this->SeriesUpdateScope() == SeriesUpdateScope::ThisInstance) {
            $repeatOptions = new RepeatNone();
        }

        if ($this->repeatOptions->RepeatType() == RepeatType::Custom && $this->SeriesUpdateScope() != SeriesUpdateScope::ThisInstance) {
            $this->seriesUpdateStrategy = new SeriesUpdateScope_Custom(SeriesUpdateScope::CreateStrategy($this->SeriesUpdateScope()));
        }

        if ($this->seriesUpdateStrategy->ShouldEndOldSeries()) {
            $this->AddEvent(new SeriesRecurrenceTerminationChangedEvent($this, $this->CurrentInstance()->StartDate()));
        }

        if ($repeatOptions->Equals($this->repeatOptions) && !$this->durationChanged) {
            // no change to recurrence options
            return;
        }

        if ($repeatOptions->RepeatType() == RepeatType::None && $this->repeatOptions != RepeatType::None && $this->seriesUpdateStrategy->GetScope() != SeriesUpdateScope::ThisInstance) {
            // don't allow from recurring series to none
            return;
        }

        if ($this->seriesUpdateStrategy->CanChangeRepeatTo($this, $repeatOptions)) {
            Log::Debug('Updating recurrence for series', ['seriesId' => $this->SeriesId()]);

            $this->repeatOptions = $repeatOptions;
            $repeatDates = $repeatOptions->GetDates($this->CurrentInstance()->Duration()->ToTimezone($this->_bookedBy->Timezone));

            foreach ($this->instances as $instance) {
                // delete all reservation instances which will be replaced
                if ($this->seriesUpdateStrategy->ShouldInstanceBeRemoved($this, $instance, $repeatDates)) {
                    $this->RemoveInstance($instance);
                }
            }

            // create all future instances
            parent::Repeats($repeatOptions);
        }
    }

    /**
     * @param $resources array|BookableResource([]
     * @return void
     */
    public function ChangeResources($resources)
    {
        $diff = new ArrayDiff($this->_additionalResources, $resources);

        $added = $diff->GetAddedToArray1();
        $removed = $diff->GetRemovedFromArray1();

        /** @var $resource BookableResource */
        foreach ($added as $resource) {
            $this->AddEvent(new ResourceAddedEvent($resource, ResourceLevel::Additional, $this));
        }

        /** @var $resource BookableResource */
        foreach ($removed as $resource) {
            $this->AddEvent(new ResourceRemovedEvent($resource, $this));
        }

        $this->_additionalResources = $resources;
    }

    /**
     * @param UserSession $deletedBy
     * @param string $reason
     * @return void
     */
    public function Delete(UserSession $deletedBy, $reason = null)
    {
        $this->_bookedBy = $deletedBy;
        $this->_deleteReason = $reason;

        if (!$this->AppliesToAllInstances()) {
            $instances = $this->Instances();
            Log::Debug('Removing instances from series', ['count' => count($instances), 'seriesId' => $this->SeriesId()]);

            foreach ($instances as $instance) {
                $this->RemoveInstance($instance);
                $this->unusedCreditBalance += $instance->GetCreditsConsumed();
            }
        } else {
            Log::Debug("Removing series", ['seriesId' => $this->SeriesId()]);

            $this->_seriesBeingDeleted = true;
            $this->AddEvent(new SeriesDeletedEvent($this, $reason));
            foreach ($this->instances as $instance) {
                $this->unusedCreditBalance += $instance->GetCreditsConsumed();
            }
        }
    }

    /**
     * @param UserSession $approvedBy
     * @return void
     */
    public function Approve(UserSession $approvedBy)
    {
        $this->_bookedBy = $approvedBy;

        $this->statusId = ReservationStatus::Created;

        Log::Debug("Approving series", ['seriesId' => $this->SeriesId()]);

        $this->AddEvent(new SeriesApprovedEvent($this, $approvedBy));
    }

    /**
     * @return bool
     */
    private function AppliesToAllInstances()
    {
        return count($this->instances) == count($this->Instances());
    }

    public function UpdateBookedBy(UserSession $bookedBy)
    {
        $this->_bookedBy = $bookedBy;
    }

    public function Checkin(UserSession $checkedInBy)
    {
        $this->_bookedBy = $checkedInBy;
        $this->CurrentInstance()->Checkin();
        $this->AddEvent(new InstanceUpdatedEvent($this->CurrentInstance(), $this));
    }

    public function Checkout(UserSession $checkedInBy)
    {
        $this->_bookedBy = $checkedInBy;
        $this->CurrentInstance()->Checkout();
        $this->AddEvent(new InstanceUpdatedEvent($this->CurrentInstance(), $this));
    }

    /**
     * @param int|ReservationMeetingLinkType $meetingType
     * @param string $meetingUrl
     * @param string|null $meetingId
     */
    public function WithMeetingLink($meetingType, $meetingUrl, $meetingId)
    {
        $this->meetingLink = new ReservationMeetingLink($meetingType, $meetingUrl, $meetingId);
    }

    protected function AddNewInstance(DateRange $reservationDate)
    {
        if (!$this->InstanceStartsOnDate($reservationDate)) {
            Log::Debug('Adding instance for series.', ['seriesId' => $this->SeriesId(), 'reservationDate' => $reservationDate->ToString()]);

            $newInstance = parent::AddNewInstance($reservationDate);
            $this->AddEvent(new InstanceAddedEvent($newInstance, $this));
        }
    }

    /**
     * @internal
     */
    public function UpdateInstance(Reservation $instance, DateRange $newDate)
    {
        unset($this->instances[$this->CreateInstanceKey($instance)]);

        $instance->SetReservationDate($newDate);
        $this->AddInstance($instance);

        $this->RaiseInstanceUpdatedEvent($instance);

    }

    private function RaiseInstanceUpdatedEvent(Reservation $instance)
    {
        if (!$instance->IsNew()) {
            $this->AddEvent(new InstanceUpdatedEvent($instance, $this));
            $this->_updateRequestIds[] = $instance->ReservationId();
        }
    }

    /**
     * @return array|SeriesEvent[]
     */
    public function GetEvents()
    {
        $uniqueEvents = array_unique($this->events);
        usort($uniqueEvents, array('SeriesEvent', 'Compare'));

        return $uniqueEvents;
    }

    /**
     * @param string $eventType
     * @return SeriesEvent|null
     */
    public function GetEvent(string $eventType)
    {
        foreach ($this->events as $e) {
            if (is_a($e, $eventType)) {
                return $e;
            }
        }

        return null;
    }

    public function Instances()
    {
        return $this->seriesUpdateStrategy->Instances($this);
    }

    public function SortedInstances()
    {
        $instances = $this->Instances();
//		uasort($instances, array($this, 'SortReservations'));
        usort($instances, array($this, 'SortReservations'));

        return $instances;
    }

    /**
     * @internal
     */
    public function _Instances()
    {
        return $this->instances;
    }

    public function AddEvent(SeriesEvent $event)
    {
        $this->events[] = $event;
    }

    public function RemoveEvent(SeriesEvent $event)
    {
        foreach ($this->events as $i => $e) {
            if ($event == $e) {
                unset($this->events[$i]);
            }
        }
    }

    public function IsMarkedForDelete($reservationId)
    {
        return $this->_seriesBeingDeleted || in_array($reservationId, $this->_deleteRequestIds);
    }

    public function IsMarkedForUpdate($reservationId)
    {
        return in_array($reservationId, $this->_updateRequestIds);
    }

    /**
     * @param int[] $coOwnerIds
     * @return void
     */
    public function ChangeCoOwners($coOwnerIds)
    {
        foreach ($this->Instances() as $instance) {
            $numberChanged = $instance->ChangeCoOwners($coOwnerIds);
            if ($numberChanged != 0) {
                $this->RaiseInstanceUpdatedEvent($instance);
            }
        }
    }

    /**
     * @param int[] $participantIds
     * @return void
     */
    public function ChangeParticipants($participantIds)
    {
        foreach ($this->Instances() as $instance) {
            $numberChanged = $instance->ChangeParticipants($participantIds);
            if ($numberChanged != 0) {
                $this->RaiseInstanceUpdatedEvent($instance);
            }
        }
    }

    /**
     * @param int[] $inviteeIds
     * @return void
     */
    public function ChangeInvitees($inviteeIds)
    {
        foreach ($this->Instances() as $instance) {
            $numberChanged = $instance->ChangeInvitees($inviteeIds);
            if ($numberChanged != 0) {
                $this->RaiseInstanceUpdatedEvent($instance);
            }
        }
    }

    /**
     * @param string[] $invitedGuests
     * @param string[] $participatingGuests
     * @return void
     */
    public function ChangeGuests($invitedGuests, $participatingGuests)
    {
        foreach ($this->Instances() as $instance) {
            $invitedChanged = $instance->ChangeInvitedGuests($invitedGuests);
            $participatingChanged = $instance->ChangeParticipatingGuests($participatingGuests);

            if ($invitedChanged + $participatingChanged != 0) {
                $this->RaiseInstanceUpdatedEvent($instance);
            }
        }
    }

    /**
     * @param int $inviteeId
     * @return bool
     */
    public function AcceptInvitation($inviteeId)
    {
        $wasAccepted = $this->CurrentInstance()->AcceptInvitation($inviteeId);
        if ($wasAccepted) {
            $this->RaiseInstanceUpdatedEvent($this->CurrentInstance());
        }

        return $wasAccepted;
    }

    /**
     * @param int $inviteeId
     * @return bool
     */
    public function AcceptInvitationSeries($inviteeId)
    {
        $wasAccepted = false;
        foreach ($this->Instances() as $instance) {
            $accepted = $instance->AcceptInvitation($inviteeId);
            if ($accepted) {
                $wasAccepted = true;
                $this->RaiseInstanceUpdatedEvent($instance);
            }
        }

        return $wasAccepted;
    }

    /**
     * @param int $inviteeId
     * @return bool
     */
    public function DeclineInvitation($inviteeId)
    {
        $declined = $this->CurrentInstance()->DeclineInvitation($inviteeId);
        if ($declined) {
            $this->RaiseInstanceUpdatedEvent($this->CurrentInstance());
        }

        return $declined;
    }

    /**
     * @param int $inviteeId
     * @return bool
     */
    public function DeclineInvitationSeries($inviteeId)
    {
        $declined = false;
        foreach ($this->Instances() as $instance) {
            $ok = $instance->DeclineInvitation($inviteeId);
            if ($ok) {
                $declined = true;
                $this->RaiseInstanceUpdatedEvent($instance);
            }
        }

        return $declined;
    }

    /**
     * @param string $email
     */
    public function AcceptGuestInvitation($email)
    {
        if (!$this->GetAllowParticipation()) {
            return false;
        }

        $wasAccepted = $this->CurrentInstance()->AcceptGuestInvitation($email);
        if ($wasAccepted) {
            $this->RaiseInstanceUpdatedEvent($this->CurrentInstance());
        }

        return $wasAccepted;
    }

    /**
     * @param string $email
     */
    public function AcceptGuestInvitationSeries($email)
    {
        $wasAccepted = false;
        foreach ($this->Instances() as $instance) {
            $ok = $instance->AcceptGuestInvitation($email);
            if ($ok) {
                $wasAccepted = true;
                $this->RaiseInstanceUpdatedEvent($instance);
            }
        }

        return $wasAccepted;
    }

    /**
     * @param string $email
     * @param User $user
     */
    public function AcceptGuestAsUserInvitation($email, $user)
    {
        foreach ($this->Instances() as $instance) {
            $instance->RemoveInvitedGuest($email);

            $instance->WithInvitee($user->Id());
            $instance->AcceptInvitation($user->Id());

            $this->RaiseInstanceUpdatedEvent($instance);
        }
    }

    /**
     * @param string $email
     */
    public function DeclineGuestInvitation($email)
    {
        if (!$this->GetAllowParticipation()) {
            return false;
        }

        $declined = $this->CurrentInstance()->DeclineGuestInvitation($email);
        if ($declined) {
            $this->RaiseInstanceUpdatedEvent($this->CurrentInstance());
        }

        return $declined;
    }

    /**
     * @param string $email
     */
    public function DeclineGuestInvitationSeries($email)
    {
        $declined = false;
        foreach ($this->Instances() as $instance) {
            $ok = $instance->DeclineGuestInvitation($email);
            if ($ok) {
                $declined = true;
                $this->RaiseInstanceUpdatedEvent($instance);
            }
        }

        return $declined;
    }

    /**
     * @param int $participantId
     * @return bool
     */
    public function CancelAllParticipation($participantId)
    {
        $wasCancelled = false;
        foreach ($this->Instances() as $instance) {
            $ok = $instance->CancelParticipation($participantId);
            if ($ok) {
                $wasCancelled = true;
                $this->RaiseInstanceUpdatedEvent($instance);
            }
        }

        return $wasCancelled;
    }

    /**
     * @param int $participantId
     * @return bool
     */
    public function CancelInstanceParticipation($participantId)
    {
        $wasCancelled = $this->CurrentInstance()->CancelParticipation($participantId);
        if ($wasCancelled) {
            $this->RaiseInstanceUpdatedEvent($this->CurrentInstance());
        }

        return $wasCancelled;
    }

    /**
     * @param int $participantId
     * @return bool
     */
    public function JoinReservationSeries($participantId)
    {
        if (!$this->GetAllowParticipation()) {
            return false;
        }

        $joined = false;
        foreach ($this->Instances() as $instance) {
            $ok = $instance->JoinReservation($participantId);
            if ($ok) {
                $joined = true;
                $this->RaiseInstanceUpdatedEvent($instance);
            }
        }

        return $joined;
    }

    /**
     * @param int $participantId
     * @return bool
     */
    public function JoinReservation($participantId)
    {
        if (!$this->GetAllowParticipation()) {
            return false;
        }

        $joined = $this->CurrentInstance()->JoinReservation($participantId);
        if ($joined) {
            $this->RaiseInstanceUpdatedEvent($this->CurrentInstance());
        }

        return $joined;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function JoinSeriesAsGuest($email)
    {
        if (!$this->GetAllowParticipation()) {
            return false;
        }

        $joined = false;
        foreach ($this->Instances() as $instance) {
            $ok = $instance->JoinAsGuest($email);
            if ($ok) {
                $joined = true;
                $this->RaiseInstanceUpdatedEvent($instance);
            }
        }

        return $joined;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function JoinAsGuest($email)
    {
        if (!$this->GetAllowParticipation()) {
            return false;
        }

        $joined = $this->CurrentInstance()->JoinAsGuest($email);
        if ($joined) {
            $this->RaiseInstanceUpdatedEvent($this->CurrentInstance());
        }

        return $joined;
    }

    /**
     * @param array|ReservationAccessory[] $accessories
     * @return void
     */
    public function ChangeAccessories($accessories)
    {
        $diff = new ArrayDiff($this->_accessories, $accessories);

        $added = $diff->GetAddedToArray1();
        $removed = $diff->GetRemovedFromArray1();

        /** @var $accessory ReservationAccessory */
        foreach ($added as $accessory) {
            $this->AddEvent(new AccessoryAddedEvent($accessory, $this));
        }

        /** @var $accessory ReservationAccessory */
        foreach ($removed as $accessory) {
            $this->AddEvent(new AccessoryRemovedEvent($accessory, $this));
        }

        $this->_accessories = [];
        /** @var $accessory ReservationAccessory */
        foreach ($accessories as $accessory) {
            if (empty($accessory->QuantityReserved)) {
                $this->AddEvent(new AccessoryRemovedEvent($accessory, $this));
            } else {
                $this->_accessories[] = $accessory;
            }
        }
    }

    /**
     * @param $attribute AttributeValue
     */
    public function ChangeAttribute($attribute)
    {
        $this->AddEvent(new AttributeAddedEvent($attribute, $this));
        $this->AddEvent(new AttributeRemovedEvent($attribute, $this));
        $this->AddAttributeValue($attribute);
    }

    /**
     * @param $attributes AttributeValue[]|array
     */
    public function ChangeAttributes($attributes)
    {
        $diff = new ArrayDiff($this->_attributeValues, $attributes);

        $added = $diff->GetAddedToArray1();
        $removed = $diff->GetRemovedFromArray1();

        /** @var $attribute AttributeValue */
        foreach ($added as $attribute) {
            $this->AddEvent(new AttributeAddedEvent($attribute, $this));
        }

        $updatedIds = array_map(function ($a) {
            return $a->AttributeId;
        }, $attributes);
        /** @var $accessory ReservationAccessory */
        foreach ($removed as $attribute) {
            if (in_array($attribute->AttributeId, $updatedIds)) {
                $this->AddEvent(new AttributeRemovedEvent($attribute, $this));
                // updating value. if attribute isn't provided don't remove the value
            }
        }

        $this->_attributeValues = array();
        foreach ($attributes as $attribute) {
            $this->AddAttributeValue($attribute);
        }
    }

    /**
     * @param $fileId int
     */
    public function RemoveAttachment($fileId)
    {
        if (array_key_exists($fileId, $this->attachmentIds)) {
            $this->AddEvent(new AttachmentRemovedEvent($this, $fileId, $this->attachmentIds[$fileId]));
            $this->_removedAttachmentIds[] = $fileId;
        }
        $allAttachments = [];
        foreach ($this->allAttachments as $attachment) {
            if ($attachment->FileId() != $fileId) {
                $allAttachments = $attachment;
            }
        }
        $this->allAttachments = $allAttachments;
    }

    /**
     * @return array|int[]
     */
    public function RemovedAttachmentIds()
    {
        return $this->_removedAttachmentIds;
    }

    public function AddStartReminder(ReservationReminder $reminder)
    {
        if ($reminder->MinutesPrior() != $this->startReminder->MinutesPrior()) {
            $this->AddEvent(new ReminderAddedEvent($this, $reminder->MinutesPrior(), ReservationReminderType::Start));
            parent::AddStartReminder($reminder);
        }
    }

    public function AddEndReminder(ReservationReminder $reminder)
    {
        if ($reminder->MinutesPrior() != $this->endReminder->MinutesPrior()) {
            $this->AddEvent(new ReminderAddedEvent($this, $reminder->MinutesPrior(), ReservationReminderType::End));
            parent::AddEndReminder($reminder);
        }
    }

    public function RemoveStartReminder()
    {
        if ($this->startReminder->Enabled()) {
            $this->startReminder = ReservationReminder::None();
            $this->AddEvent(new ReminderRemovedEvent($this, ReservationReminderType::Start));
        }
    }

    public function RemoveEndReminder()
    {
        if ($this->endReminder->Enabled()) {
            $this->endReminder = ReservationReminder::None();
            $this->AddEvent(new ReminderRemovedEvent($this, ReservationReminderType::End));
        }
    }

    public function WithStartReminder(ReservationReminder $reminder)
    {
        $this->startReminder = $reminder;
    }

    public function WithEndReminder(ReservationReminder $reminder)
    {
        $this->endReminder = $reminder;
    }

    public function GetCreditsConsumed()
    {
        $consumed = 0;
        foreach ($this->Instances() as $instance) {
            $consumed += $instance->GetCreditsConsumed();
        }

        return $consumed;
    }

    /**
     * @var float
     */
    protected $unusedCreditBalance = 0;

    public function GetUnusedCreditBalance()
    {
        return $this->unusedCreditBalance;
    }

    public function GetDeleteReason()
    {
        return $this->_deleteReason;
    }

    /**
     * @var int
     */
    protected $originalCreditsConsumed = 0;

    public function GetOriginalCreditsConsumed()
    {
        return $this->originalCreditsConsumed;
    }

    public function RemoveMeetingLink()
    {
        if (!empty($this->meetingLink)) {
            $this->previousMeetingLink = $this->meetingLink->Clone();
            $this->meetingLink = null;
            $this->AddEvent(new MeetingLinkRemovedEvent($this, $this->previousMeetingLink));
        }
    }

    /**
     * @param ReservationMeetingLinkType|int $type
     * @param string|null $url
     */
    public function UpdateMeetingLink($type, $url)
    {
        if (empty($this->meetingLink)) {
            $this->meetingLink = new ReservationMeetingLink($type, $url);
            $this->AddEvent(new MeetingLinkAddedEvent($this));
            return;
        }

        $linkChanged = $this->meetingLink->Type() == ReservationMeetingLinkType::Link && $this->meetingLink->Url() != $url;
        $typeChanged = $this->meetingLink->Type() != $type;
        $newTypeAllowed = $type == $this->meetingLink->Type() || $type == ReservationMeetingLinkType::Link || $this->MeetingLink()->Type() == ReservationMeetingLinkType::Link;

        if ($newTypeAllowed && ($typeChanged || $linkChanged)) {
            $this->previousMeetingLink = $this->meetingLink->Clone();
            $this->meetingLink = new ReservationMeetingLink($type, $url);
            $this->AddEvent(new MeetingLinkUpdatedEvent($this, $this->previousMeetingLink));
        }
    }

    /**
     * @return ReservationMeetingLink|null
     */
    public function PreviousMeetingLink()
    {
        return $this->previousMeetingLink;
    }
}