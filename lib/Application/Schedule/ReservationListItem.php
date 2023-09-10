<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

class ReservationListItem
{
    /**
     * @var IReservedItemView
     */
    protected $item;

    public function __construct(IReservedItemView $reservedItem)
    {
        $this->item = $reservedItem;
    }

    /**
     * @return Date
     */
    public function StartDate()
    {
        return $this->item->GetStartDate();
    }

    /**
     * @return Date
     */
    public function EndDate()
    {
        return $this->item->GetEndDate();
    }

    /**
     * @return Date
     */
    public function BufferedStartDate()
    {
        if ($this->HasBufferTime()) {
            return $this->item->BufferedTimes()->GetBegin();
        }
        return $this->item->GetStartDate();
    }

    /**
     * @return Date
     */
    public function BufferedEndDate()
    {
        if ($this->HasBufferTime()) {
            return $this->item->BufferedTimes()->GetEnd();
        }
        return $this->item->GetEndDate();
    }

    public function OccursOn(Date $date)
    {
        return $this->item->OccursOn($date);
    }

    /**
     * @param SchedulePeriod $start
     * @param SchedulePeriod $end
     * @param Date $displayDate
     * @param int $span
     * @return IReservationSlot
     */
    public function BuildSlot(SchedulePeriod $start, SchedulePeriod $end, Date $displayDate, $span)
    {
        return new ReservationSlot($start, $end, $displayDate, $span, $this->item);
    }

    /**
     * @return int
     */
    public function ResourceId()
    {
        return $this->item->GetResourceId();
    }

    /**
     * @return int
     */
    public function Id()
    {
        return $this->item->GetId();
    }

    public function IsReservation()
    {
        return true;
    }

    public function ReferenceNumber()
    {
        return $this->item->GetReferenceNumber();
    }

    /**
     * @return null|TimeInterval
     */
    public function BufferTime()
    {
        return $this->item->GetBufferTime();
    }

    /**
     * @return bool
     */
    public function HasBufferTime()
    {
        $bufferTime = $this->BufferTime();
        return !empty($bufferTime) && $bufferTime->TotalSeconds() > 0;
    }

    /**
     * @param Date $date
     * @return bool
     */
    public function CollidesWith(Date $date)
    {
        if ($this->HasBufferTime()) {
            $range = new DateRange($this->StartDate()->SubtractInterval($this->BufferTime()),
                $this->EndDate()->AddInterval($this->BufferTime()));
        } else {
            $range = new DateRange($this->StartDate(), $this->EndDate());
        }

        return $range->Contains($date, false);
    }

    /**
     * @param DateRange $dateRange
     * @return bool
     */
    public function CollidesWithRange(DateRange $dateRange)
    {
        if ($this->HasBufferTime()) {
            $range = new DateRange($this->StartDate()->SubtractInterval($this->BufferTime()),
                $this->EndDate()->AddInterval($this->BufferTime()));
        } else {
            $range = new DateRange($this->StartDate(), $this->EndDate());
        }

        return $range->Overlaps($dateRange);
    }

    public function GetColor()
    {
        return $this->item->GetColor();
    }

    public function GetTextColor()
    {
        return $this->item->GetTextColor();
    }

    public function GetBorderColor()
    {
        return $this->item->GetBorderColor();
    }

    /**
     * @return string
     */
    public function GetTitle()
    {
        return $this->item->GetTitle();
    }

    /**
     * @return string
     */
    public function GetResourceName()
    {
        return $this->item->GetResourceName();
    }

    /**
     * @return string[]
     */
    public function GetResourceNames()
    {
        return $this->item->GetResourceNames();
    }

    /**
     * @return string
     */
    public function GetUserName()
    {
        return $this->item->GetUserName();
    }

    public function RequiresCheckin()
    {
        return $this->item->RequiresCheckin();
    }

    /**
     * @param $currentUser UserSession
     * @param $displayRange DateRange
     * @return ReservationListItemDto[]
     */
    public function AsDto($currentUser, $displayRange)
    {
        $dtos = [];

        $currentUserId = $currentUser->UserId;
        $timezone = $currentUser->Timezone;
        $format = Resources::GetInstance()->GetDateFormat('period_time');

        $displayRange = $displayRange->ToTimezone($timezone);
        $bufferedStartDate = $this->BufferedStartDate()->ToTimezone($timezone);
        $bufferedEndDate = $this->BufferedEndDate()->ToTimezone($timezone);
        $rangeStart = $displayRange->GetBegin()->GreaterThan($bufferedStartDate) ? $displayRange->GetBegin() : $bufferedStartDate;
        $rangeEnd = $displayRange->GetEnd()->LessThan($bufferedEndDate) ? $displayRange->GetEnd() : $bufferedEndDate;
        $startDate = $this->StartDate()->ToTimezone($timezone);
        $endDate = $this->EndDate()->ToTimezone($timezone);
        $bufferedStartDate = $this->BufferedStartDate()->ToTimezone($timezone);
        $bufferedEndDate = $this->BufferedEndDate()->ToTimezone($timezone);

        if ($bufferedStartDate->LessThan($rangeStart)) {
            $bufferedStartDate = $rangeStart->GetDate();
        }

        if ($startDate->LessThan($rangeStart)) {
            $startDate = $rangeStart->GetDate();
        }

        if ($bufferedEndDate->GreaterThan($rangeEnd)) {
            $bufferedEndDate = $rangeEnd->GetDate();
        }

        if ($endDate->GreaterThan($rangeEnd)) {
            $endDate = $rangeEnd->GetDate();
        }

        $dto = new ReservationListItemDto();
        $dto->StartDate = $startDate->Timestamp();
        $dto->EndDate = $endDate->Timestamp();
        $dto->StartDateString = $startDate->Format('Y-m-d');
        $dto->EndDateString = $endDate->Format('Y-m-d');
        $dto->StartTime = $startDate->Format($format);
        $dto->EndTime = $endDate->Format($format);
        $dto->Id = $this->Id();
        $dto->ReferenceNumber = $this->ReferenceNumber();
        $dto->ResourceId = $this->ResourceId();
        $dto->RequiresCheckin = $this->RequiresCheckin();
        $dto->BorderColor = $this->GetBorderColor();
        $dto->BackgroundColor = $this->GetColor();
        $dto->TextColor = $this->GetTextColor();
        $dto->IsReservation = $this->IsReservation();
        $dto->IsBuffered = false;
        $dto->IsBuffer = false;
        $dto->IsPending = $this->GetPending();
        $dto->IsNew = $this->GetIsNew();
        $dto->IsUpdated = $this->GetIsUpdated();
        $dto->IsOwner = $this->GetIsOwner($currentUserId);
        $dto->IsCoOwner = $this->GetIsCoOwner($currentUserId);
        $dto->IsParticipant = $this->GetIsParticipant($currentUserId);
        $dto->Label = $this->GetLabel();
        $dto->IsPast = $this->BufferedEndDate()->LessThan(Date::Now());
        $dto->IsAdmin = $currentUser->IsAdmin || $currentUser->IsGroupAdmin || $currentUser->IsResourceAdmin || $currentUser->IsScheduleAdmin;
        $dto->IsCheckedIn = $this->item->IsCheckedIn();
        $dto->IsCheckedOut = $this->item->IsCheckedOut();
        $dto->IsMissedCheckIn = $this->item->IsMissedCheckIn();
        $dto->IsMissedCheckOut = $this->item->IsMissedCheckOut();

        if ($this->HasBufferTime()) {
            $dto->IsBuffered = true;
            $dto->BufferedStartDate = $bufferedStartDate->Timestamp();
            $dto->BufferedEndDate = $bufferedEndDate->Timestamp();
            $dto->BufferedStartTime = $bufferedStartDate->Format($format);
            $dto->BufferedEndTime = $bufferedEndDate->Format($format);

            $buffer = new ReservationListItemDto();
            $buffer->StartDate = $bufferedStartDate->Timestamp();
            $buffer->StartTime = $bufferedStartDate->Format($format);
            $buffer->EndDate = $bufferedEndDate->Timestamp();
            $buffer->EndTime = $bufferedEndDate->Format($format);
            $buffer->IsReservation = false;
            $buffer->IsBuffer = true;
            $buffer->IsBuffered = false;
            $buffer->Id = $this->Id() . 'buffer';
            $buffer->ReferenceNumber = $this->ReferenceNumber();
            $buffer->ResourceId = $this->ResourceId();
            $buffer->Label = "";

            array_push($dtos, $dto, $buffer);
        } else {
            $dtos[] = $dto;
        }

        return $dtos;
    }

    private function GetIsNew()
    {
        $newMinutes = Configuration::Instance()->GetSectionKey(ConfigSection::SCHEDULE, ConfigKeys::SCHEDULE_UPDATE_HIGHLIGHT_MINUTES, new IntConverter());
        return $this->item->GetIsNew($newMinutes);
    }

    private function GetIsUpdated()
    {
        if ($this->GetIsNew()) {
            return false;
        }
        $updatedMinutes = Configuration::Instance()->GetSectionKey(ConfigSection::SCHEDULE, ConfigKeys::SCHEDULE_UPDATE_HIGHLIGHT_MINUTES, new IntConverter());
        return $this->item->GetIsUpdated($updatedMinutes);
    }

    private function GetPending()
    {
        return $this->item->IsPending();
    }

    private function GetIsOwner(int $userId)
    {
        return $this->item->IsOwner($userId);
    }

    private function GetIsCoOwner(int $userId)
    {
        return $this->item->IsCoOwner($userId);
    }

    private function GetLabel()
    {
        return $this->item->GetLabel();
    }

    private function GetIsParticipant(int $currentUserId)
    {
        return $this->item->IsUserParticipating($currentUserId);
    }

    /**
     * @param int $id
     * @return string|null
     */
    public function GetAttributeValue($id)
    {
        return $this->item->GetAttributeValue($id);
    }

    /**
     * @return int
     */
    public function OwnerId()
    {
        return $this->item->OwnerId();
    }

    /**
     * @return int[]
     */
    public function CoOwnerIds()
    {
        return $this->item->CoOwnerIds();
    }
}

class BufferItem extends ReservationListItem
{
    const LOCATION_BEFORE = 'begin';
    const LOCATION_AFTER = 'end';

    /**
     * @var string
     */
    private $location;

    /**
     * @var Date
     */
    private $startDate;

    /**
     * @var Date
     */
    private $endDate;

    public function __construct(ReservationListItem $item, $location)
    {
        parent::__construct($item->item);
        $this->item = $item;
        $this->location = $location;

        if ($this->IsBefore()) {
            $this->startDate = $this->item->StartDate()->SubtractInterval($this->item->BufferTime());
            $this->endDate = $this->item->StartDate();
        } else {
            $this->startDate = $this->item->EndDate();
            $this->endDate = $this->item->EndDate()->AddInterval($this->item->BufferTime());
        }
    }

    public function BuildSlot(SchedulePeriod $start, SchedulePeriod $end, Date $displayDate, $span)
    {
        return new BufferSlot($start, $end, $displayDate, $span, $this->item->item);
    }

    /**
     * @return Date
     */
    public function StartDate()
    {
        return $this->startDate;
    }

    /**
     * @return Date
     */
    public function EndDate()
    {
        return $this->endDate;
    }

    private function IsBefore()
    {
        return $this->location == self::LOCATION_BEFORE;
    }

    public function OccursOn(Date $date)
    {
        return $this->item->OccursOn($date);
    }

    public function Id()
    {
        return $this->Id() . 'buffer_' . $this->location;
    }

    public function IsReservation()
    {
        return false;
    }

    public function HasBufferTime()
    {
        return false;
    }

    public function BufferTime()
    {
        return 0;
    }
}

class BlackoutListItem extends ReservationListItem
{
    protected $blackoutItem;

    public function __construct(BlackoutItemView $item)
    {
        $this->blackoutItem = $item;
        parent::__construct($item);
    }

    /**
     * @param SchedulePeriod $start
     * @param SchedulePeriod $end
     * @param Date $displayDate
     * @param int $span
     * @return IReservationSlot
     */
    public function BuildSlot(SchedulePeriod $start, SchedulePeriod $end, Date $displayDate, $span)
    {
        return new BlackoutSlot($start, $end, $displayDate, $span, $this->blackoutItem);
    }

    public function IsReservation()
    {
        return false;
    }
}

class ReservationListItemDto
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
     * @var int
     */
    public $Id = 0;
    /**
     * @var string
     */
    public $ReferenceNumber;
    /**
     * @var int
     */
    public $ResourceId = 0;
    /**
     * @var bool
     */
    public $RequiresCheckin = false;
    /**
     * @var string
     */
    public $BorderColor;
    /**
     * @var string|null
     */
    public $BackgroundColor;
    /**
     * @var string
     */
    public $TextColor;
    /**
     * @var bool
     */
    public $IsReservation = false;
    /**
     * @var bool
     */
    public $IsBuffer = false;
    /**
     * @var bool
     */
    public $IsBuffered = false;
    /**
     * @var string
     */
    public $Label;
    /**
     * @var bool
     */
    public $IsPending = false;
    /**
     * @var bool
     */
    public $IsNew = false;
    /**
     * @var bool
     */
    public $IsUpdated = false;
    /**
     * @var bool
     */
    public $IsOwner = false;
    /**
     * @var bool
     */
    public $IsCoOwner = false;
    /**
     * @var bool
     */
    public $IsPast = false;
    /**
     * @var string
     */
    public $StartTime;
    /**
     * @var string
     */
    public $EndTime;
    /**
     * @var bool
     */
    public $IsParticipant = false;
    /**
     * @var bool
     */
    public $IsAdmin = false;
    /**
     * @var string|null
     */
    public $BufferedStartDate;
    /**
     * @var string|null
     */
    public $BufferedEndDate;
    /**
     * @var string|null
     */
    public $BufferedStartTime;
    /**
     * @var string|null
     */
    public $BufferedEndTime;
    /**
     * @var string
     */
    public $StartDateString;
    /**
     * @var string
     */
    public $EndDateString;
    /**
     * @var bool
     */
    public $IsCheckedIn = false;
    /**
     * @var bool
     */
    public $IsCheckedOut = false;
    /**
     * @var bool
     */
    public $IsMissedCheckIn = false;
    /**
     * @var bool
     */
    public $IsMissedCheckOut = false;
}