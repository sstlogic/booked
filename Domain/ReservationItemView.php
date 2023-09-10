<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Values/ResourceProperties.php');

interface IReservedItemView
{
    /**
     * @return Date
     */
    public function GetStartDate();

    /**
     * @return Date
     */
    public function GetEndDate();

    /**
     * @return int
     */
    public function GetResourceId();

    /**
     * @return string
     */
    public function GetResourceName();

    /**
     * @return string[]
     */
    public function GetResourceNames();

    /**
     * @return int
     */
    public function GetId();

    /**
     * @param Date $date
     * @return bool
     */
    public function OccursOn(Date $date);

    /**
     * @return string
     */
    public function GetReferenceNumber();

    /**
     * @return TimeInterval|null
     */
    public function GetBufferTime();

    /**
     * @return bool
     */
    public function HasBufferTime();

    /**
     * @return DateRange
     */
    public function BufferedTimes();

    /**
     * @return null|string
     */
    public function GetColor();

    /**
     * @return string
     */
    public function GetTextColor();

    /**
     * @return string
     */
    public function GetBorderColor();

    /**
     * @return string
     */
    public function GetTitle();

    /**
     * @return string
     */
    public function GetUserName();

    /**
     * @return bool
     */
    public function RequiresCheckin();

    /**
     * @return bool
     */
    public function IsPending();

    /**
     * @param int $newMinutes
     * @return bool
     */
    public function GetIsNew($newMinutes);

    /**
     * @param int $updatedMinutes
     * @return bool
     */
    public function GetIsUpdated($updatedMinutes);

    /**
     * @param int $userId
     * @return bool
     */
    public function IsOwner($userId);

    /**
     * @param int $userId
     * @return bool
     */
    public function IsCoOwner($userId);

    /**
     * @return string
     */
    public function GetLabel();

    /**
     * @return int
     */
    public function GetScheduleId();

    /**
     * @param int $id
     * @return string|null
     */
    public function GetAttributeValue($id);

    /**
     * @return bool
     */
    public function IsCheckedOut();

    /**
     * @return bool
     */
    public function IsMissedCheckIn();

    /**
     * @return bool
     */
    public function IsCheckedIn();

    /**
     * @return bool
     */
    public function IsMissedCheckOut();

    /**
     * @return int
     */
    public function OwnerId();

    /**
     * @return int[]
     */
    public function CoOwnerIds();
}

class ReservationItemView extends stdClass implements IReservedItemView
{
    /**
     * @var string
     */
    public $ReferenceNumber;

    /**
     * @var Date
     */
    public $StartDate;

    /**
     * @var Date
     */
    public $EndDate;

    /**
     * @var DateRange
     */
    public $Date;

    /**
     * @var string
     */
    public $ResourceName;

    /**
     * @var int
     */
    public $ReservationId;

    /**
     * @var int|ReservationUserLevel
     */
    public $UserLevelId;

    /**
     * @var string
     */
    public $Title;

    /**
     * @var string
     */
    public $Description;

    /**
     * @var int
     */
    public $ScheduleId;

    /**
     * @var null|string
     */
    public $FirstName;

    /**
     * @var null|string
     */
    public $LastName;

    /**
     * @var null|int
     */
    public $UserId;

    /**
     * @var null|Date
     */
    public $CreatedDate;
    /**
     * alias of $CreatedDate
     * @var null|Date
     */
    public $DateCreated;
    /**
     * @var null|Date
     */
    public $ModifiedDate;
    /**
     * @var null|bool
     */
    public $IsRecurring;
    /**
     * @var null|bool
     */
    public $RequiresApproval;
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
     * @var int
     */
    public $OwnerId;
    /**
     * @var string
     */
    public $OwnerEmailAddress;
    /**
     * @var string
     */
    public $OwnerPhone;
    /**
     * @var string
     */
    public $OwnerOrganization;
    /**
     * @var string
     */
    public $OwnerPosition;

    /**
     * @var string
     */
    public $OwnerLanguage;
    /**
     * @var string
     */
    public $OwnerTimezone;

    /**
     * @var int
     */
    public $SeriesId;

    /**
     * @var array|int[]
     */
    public $CoOwnerIds = [];

    /** @var array|string[]
     */
    public $CoOwnerNames = [];

    /**
     * @var array|int[]
     */
    public $ParticipantIds = [];

    /** @var array|string[]
     */
    public $ParticipantNames = [];

    /**
     * @var array|int[]
     */
    public $InviteeIds = [];

    /**
     * @var array|string[]
     */
    public $InviteeNames = [];

    /**
     * @var CustomAttributes
     */
    public $Attributes;

    /**
     * @var UserPreferences
     */
    public $UserPreferences;

    /**
     * @var int
     */
    public $ResourceStatusId;

    /**
     * @var int|null
     */
    public $ResourceStatusReasonId;

    /**
     * @var ReservationReminderView|null
     */
    public $StartReminder;

    /**
     * @var ReservationReminderView|null
     */
    public $EndReminder;

    /**
     * @var string|null
     */
    public $ResourceColor;

    /**
     * @var int|null
     */
    public $ResourceId;

    /**
     * @var null|string
     */
    public $OwnerFirstName;

    /**
     * @var null|string
     */
    public $OwnerLastName;

    /**
     * @var Date
     */
    public $CheckinDate;

    /**
     * @var Date
     */
    public $CheckoutDate;

    /**
     * @var bool
     */
    public $IsCheckInEnabled;

    /**
     * @var int|null
     */
    public $AutoReleaseMinutes;

    /**
     * @var Date
     */
    public $OriginalEndDate;

    /**
     * @var int|null
     */
    public $CreditsConsumed;

    /**
     * @var string[]
     */
    public $ParticipatingGuests = [];

    /**
     * @var string[]
     */
    public $InvitedGuests = [];

    /**
     * @var string[]
     */
    public $ResourceNames = [];

    /**
     * @var int[]
     */
    public $ResourceIds = [];

    /**
     * @var null|int
     */
    public $ResourceAdminGroupId = null;

    /**
     * @var null|int
     */
    public $ScheduleAdminGroupId = null;

    /**
     * @var null|string
     */
    public $ResourceLabel = null;

    /**
     * @var bool
     */
    public $CheckinLimitedToAdmins = false;

    /**
     * @var int[]
     */
    public $AttachmentIds = [];

    /**
     * @var null|int
     */
    public $LastApproverId = null;

    /**
     * @var null|string
     */
    public $LastApproverName = null;
    /**
     * @var Date
     */
    public $LastApprovedDate;

    /**
     * @var int|null
     */
    private $bufferSeconds = 0;

    /**
     * @var int[]
     */
    private $ownerGroupIds = [];

    /**
     * @var string[]
     */
    private $ownerGroupNames = [];

    /**
     * @param $referenceNumber string
     * @param $startDate Date
     * @param $endDate Date
     * @param $resourceName string
     * @param $resourceId int
     * @param $reservationId int
     * @param $userLevelId int|ReservationUserLevel
     * @param $title string
     * @param $description string
     * @param $scheduleId int
     * @param $userFirstName string
     * @param $userLastName string
     * @param $userId int
     * @param $userPhone string
     * @param $userPosition string
     * @param $userOrganization string
     * @param $participant_list string
     * @param $invitee_list string
     * @param $attribute_list string
     * @param $preferences string
     * @param $resourceProperties string
     * @param $coowner_list string
     */
    public function __construct(
        $referenceNumber = null,
        $startDate = null,
        $endDate = null,
        $resourceName = null,
        $resourceId = null,
        $reservationId = null,
        $userLevelId = null,
        $title = null,
        $description = null,
        $scheduleId = null,
        $userFirstName = null,
        $userLastName = null,
        $userId = null,
        $userPhone = null,
        $userOrganization = null,
        $userPosition = null,
        $participant_list = null,
        $invitee_list = null,
        $attribute_list = null,
        $preferences = null,
        $resourceProperties = null,
        $coowner_list = null
    )
    {
        $this->DateCreated = new NullDate();
        $this->ModifiedDate = new NullDate();
        $this->OriginalEndDate = new NullDate();
        $this->CheckinDate = new NullDate();
        $this->CheckoutDate = new NullDate();

        $this->ReferenceNumber = $referenceNumber;
        $this->StartDate = $startDate;
        $this->EndDate = $endDate;
        $this->ResourceName = $resourceName;
        $this->ResourceNames[] = $resourceName;
        $this->ResourceId = $resourceId;
        $this->ResourceIds[] = $resourceId;
        $this->ReservationId = $reservationId;
        $this->Title = $title;
        $this->Description = $description;
        $this->ScheduleId = $scheduleId;
        $this->FirstName = $userFirstName;
        $this->OwnerFirstName = $userFirstName;
        $this->LastName = $userLastName;
        $this->OwnerLastName = $userLastName;
        $this->OwnerPhone = $userPhone;
        $this->OwnerOrganization = $userOrganization;
        $this->OwnerPosition = $userPosition;
        $this->UserId = $userId;
        $this->OwnerId = $userId;
        $this->UserLevelId = $userLevelId;

        if (!empty($startDate) && !empty($endDate)) {
            $this->Date = new DateRange($startDate, $endDate);
        }

        if (!empty($participant_list)) {
            $participants = explode('!sep!', $participant_list);

            foreach ($participants as $participant) {
                $pair = explode('=', $participant);

                $id = $pair[0];
                $name = $pair[1];
                $this->ParticipantIds[] = $id;
                $this->ParticipantNames[$id] = $name;
            }
        }

        if (!empty($invitee_list)) {
            $invitees = explode('!sep!', $invitee_list);

            foreach ($invitees as $invitee) {
                $pair = explode('=', $invitee);

                $id = $pair[0];
                $name = $pair[1];
                $this->InviteeIds[] = $id;
                $this->InviteeNames[$id] = $name;
            }
        }

        if (!empty($coowner_list)) {
            $coowners = explode('!sep!', $coowner_list);

            foreach ($coowners as $coowner) {
                $pair = explode('=', $coowner);

                $id = $pair[0];
                $name = $pair[1];
                $this->CoOwnerIds[] = $id;
                $this->CoOwnerNames[$id] = $name;
            }
        }

        $this->Attributes = CustomAttributes::Parse($attribute_list);
        $this->UserPreferences = UserPreferences::Parse($preferences);
        $this->ResourceLabel = ResourceProperties::Deserialize($resourceProperties)->SlotLabel;
    }

    /**
     * @static
     * @param $row array
     * @return ReservationItemView
     */
    public static function Populate($row)
    {
        $view = new ReservationItemView (
            $row[ColumnNames::REFERENCE_NUMBER],
            Date::FromDatabase($row[ColumnNames::RESERVATION_START]),
            Date::FromDatabase($row[ColumnNames::RESERVATION_END]),
            $row[ColumnNames::RESOURCE_NAME],
            $row[ColumnNames::RESOURCE_ID],
            $row[ColumnNames::RESERVATION_INSTANCE_ID],
            $row[ColumnNames::RESERVATION_USER_LEVEL],
            $row[ColumnNames::RESERVATION_TITLE],
            $row[ColumnNames::RESERVATION_DESCRIPTION],
            $row[ColumnNames::SCHEDULE_ID],
            $row[ColumnNames::OWNER_FIRST_NAME],
            $row[ColumnNames::OWNER_LAST_NAME],
            $row[ColumnNames::OWNER_USER_ID],
            $row[ColumnNames::OWNER_PHONE],
            $row[ColumnNames::OWNER_ORGANIZATION],
            $row[ColumnNames::OWNER_POSITION],
            $row[ColumnNames::PARTICIPANT_LIST],
            $row[ColumnNames::INVITEE_LIST],
            $row[ColumnNames::ATTRIBUTE_LIST],
            $row[ColumnNames::USER_PREFERENCES],
            $row[ColumnNames::RESOURCE_ADDITIONAL_PROPERTIES_ALIAS],
            $row[ColumnNames::COOWNER_LIST],
        );

        if (isset($row[ColumnNames::RESERVATION_CREATED])) {
            $view->CreatedDate = Date::FromDatabase($row[ColumnNames::RESERVATION_CREATED]);
            $view->DateCreated = Date::FromDatabase($row[ColumnNames::RESERVATION_CREATED]);
        }

        if (isset($row[ColumnNames::RESERVATION_MODIFIED])) {
            $view->ModifiedDate = Date::FromDatabase($row[ColumnNames::RESERVATION_MODIFIED]);
        }

        if (isset($row[ColumnNames::REPEAT_TYPE])) {
            $repeatConfig = RepeatConfiguration::Create($row[ColumnNames::REPEAT_TYPE],
                $row[ColumnNames::REPEAT_OPTIONS]);

            $view->RepeatType = $repeatConfig->Type;
            $view->RepeatInterval = $repeatConfig->Interval;
            $view->RepeatWeekdays = $repeatConfig->Weekdays;
            $view->RepeatMonthlyType = $repeatConfig->MonthlyType;
            $view->RepeatTerminationDate = $repeatConfig->TerminationDate;

            $view->IsRecurring = $row[ColumnNames::REPEAT_TYPE] != RepeatType::None;
        }

        if (isset($row[ColumnNames::RESERVATION_STATUS])) {
            $view->RequiresApproval = $row[ColumnNames::RESERVATION_STATUS] == ReservationStatus::Pending;
        }

        if (isset($row[ColumnNames::EMAIL])) {
            $view->OwnerEmailAddress = $row[ColumnNames::EMAIL];
        }

        if (isset($row[ColumnNames::SERIES_ID])) {
            $view->SeriesId = $row[ColumnNames::SERIES_ID];
        }

        if (isset($row[ColumnNames::RESOURCE_STATUS_REASON_ID])) {
            $view->ResourceStatusReasonId = $row[ColumnNames::RESOURCE_STATUS_REASON_ID];
        }

        if (isset($row[ColumnNames::RESOURCE_STATUS_ID_ALIAS])) {
            $view->ResourceStatusId = $row[ColumnNames::RESOURCE_STATUS_ID_ALIAS];
        }

        if (isset($row[ColumnNames::RESOURCE_BUFFER_TIME])) {
            $view->WithBufferTime($row[ColumnNames::RESOURCE_BUFFER_TIME]);
        }

        if (isset($row[ColumnNames::GROUP_LIST])) {
            $groupList = explode('!sep!', $row[ColumnNames::GROUP_LIST]);
            $ids = [];
            $names = [];
            foreach ($groupList as $pair) {
                $idName = explode('=', $pair);
                $ids[] = $idName[0];
                $names[] = $idName[1];
            }
            $view->WithOwnerGroupIds($ids);
            $view->WithOwnerGroupNames($names);
        }

        if (isset($row[ColumnNames::START_REMINDER_MINUTES_PRIOR])) {
            $view->StartReminder = new ReservationReminderView($row[ColumnNames::START_REMINDER_MINUTES_PRIOR]);
        }
        if (isset($row[ColumnNames::END_REMINDER_MINUTES_PRIOR])) {
            $view->EndReminder = new ReservationReminderView($row[ColumnNames::END_REMINDER_MINUTES_PRIOR]);
        }
        if (isset($row[ColumnNames::RESERVATION_COLOR])) {
            $view->ResourceColor = $row[ColumnNames::RESERVATION_COLOR];
        }
        if (isset($row[ColumnNames::GUEST_LIST])) {
            $guests = explode('!sep!', $row[ColumnNames::GUEST_LIST]);
            foreach ($guests as $guest) {
                $emailAndLevel = explode('=', $guest);
                if ($emailAndLevel[1] == ReservationUserLevel::INVITEE) {
                    $view->InvitedGuests[] = $emailAndLevel[0];
                } else {
                    $view->ParticipatingGuests[] = $emailAndLevel[0];
                }
            }
        }

        if (isset($row[ColumnNames::LANGUAGE_CODE])) {
            $view->OwnerLanguage = $row[ColumnNames::LANGUAGE_CODE];
        }
        if (isset($row[ColumnNames::TIMEZONE_NAME])) {
            $view->OwnerTimezone = $row[ColumnNames::TIMEZONE_NAME];
        }

        $view->CheckinDate = Date::FromDatabase($row[ColumnNames::CHECKIN_DATE]);
        $view->CheckoutDate = Date::FromDatabase($row[ColumnNames::CHECKOUT_DATE]);
        $view->OriginalEndDate = Date::FromDatabase($row[ColumnNames::PREVIOUS_END_DATE]);
        $view->IsCheckInEnabled = (bool)$row[ColumnNames::ENABLE_CHECK_IN];
        $view->AutoReleaseMinutes = $row[ColumnNames::AUTO_RELEASE_MINUTES];
        $view->CreditsConsumed = $row[ColumnNames::CREDIT_COUNT];
        $view->ResourceAdminGroupId = $row[ColumnNames::RESOURCE_ADMIN_GROUP_ID_RESERVATIONS];
        $view->ScheduleAdminGroupId = $row[ColumnNames::SCHEDULE_ADMIN_GROUP_ID_RESERVATIONS];
        if (isset($row[ColumnNames::CHECKIN_LIMITED_TO_ADMINS])) {
            $view->CheckinLimitedToAdmins = (bool)$row[ColumnNames::CHECKIN_LIMITED_TO_ADMINS];
        }

        if (isset($row[ColumnNames::ATTACHMENT_LIST])) {
            $view->AttachmentIds = explode(',', $row[ColumnNames::ATTACHMENT_LIST]);
        }

        if (isset($row[ColumnNames::APPROVER_USER_ID])) {
            $view->LastApproverId = empty($row[ColumnNames::APPROVER_USER_ID]) ? null : intval($row[ColumnNames::APPROVER_USER_ID]);
        }

        if (isset($row[ColumnNames::APPROVER_FIRST_NAME]) && isset($row[ColumnNames::APPROVER_LAST_NAME])) {
            $view->LastApproverName = empty($row[ColumnNames::APPROVER_FIRST_NAME]) ? null : FullName::AsString($row[ColumnNames::APPROVER_FIRST_NAME], $row[ColumnNames::APPROVER_LAST_NAME]);
        }

        if (isset($row[ColumnNames::DATE_APPROVED])) {
            $view->LastApprovedDate = Date::FromDatabase($row[ColumnNames::DATE_APPROVED]);
        }

        return $view;
    }

    /**
     * @param ReservationView $r
     * @return ReservationItemView
     */
    public static function FromReservationView(ReservationView $r)
    {
        $item = new ReservationItemView($r->ReferenceNumber,
            $r->StartDate,
            $r->EndDate,
            $r->ResourceName,
            $r->ResourceId,
            $r->ReservationId,
            ReservationUserLevel::OWNER,
            $r->Title,
            $r->Description,
            $r->ScheduleId,
            $r->OwnerFirstName,
            $r->OwnerLastName,
            $r->OwnerId,
            null, null, null, null, null, null, null, null, null);

        foreach ($r->CoOwners as $u) {
            $item->CoOwnerIds[] = $u->UserId;
        }

        foreach ($r->Participants as $u) {
            $item->ParticipantIds[] = $u->UserId;
        }

        foreach ($r->Invitees as $u) {
            $item->InviteeIds[] = $u->UserId;
        }

        foreach ($r->Attributes as $a) {
            $item->Attributes->Add($a->AttributeId, $a->Value);
        }

        $item->RepeatInterval = $r->RepeatInterval;
        $item->RepeatMonthlyType = $r->RepeatMonthlyType;
        $item->RepeatTerminationDate = $r->RepeatTerminationDate;
        $item->RepeatType = $r->RepeatType;
        $item->RepeatWeekdays = $r->RepeatWeekdays;
        $item->StartReminder = $r->StartReminder;
        $item->EndReminder = $r->EndReminder;
        $item->CreatedDate = $r->DateCreated;
        $item->DateCreated = $r->DateCreated;
        $item->ModifiedDate = $r->DateModified;
        $item->OwnerEmailAddress = $r->OwnerEmailAddress;
        $item->OwnerPhone = $r->OwnerPhone;
        foreach ($r->Attachments as $attachment) {
            $item->AttachmentIds[] = $attachment->FileId();
        }

        return $item;
    }

    /**
     * @param Date $date
     * @return bool
     */
    public function OccursOn(Date $date)
    {
        return $this->Date->OccursOn($date);
    }

    /**
     * @return Date
     */
    public function GetStartDate()
    {
        return $this->StartDate;
    }

    /**
     * @return Date
     */
    public function GetEndDate()
    {
        return $this->EndDate;
    }

    /**
     * @return int
     */
    public function GetReservationId()
    {
        return $this->ReservationId;
    }

    /**
     * @return int
     */
    public function GetResourceId()
    {
        return $this->ResourceId;
    }

    /**
     * @return string
     */
    public function GetReferenceNumber()
    {
        return $this->ReferenceNumber;
    }

    public function GetId()
    {
        return $this->GetReservationId();
    }

    /**
     * @return DateDiff
     */
    public function GetDuration()
    {
        return $this->StartDate->GetDifference($this->EndDate);
    }

    public function IsUserOwner($userId)
    {
        return $this->OwnerId() == $userId;
    }

    public function IsCoOwner($userId)
    {
        return in_array($userId, $this->CoOwnerIds);
    }

    /**
     * @param $userId int
     * @return bool
     */
    public function IsUserParticipating($userId)
    {
        return in_array($userId, $this->ParticipantIds);
    }

    /**
     * @param $userId int
     * @return bool
     */
    public function IsUserInvited($userId)
    {
        return in_array($userId, $this->InviteeIds);
    }

    public function GetResourceName()
    {
        return $this->ResourceName;
    }

    public function GetResourceNames()
    {
        return $this->ResourceNames;
    }

    /**
     * @param int $seconds
     */
    public function WithBufferTime($seconds)
    {
        $this->bufferSeconds = $seconds;
    }

    /**
     * @param int[] $ownerGroupIds
     */
    public function WithOwnerGroupIds($ownerGroupIds)
    {
        $this->ownerGroupIds = $ownerGroupIds;
    }

    /**
     * @param string[] $ownerGroupNames
     */
    public function WithOwnerGroupNames($ownerGroupNames)
    {
        $this->ownerGroupNames = $ownerGroupNames;
    }

    /**
     * @return bool
     */
    public function HasBufferTime()
    {
        return !empty($this->bufferSeconds);
    }

    /**
     * @return int[]
     */
    public function OwnerGroupIds()
    {
        return $this->ownerGroupIds;
    }

    /**
     * @return string[]
     */
    public function OwnerGroupNames()
    {
        return $this->ownerGroupNames;
    }

    /**
     * @param int $attributeId
     * @return null|string
     */
    public function GetAttributeValue($attributeId)
    {
        return $this->Attributes->Get($attributeId);
    }

    /**
     * @return TimeInterval
     */
    public function GetBufferTime()
    {
        return TimeInterval::Parse($this->bufferSeconds);
    }

    /**
     * @return DateRange
     */
    public function BufferedTimes()
    {
        if (!$this->HasBufferTime()) {
            return new DateRange($this->GetStartDate(), $this->GetEndDate());

        }

        $buffer = $this->GetBufferTime();
        return new DateRange($this->GetStartDate()->SubtractInterval($buffer),
            $this->GetEndDate()->AddInterval($buffer));
    }

    /**
     * @return DateRange
     */
    public function DateRange()
    {
        return new DateRange($this->GetStartDate(), $this->GetEndDate());
    }

    /**
     * @param Date $date
     * @return bool
     */
    public function CollidesWith(Date $date)
    {
        if ($this->HasBufferTime()) {
            $range = new DateRange($this->StartDate->SubtractInterval($this->GetBufferTime()),
                $this->EndDate->AddInterval($this->GetBufferTime()));
        } else {
            $range = new DateRange($this->StartDate, $this->EndDate);
        }

        return $range->Contains($date, false);
    }

    public function IsCheckinEnabled($isAdmin = false)
    {
        return $this->IsCheckInEnabled && (!$this->CheckinLimitedToAdmins || $isAdmin);
    }

    public function IsCheckedIn()
    {
        return $this->IsCheckInEnabled && !$this->CheckinDate->IsNull() && $this->CheckoutDate->IsNull();
    }

    public function IsCheckedOut()
    {
        return $this->IsCheckInEnabled && !$this->CheckoutDate->IsNull();
    }

    public function IsMissedCheckIn()
    {
        return $this->IsCheckInEnabled && Date::Now()->GreaterThan($this->StartDate) && $this->CheckinDate->IsNull();
    }

    public function IsMissedCheckOut()
    {
        return $this->IsCheckInEnabled && Date::Now()->GreaterThan($this->EndDate) && $this->CheckoutDate->IsNull();
    }

    public function RequiresCheckin()
    {
        $checkinMinutes = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_CHECKIN_MINUTES, new IntConverter());

        return ($this->CheckinDate->IsNull() &&
            $this->IsCheckInEnabled &&
            $this->EndDate->GreaterThan(Date::Now()) &&
            Date::Now()->AddMinutes($checkinMinutes)->GreaterThanOrEqual($this->StartDate)
        );
    }

    public function RequiresCheckOut()
    {
        if ($this->StartDate->LessThan(Date::Now()) &&
            $this->CheckoutDate->IsNull() &&
            !$this->CheckinDate->IsNull()) {
            return $this->IsCheckinEnabled();
        }

        return false;
    }

    /**
     * @var null|string
     */
    private $_color = null;

    /**
     * @var ReservationColorRule[]
     */
    private $_colorRules = array();

    /**
     * @param ReservationColorRule[] $colorRules
     */
    public function WithColorRules($colorRules = array())
    {
        $this->_colorRules = $colorRules;
    }

    /**
     * @return null|string
     */
    public function GetColor()
    {
        if ($this->RequiresApproval) {
            return '';
        }
        if ($this->_color == null) {
            $this->_color = "";
            // cache the color after the first call to prevent multiple iterations of this logic
            $userColor = $this->UserPreferences->Get(UserPreferences::RESERVATION_COLOR);
            $resourceColor = $this->ResourceColor;

            if (!empty($resourceColor)) {
                $this->_color = "$resourceColor";
            }

            if (!empty($userColor)) {
                $this->_color = "$userColor";
            }

            $lastPriority = 1000000;
            if (count($this->_colorRules) > 0) {
                foreach ($this->_colorRules as $rule) {
                    if ($rule->IsHigherPriority($lastPriority) && $rule->IsSatisfiedBy($this)) {
                        $lastPriority = $rule->Priority;
                        $this->_color = "{$rule->Color}";
                    }
                }
            }
        }

        if (!empty($this->_color) && !BookedStringHelper::StartsWith($this->_color, '#')) {
            $this->_color = "#$this->_color";
        }

        return $this->_color;
    }

    /**
     * @return string
     */
    public function GetTextColor()
    {
        if ($this->RequiresApproval) {
            return '';
        }
        $color = $this->GetColor();
        if (!empty($color)) {
            $contrastingColor = new ContrastingColor($color);
            return $contrastingColor->__toString();
        }

        return '';
    }

    /**
     * @return string
     */
    public function GetBorderColor()
    {
        $color = $this->GetColor();
        if (!empty($color)) {
            $contrastingColor = new AdjustedColor($color, 80);
            return $contrastingColor->__toString();
        }

        return '';
    }

    public function GetTitle()
    {
        return $this->Title;
    }

    public function GetUserName()
    {
        return new FullName($this->FirstName, $this->LastName);
    }

    public function GetScheduleId()
    {
        return $this->ScheduleId;
    }

    public function IsPending()
    {
        return $this->RequiresApproval;
    }

    public function GetIsNew($newMinutes)
    {
        $createdDate = $this->CreatedDate;
        return
            ($newMinutes > 0) &&
            (!empty($createdDate) && !$createdDate->IsNull()) &&
            ($this->CreatedDate->AddMinutes($newMinutes)->GreaterThanOrEqual(Date::Now()));
    }

    public function GetIsUpdated($updatedMinutes)
    {
        $modifiedDate = $this->ModifiedDate;
        return
            ($updatedMinutes > 0) &&
            (!empty($modifiedDate) && !$modifiedDate->IsNull()) &&
            ($modifiedDate->AddMinutes($updatedMinutes)->GreaterThanOrEqual(Date::Now()));
    }

    public function IsOwner($userId)
    {
        return $this->IsUserOwner($userId);
    }

    public function GetLabel()
    {
        return SlotLabelFactory::Create($this);
    }

    public function OwnerId()
    {
        return $this->OwnerId;
    }

    public function CoOwnerIds()
    {
        return $this->CoOwnerIds;
    }
}

class BlackoutItemView extends ReservationItemView
{
    /**
     * @var Date
     */
    public $StartDate;

    /**
     * @var Date
     */
    public $EndDate;

    /**
     * @var DateRange
     */
    public $Date;

    /**
     * @var int
     */
    public $ResourceId;

    /**
     * @var string
     */
    public $ResourceName;

    /**
     * @var int
     */
    public $InstanceId;

    /**
     * @var int
     */
    public $SeriesId;

    /**
     * @var string
     */
    public $Title;

    /**
     * @var string
     */
    public $Description;

    /**
     * @var int
     */
    public $ScheduleId;

    /**
     * @var null|string
     */
    public $FirstName;

    /**
     * @var null|string
     */
    public $LastName;

    /**
     * @var null|int
     */
    public $OwnerId;

    /**
     * @var RepeatConfiguration
     */
    public $RepeatConfiguration;

    /**
     * @var bool
     */
    public $IsRecurring;

    /**
     * @param int $instanceId
     * @param Date $startDate
     * @param Date $endDate
     * @param int $resourceId
     * @param int $ownerId
     * @param int $scheduleId
     * @param string $title
     * @param string $description
     * @param string $firstName
     * @param string $lastName
     * @param string $resourceName
     * @param int $seriesId
     * @param string $repeatOptions
     * @param string $repeatType
     */
    public function __construct(
        $instanceId,
        Date $startDate,
        Date $endDate,
        $resourceId,
        $ownerId,
        $scheduleId,
        $title,
        $description,
        $firstName,
        $lastName,
        $resourceName,
        $seriesId,
        $repeatOptions,
        $repeatType)
    {
        $this->InstanceId = $instanceId;
        $this->StartDate = $startDate;
        $this->EndDate = $endDate;
        $this->ResourceId = $resourceId;
        $this->OwnerId = $ownerId;
        $this->ScheduleId = $scheduleId;
        $this->Title = $title;
        $this->Description = $description;
        $this->FirstName = $firstName;
        $this->LastName = $lastName;
        $this->ResourceName = $resourceName;
        $this->SeriesId = $seriesId;
        $this->Date = new DateRange($startDate, $endDate);
        $this->RepeatConfiguration = RepeatConfiguration::Create($repeatType, $repeatOptions);
        $this->IsRecurring = !empty($repeatType) && $repeatType != RepeatType::None;
    }

    /**
     * @static
     * @param $row
     * @return BlackoutItemView
     */
    public static function Populate($row)
    {
        return new BlackoutItemView($row[ColumnNames::BLACKOUT_INSTANCE_ID],
            Date::FromDatabase($row[ColumnNames::BLACKOUT_START]),
            Date::FromDatabase($row[ColumnNames::BLACKOUT_END]),
            $row[ColumnNames::RESOURCE_ID],
            $row[ColumnNames::USER_ID],
            $row[ColumnNames::SCHEDULE_ID],
            $row[ColumnNames::BLACKOUT_TITLE],
            $row[ColumnNames::BLACKOUT_DESCRIPTION],
            $row[ColumnNames::FIRST_NAME],
            $row[ColumnNames::LAST_NAME],
            $row[ColumnNames::RESOURCE_NAME],
            $row[ColumnNames::BLACKOUT_SERIES_ID],
            $row[ColumnNames::REPEAT_OPTIONS],
            $row[ColumnNames::REPEAT_TYPE]);
    }

    /**
     * @return Date
     */
    public function GetStartDate()
    {
        return $this->StartDate;
    }

    /**
     * @return Date
     */
    public function GetEndDate()
    {
        return $this->EndDate;
    }

    /**
     * @return int
     */
    public function GetResourceId()
    {
        return $this->ResourceId;
    }

    /**
     * @return int
     */
    public function GetId()
    {
        return $this->InstanceId;
    }

    /**
     * @param Date $date
     * @return bool
     */
    public function OccursOn(Date $date)
    {
        return $this->Date->OccursOn($date);
    }

    public function GetResourceName()
    {
        return $this->ResourceName;
    }

    public function GetResourceNames()
    {
        return $this->ResourceNames;
    }

    public function GetReferenceNumber()
    {
        return '';
    }

    /**
     * @return int|null
     */
    public function GetBufferTime()
    {
        return null;
    }

    /**
     * @return bool
     */
    public function HasBufferTime()
    {
        return false;
    }

    /**
     * @return DateRange
     */
    public function BufferedTimes()
    {
        return new DateRange($this->GetStartDate(), $this->GetEndDate());
    }

    public function CollidesWith(Date $date)
    {
        return $this->BufferedTimes()->Contains($date, false);
    }

    public function GetColor()
    {
        return '';
    }

    public function GetTextColor()
    {
        return '';
    }

    public function GetBorderColor()
    {
        return '';
    }

    public function GetTitle()
    {
        return $this->Title;
    }

    public function GetUserName()
    {
        return new FullName($this->FirstName, $this->LastName);
    }

    public function RequiresCheckin()
    {
        return false;
    }

    public function GetScheduleId()
    {
        return $this->ScheduleId;
    }

    public function IsPending()
    {
        return false;
    }

    public function GetIsNew($newMinutes)
    {
        return false;
    }

    public function GetIsUpdated($updatedMinutes)
    {
        return false;
    }

    public function IsOwner($userId)
    {
        return false;
    }

    public function IsCoOwner($userId)
    {
        return false;
    }

    public function GetLabel()
    {
        return $this->GetTitle();
    }

    public function IsCheckedIn()
    {
        return false;
    }

    public function IsCheckedOut()
    {
        return false;
    }

    public function IsMissedCheckIn()
    {
        return false;
    }

    public function IsMissedCheckOut()
    {
        return false;
    }
}