<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Common/namespace.php');
require_once(ROOT_DIR . 'Domain/Values/FullName.php');

class Reservation
{
    /**
     * @var string
     */
    protected $referenceNumber;

    /**
     * @return string
     */
    public function ReferenceNumber()
    {
        return $this->referenceNumber;
    }

    /**
     * @var Date
     */
    protected $startDate;

    /**
     * @return Date
     */
    public function StartDate()
    {
        return $this->startDate;
    }

    /**
     * @var Date
     */
    protected $endDate;

    /**
     * @return Date
     */
    public function EndDate()
    {
        return $this->endDate;
    }

    /**
     * @return DateRange
     */
    public function Duration()
    {
        return new DateRange($this->StartDate(), $this->EndDate());
    }

    /**
     * @var Date
     */
    protected $previousStart;

    /**
     * @return Date
     */
    public function PreviousStartDate()
    {
        return $this->previousStart;
    }

    /**
     * @var Date
     */
    protected $previousEnd;

    /**
     * @var int
     */
    protected $creditsRequired;


    /**
     * @return Date
     */
    public function PreviousEndDate()
    {
        return $this->previousEnd == null ? new NullDate() : $this->previousEnd;
    }

    protected $reservationId;

    public function ReservationId()
    {
        return $this->reservationId;
    }

    /**
     * @var array|int[]
     */
    private $participantIds = [];

    /**
     * @var array|int[]
     */
    protected $addedParticipants = [];

    /**
     * @var array|int[]
     */
    protected $removedParticipants = [];

    /**
     * @var array|int[]
     */
    protected $unchangedParticipants = [];

    /**
     * @var int[]
     */
    private $inviteeIds = [];

    /**
     * @var int[]
     */
    protected $addedInvitees = [];

    /**
     * @var int[]
     */
    protected $removedInvitees = [];

    /**
     * @var int[]
     */
    protected $unchangedInvitees = [];

    /**
     * @var int[]
     */
    protected $coOwnerIds = [];

    /**
     * @var int[]
     */
    protected $addedCoOwners = [];

    /**
     * @var int[]
     */
    protected $removedCoOwners = [];

    /**
     * @var int[]
     */
    protected $unchangedCoOwners = [];

    /**
     * @var string[]
     */
    private $invitedGuests = [];

    /**
     * @var string[]
     */
    protected $addedInvitedGuests = [];

    /**
     * @var string[]
     */
    protected $removedInvitedGuests = [];

    /**
     * @var string[]
     */
    protected $unchangedInvitedGuests = [];

    /**
     * @var string[]
     */
    private $_participatingGuests = [];

    /**
     * @var string[]
     */
    protected $addedParticipatingGuests = [];

    /**
     * @var string[]
     */
    protected $removedParticipatingGuests = [];

    /**
     * @var string[]
     */
    protected $unchangedParticipatingGuests = [];

    /**
     * @var Date|null
     */
    protected $checkinDate;

    /**
     * @var Date|null
     */
    protected $checkoutDate;

    /**
     * @var bool
     */
    protected $reservationDatesChanged = false;

    /**
     * @var ReservationSeries
     */
    public $series;

    public function __construct(ReservationSeries $reservationSeries, DateRange $reservationDate, $reservationId = null, $referenceNumber = null)
    {
        $this->series = $reservationSeries;

        $this->startDate = $reservationDate->GetBegin();
        $this->endDate = $reservationDate->GetEnd();

        $this->SetReferenceNumber($referenceNumber);

        if (!empty($reservationId)) {
            $this->SetReservationId($reservationId);
        }

        if (empty($referenceNumber)) {
            $this->SetReferenceNumber(ReferenceNumberGenerator::Generate());
        }

        $this->checkinDate = new NullDate();
        $this->checkoutDate = new NullDate();
        $this->previousStart = new NullDate();
        $this->previousEnd = new NullDate();
    }

    public function SetReservationId($reservationId)
    {
        $this->reservationId = $reservationId;
    }

    public function SetReferenceNumber($referenceNumber)
    {
        $this->referenceNumber = $referenceNumber;
    }

    public function SetReservationDate(DateRange $reservationDate)
    {
        $this->previousStart = $this->StartDate();
        $this->previousEnd = $this->EndDate();

        if (!$this->startDate->Equals($reservationDate->GetBegin()) || !$this->endDate->Equals($reservationDate->GetEnd())) {
            $this->reservationDatesChanged = true;
        }

        $this->startDate = $reservationDate->GetBegin();
        $this->endDate = $reservationDate->GetEnd();

        if ($this->previousStart != null && !($this->previousStart->Equals($reservationDate->GetBegin())) && $this->CheckinDate()->LessThan($this->startDate)) {
            $this->WithCheckin(new NullDate(), $this->CheckoutDate());
        }
    }

    /**
     * @param array|int[] $participantIds
     * @return void
     * @internal
     */
    public function WithParticipants($participantIds)
    {
        $this->participantIds = $participantIds;
        $this->unchangedParticipants = $participantIds;
    }

    /**
     * @param int $participantId
     */
    public function WithParticipant($participantId)
    {
        $this->participantIds[] = $participantId;
        $this->unchangedParticipants[] = $participantId;
    }

    /**
     * @param array|int[] $participantIds
     * @return void
     * @internal
     */
    public function WithCoOwners($coOwnerIds)
    {
        $this->coOwnerIds = $coOwnerIds;
        $this->unchangedCoOwners = $coOwnerIds;
    }

    /**
     * @param int $participantId
     */
    public function WithCoOwner($coOwnerId)
    {
        $this->coOwnerIds[] = $coOwnerId;
        $this->unchangedCoOwners[] = $coOwnerId;
    }

    /**
     * @param array|int[] $inviteeIds
     * @return void
     * @internal
     */
    public function WithInvitees($inviteeIds)
    {
        $this->inviteeIds = $inviteeIds;
        $this->unchangedInvitees = $inviteeIds;
    }

    /**
     * @param int $inviteeId
     */
    public function WithInvitee($inviteeId)
    {
        $this->inviteeIds[] = $inviteeId;
        $this->unchangedInvitees[] = $inviteeId;
    }

    /**
     * @return array|int[]
     */
    public function Participants()
    {
        return $this->participantIds;
    }

    /**
     * @return array|int[]
     */
    public function AddedParticipants()
    {
        return $this->addedParticipants;
    }

    /**
     * @return array|int[]
     */
    public function RemovedParticipants()
    {
        return $this->removedParticipants;
    }

    /**
     * @return array|int[]
     */
    public function UnchangedParticipants()
    {
        return $this->unchangedParticipants;
    }

    /**
     * @param array|int[] $participantIds
     * @return int total number changed
     */
    public function ChangeParticipants($participantIds)
    {
        $validParticipantIds = $this->FilterOutOwners($participantIds);

        $diff = new ArrayDiff($this->participantIds, $validParticipantIds);

        $this->addedParticipants = $diff->GetAddedToArray1();
        $this->removedParticipants = $diff->GetRemovedFromArray1();
        $this->unchangedParticipants = $diff->GetUnchangedInArray1();

        $this->participantIds = $validParticipantIds;

        return count($this->addedParticipants) + count($this->removedParticipants);
    }

    /**
     * @return array|int[]
     */
    public function CoOwners()
    {
        return $this->coOwnerIds;
    }

    /**
     * @return array|int[]
     */
    public function AddedCoOwners()
    {
        return $this->addedCoOwners;
    }

    /**
     * @return array|int[]
     */
    public function RemovedCoOwners()
    {
        return $this->removedCoOwners;
    }

    /**
     * @return array|int[]
     */
    public function UnchangedCoOwners()
    {
        return $this->unchangedCoOwners;
    }

    public function ChangeCoOwners($coOwnerIds)
    {
        $uniqueCoOwners = array_filter($coOwnerIds, function($id) { return $id !== $this->series->UserId(); });

        $diff = new ArrayDiff($this->coOwnerIds, $uniqueCoOwners);

        $this->addedCoOwners = $diff->GetAddedToArray1();
        $this->removedCoOwners = $diff->GetRemovedFromArray1();
        $this->unchangedCoOwners = $diff->GetUnchangedInArray1();

        $this->coOwnerIds = $uniqueCoOwners;

        return count($this->addedCoOwners) + count($this->removedCoOwners);
    }

    public function TotalParticipantCountIncludingOwner()
    {
        return count($this->Participants()) + count($this->ParticipatingGuests()) + 1;
    }

    /**
     * @param string $guest
     */
    public function WithInvitedGuest($guest)
    {
        $this->invitedGuests[] = strtolower($guest);
        $this->unchangedInvitedGuests[] = strtolower($guest);
    }

    /**
     * @param string $guest
     */
    public function WithParticipatingGuest($guest)
    {
        $this->_participatingGuests[] = strtolower($guest);
        $this->unchangedParticipatingGuests[] = strtolower($guest);
    }

    /**
     * @return array|int[]
     */
    public function Invitees()
    {
        return $this->inviteeIds;
    }

    /**
     * @return array|int[]
     */
    public function AddedInvitees()
    {
        return $this->addedInvitees;
    }

    /**
     * @return array|int[]
     */
    public function RemovedInvitees()
    {
        return $this->removedInvitees;
    }

    /**
     * @return array|int[]
     */
    public function UnchangedInvitees()
    {
        return $this->unchangedInvitees;
    }

    /**
     * @param array|int[] $inviteeIds
     * @return int total number changed
     */
    public function ChangeInvitees($inviteeIds)
    {
        $validInviteeIds = $this->FilterOutOwners($inviteeIds, $this->participantIds);

        $diff = new ArrayDiff($this->inviteeIds, $validInviteeIds);

        $this->addedInvitees = $diff->GetAddedToArray1();
        $this->removedInvitees = $diff->GetRemovedFromArray1();
        $this->unchangedInvitees = $diff->GetUnchangedInArray1();

        $this->inviteeIds = $validInviteeIds;

        return count($this->addedInvitees) + count($this->removedInvitees);
    }

    /**
     * @param int[] $userIds
     * @param int[] $additionalUserIdsToFilterOut
     * @return int[]
     */
    public function FilterOutOwners($userIds, $additionalUserIdsToFilterOut = [])
    {
        $validUserIds = [];
        foreach ($userIds as $userId) {
            if ($userId === $this->series->UserId() || in_array($userId, $this->coOwnerIds) || in_array($userId, $additionalUserIdsToFilterOut)) {
                continue;
            } else {
                $validUserIds[] = $userId;
            }
        }

        return $validUserIds;
    }

    /**
     * @param string[] $invitedGuests
     * @return int
     */
    public function ChangeInvitedGuests($invitedGuests)
    {
        $inviteeDiff = new ArrayDiff($this->invitedGuests, $invitedGuests);

        $this->addedInvitedGuests = $inviteeDiff->GetAddedToArray1();
        $this->removedInvitedGuests = $inviteeDiff->GetRemovedFromArray1();
        $this->unchangedInvitedGuests = $inviteeDiff->GetUnchangedInArray1();

        $this->invitedGuests = array_map('strtolower', $invitedGuests);

        return count($this->addedInvitedGuests) + count($this->removedInvitedGuests);
    }

    /**
     * @param string $email
     */
    public function RemoveInvitedGuest($email)
    {
        $newInvitees = array();

        foreach ($this->invitedGuests as $invitee) {
            if ($invitee != $email) {
                $newInvitees[] = $invitee;
            }
        }

        $this->ChangeInvitedGuests($newInvitees);
    }

    /**
     * @param string[] $participatingGuests
     * @return int
     */
    public function ChangeParticipatingGuests($participatingGuests)
    {
        $participantDiff = new ArrayDiff($this->_participatingGuests, $participatingGuests);

        $this->addedParticipatingGuests = $participantDiff->GetAddedToArray1();
        $this->removedParticipatingGuests = $participantDiff->GetRemovedFromArray1();
        $this->unchangedParticipatingGuests = $participantDiff->GetUnchangedInArray1();

        $this->_participatingGuests = array_map('strtolower', $participatingGuests);

        return count($this->addedParticipatingGuests) + count($this->removedParticipatingGuests);
    }

    /**
     * @return string[]
     */
    public function AddedInvitedGuests()
    {
        return $this->addedInvitedGuests;
    }

    /**
     * @return string[]
     */
    public function RemovedInvitedGuests()
    {
        return $this->removedInvitedGuests;
    }

    /**
     * @return string[]
     */
    public function UnchangedInvitedGuests()
    {
        return $this->unchangedInvitedGuests;
    }

    /**
     * @return string[]
     */
    public function AddedParticipatingGuests()
    {
        return $this->addedParticipatingGuests;
    }

    /**
     * @return string[]
     */
    public function RemovedParticipatingGuests()
    {
        return $this->removedParticipatingGuests;
    }

    /**
     * @return string[]
     */
    public function UnchangedParticipatingGuests()
    {
        return $this->unchangedParticipatingGuests;
    }

    /**
     * @return string[]
     */
    public function ParticipatingGuests()
    {
        return $this->_participatingGuests;
    }

    /**
     * @return string[]
     */
    public function InvitedGuests()
    {
        return $this->invitedGuests;
    }

    /**
     * @return bool
     */
    public function IsNew()
    {
        return $this->ReservationId() == null;
    }

    /**
     * @param int $inviteeId
     * @return bool whether the invitation was accepted
     */
    public function AcceptInvitation($inviteeId)
    {
        if (in_array($inviteeId, $this->inviteeIds)) {
            $this->addedParticipants[] = $inviteeId;
            $this->participantIds[] = $inviteeId;
            $this->removedInvitees[] = $inviteeId;

            return true;
        }

        return false;
    }

    /**
     * @param int $userId
     * @return bool whether the user joined
     */
    public function JoinReservation($userId)
    {
        if (in_array($userId, $this->participantIds)) {
            // already participating
            return false;
        }

        if (in_array($userId, $this->inviteeIds)) {
            $this->removedInvitees[] = $userId;
        }

        $this->addedParticipants[] = $userId;
        $this->participantIds[] = $userId;

        return true;
    }

    /**
     * @param string $email
     * @return bool whether the user joined
     */
    public function JoinAsGuest($email)
    {
        $email = strtolower($email);
        if (in_array($email, $this->invitedGuests)) {
            $this->AcceptGuestInvitation($email);
        }

        if (in_array($email, $this->_participatingGuests) || in_array($email, $this->addedParticipatingGuests)) {
            return false;
        }

        $this->addedParticipatingGuests[] = $email;
        return true;
    }

    /**
     * @param int $inviteeId
     * @return bool whether the invitation was declined
     */
    public function DeclineInvitation($inviteeId)
    {
        if (in_array($inviteeId, $this->inviteeIds)) {
            $this->removedInvitees[] = $inviteeId;
            return true;
        }

        return false;
    }

    /**
     * @param string $email
     * @return bool whether the invitation was accepted
     */
    public function AcceptGuestInvitation($email)
    {
        $email = strtolower($email);
        if (in_array($email, $this->invitedGuests)) {
            $this->addedParticipatingGuests[] = $email;
            $this->_participatingGuests[] = $email;
            $this->removedInvitedGuests[] = $email;

            return true;
        }

        return false;
    }

    /**
     * @param string $email
     * @return bool whether the invitation was declined
     */
    public function DeclineGuestInvitation($email)
    {
        $email = strtolower($email);
        if (in_array($email, $this->invitedGuests)) {
            $this->removedInvitedGuests[] = $email;
            return true;
        }

        return false;
    }

    /**
     * @param int $participantId
     * @return bool whether the participant was removed
     */
    public function CancelParticipation($participantId)
    {
        if (in_array($participantId, $this->participantIds)) {
            $this->removedParticipants[] = $participantId;
            $index = array_search($participantId, $this->participantIds);
            if ($index !== false) {
                array_splice($this->participantIds, $index, 1);
            }
            return true;
        }

        return false;
    }

    /**
     * @return Date|null
     */
    public function CheckinDate()
    {
        return $this->checkinDate == null ? new NullDate() : $this->checkinDate;
    }

    /**
     * @return bool
     */
    public function IsCheckedIn()
    {
        return $this->checkinDate != null && $this->checkinDate->ToString() != '';
    }

    public function Checkin()
    {
        $this->checkinDate = Date::Now();
    }

    /**
     * @return Date|null
     */
    public function CheckoutDate()
    {
        return $this->checkoutDate == null ? new NullDate() : $this->checkoutDate;
    }

    /**
     * @return bool
     */
    public function IsCheckedOut()
    {
        return $this->checkoutDate != null && $this->checkoutDate->ToString() != '';
    }

    public function Checkout()
    {
        $this->previousEnd = $this->endDate;
        $this->endDate = Date::Now();
        $this->checkoutDate = Date::Now();
    }

    static function Compare(Reservation $res1, Reservation $res2)
    {
        return $res1->StartDate()->Compare($res2->StartDate());
    }

    /**
     * @param Date $checkinDate
     * @param Date $checkoutDate
     */
    public function WithCheckin(Date $checkinDate, Date $checkoutDate)
    {
        $this->checkinDate = $checkinDate;
        $this->checkoutDate = $checkoutDate;
    }

    /**
     * @return bool
     */
    public function WereDatesChanged()
    {
        return $this->reservationDatesChanged || empty($this->reservationId);
    }

    public function GetCreditsRequired()
    {
        if ($this->EndDate()->GreaterThan(Date::Now())) {
            return $this->creditsRequired;
        }
        return 0;
    }

    public function SetCreditsRequired($creditsRequired)
    {
        $this->creditsRequired = $creditsRequired;
    }

    private $creditsConsumed;

    public function WithCreditsConsumed($credits)
    {
        $this->creditsConsumed = $credits;
    }

    public function GetCreditsConsumed()
    {
        if ($this->EndDate()->GreaterThan(Date::Now())) {
            return empty($this->creditsConsumed) ? 0 : $this->creditsConsumed;
        }
        return 0;
    }
}

class ReferenceNumberGenerator
{

    /**
     * Just for testing
     * @var string
     */
    public static $__referenceNumber = null;

    public static function Generate()
    {
        if (self::$__referenceNumber == null) {
            return strtoupper(BookedStringHelper::Random(9));
//            return str_replace('.', '', uniqid('', true));
        }

        return self::$__referenceNumber;
    }
}