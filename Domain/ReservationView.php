<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/ReservationMeetingLinkView.php');

class ReservationView
{
    public $ReservationId;
    public $SeriesId;
    public $ReferenceNumber;
    public $ResourceId;
    public $ResourceName;
    public $ScheduleId;
    public $StatusId;
    /**
     * @var Date
     */
    public $StartDate;
    /**
     * @var Date
     */
    public $EndDate;
    /**
     * @var Date
     */
    public $DateCreated;
    /**
     * @var Date
     */
    public $DateModified;
    /**
     * @var Date
     */
    public $CheckinDate;
    /**
     * @var Date
     */
    public $CheckoutDate;
    /**
     * @var Date
     */
    public $OriginalEndDate;
    public $OwnerId;
    public $OwnerEmailAddress;
    public $OwnerPhone;
    public $OwnerFirstName;
    public $OwnerLastName;
    public $Title;
    public $Description;
    /**
     * @var string|RepeatType
     */
    public $RepeatType;
    /**
     * @var int
     */
    public $RepeatInterval;
    /**
     * @var array
     */
    public $RepeatWeekdays;
    /**
     * @var string|RepeatMonthlyType
     */
    public $RepeatMonthlyType;
    /**
     * @var Date
     */
    public $RepeatTerminationDate;
    /**
     * @var int[]
     */
    public $AdditionalResourceIds = [];

    /**
     * @var ReservationResourceView[]
     */
    public $Resources = [];

    /**
     * @var ReservationUserView[]
     */
    public $Participants = [];

    /**
     * @var ReservationUserView[]
     */
    public $Invitees = [];

    /**
     * @var ReservationUserView[]
     */
    public $CoOwners = [];

    /**
     * @var array|ReservationAccessoryView[]
     */
    public $Accessories = [];

    /**
     * @var array|AttributeValue[]
     */
    public $Attributes = [];

    /**
     * @var array|ReservationAttachmentView[]
     */
    public $Attachments = [];

    /**
     * @var ReservationReminderView|null
     */
    public $StartReminder;

    /**
     * @var ReservationReminderView|null
     */
    public $EndReminder;

    /**
     * @var string[]
     */
    public $ParticipatingGuests = array();

    /**
     * @var string[]
     */
    public $InvitedGuests = array();

    /**
     * @var bool
     */
    public $AllowParticipation = false;

    /**
     * @var int
     */
    public $CreditsConsumed;

    /**
     * @var bool
     */
    public $HasAcceptedTerms = false;

    /**
     * @var Date|null
     */
    public $TermsAcceptanceDate;

    /**
     * @var Date[]
     */
    public $CustomRepeatDates = [];

    public $CheckinLimitedToAdmin = false;

    /**
     * @var null|ReservationMeetingLinkView
     */
    public $MeetingLink = null;

    public function __construct()
    {
        $this->CheckinDate = new NullDate();
        $this->CheckoutDate = new NullDate();
        $this->OriginalEndDate = new NullDate();
    }

    /**
     * @param AttributeValue $attribute
     */
    public function AddAttribute(AttributeValue $attribute)
    {
        $this->Attributes[$attribute->AttributeId] = $attribute;
    }

    /**
     * @param $attributeId int
     * @return mixed
     */
    public function GetAttributeValue($attributeId)
    {
        if (array_key_exists($attributeId, $this->Attributes)) {
            return $this->Attributes[$attributeId]->Value;
        }

        return null;
    }

    /**
     * @return bool
     */
    public function IsRecurring()
    {
        return $this->RepeatType != RepeatType::None;
    }

    /**
     * @return bool
     */
    public function IsDisplayable()
    {
        return true; // some qualification should probably be made
    }

    /**
     * @return bool
     */
    public function RequiresApproval()
    {
        return $this->StatusId == ReservationStatus::Pending;
    }

    /**
     * @param ReservationAttachmentView $attachment
     */
    public function AddAttachment(ReservationAttachmentView $attachment)
    {
        $this->Attachments[] = $attachment;
    }

    public function IsCheckinEnabled()
    {
        foreach ($this->Resources as $resource) {
            if ($resource->IsCheckInEnabled()) {
                return true;
            }
        }

        return false;
    }

    public function IsCheckinAvailable($isAdmin)
    {
        if ($this->CheckinLimitedToAdmin && !$isAdmin) {
            return false;
        }

        if ($this->EndDate->LessThanOrEqual(Date::Now())) {
            return false;
        }

        $checkinMinutes = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_CHECKIN_MINUTES, new IntConverter());

        if ($this->CheckinDate->ToString() == '' && Date::Now()->AddMinutes($checkinMinutes)->GreaterThanOrEqual($this->StartDate)) {
            return $this->IsCheckinEnabled();
        }

        return false;
    }

    public function IsCheckoutAvailable($isAdmin)
    {
        if ($this->CheckinLimitedToAdmin && !$isAdmin) {
            return false;
        }

        if ($this->StartDate->LessThan(Date::Now()) &&
            $this->CheckoutDate->ToString() == '' &&
            $this->CheckinDate->ToString() != '') {
            return $this->IsCheckinEnabled();
        }

        return false;
    }

    /**
     * @return bool
     */
    public function IsMissedCheckIn()
    {
        return $this->IsCheckInEnabled() && Date::Now()->GreaterThan($this->StartDate) && $this->CheckinDate->IsNull();
    }

    /**
     * @return bool
     */
    public function IsMissedCheckOut()
    {
        return $this->IsCheckInEnabled() && Date::Now()->GreaterThan($this->EndDate) && $this->CheckoutDate->IsNull();
    }

    public function AutoReleaseMinutes()
    {
        $autoRelease = 0;
        foreach ($this->Resources as $resource) {
            $min = $resource->GetAutoReleaseMinutes();
            if (!empty($min) && ($autoRelease == 0 || $min < $autoRelease)) {
                $autoRelease = $min;
            }
        }

        if (!empty($autoRelease)) {
            return $autoRelease;
        }

        return null;
    }

    /**
     * @return array|int[]
     */
    public function ResourceIds()
    {
        if (empty($this->ResourceId)) {
            return [];
        }
        return array_merge([$this->ResourceId], $this->AdditionalResourceIds);
    }

    /**
     * @return ReservationView
     */
    public function Duplicate()
    {
        $dupe = new ReservationView();
        $dupe->Attributes = $this->Attributes;
        $dupe->OwnerId = $this->OwnerId;
        $dupe->CoOwners = $this->CoOwners;
        $dupe->OwnerEmailAddress = $this->OwnerEmailAddress;
        $dupe->OwnerFirstName = $this->OwnerFirstName;
        $dupe->OwnerLastName = $this->OwnerLastName;
        $dupe->OwnerPhone = $this->OwnerPhone;
        $dupe->ResourceId = $this->ResourceId;
        $dupe->ScheduleId = $this->ScheduleId;
        $dupe->StartDate = $this->StartDate;
        $dupe->EndDate = $this->EndDate;
        $dupe->StartReminder = $this->StartReminder;
        $dupe->EndReminder = $this->EndReminder;
        $dupe->AdditionalResourceIds = $this->AdditionalResourceIds;
        $dupe->Accessories = $this->Accessories;
        $dupe->CustomRepeatDates = $this->CustomRepeatDates;
        $dupe->RepeatInterval = $this->RepeatInterval;
        $dupe->RepeatMonthlyType = $this->RepeatMonthlyType;
        $dupe->RepeatTerminationDate = $this->RepeatTerminationDate;
        $dupe->RepeatType = $this->RepeatType;
        $dupe->RepeatWeekdays = $this->RepeatWeekdays;
        $dupe->InvitedGuests = $this->InvitedGuests;
        $dupe->Invitees = $this->Invitees;
        $dupe->Participants = $this->Participants;
        $dupe->ParticipatingGuests = $this->ParticipatingGuests;
        $dupe->Description = $this->Description;
        $dupe->Title = $this->Title;
        $dupe->ResourceName = $this->ResourceName;
        $dupe->AllowParticipation = $this->AllowParticipation;
        return $dupe;
    }
}

class NullReservationView extends ReservationView
{
    /**
     * @var NullReservationView
     */
    private static $instance;

    public static function Instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new NullReservationView();
        }

        return self::$instance;
    }

    public function IsDisplayable()
    {
        return false;
    }
}