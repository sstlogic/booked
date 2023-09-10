<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once ROOT_DIR . 'Presenters/ApiDtos/ApiHelperFunctions.php';

class ReservationApiDto
{
    /**
     * @var string|null
     */
    public $referenceNumber;
    /**
     * @var int
     */
    public $ownerId;
    /**
     * @var int[]
     */
    public $resourceIds = [];
    /**
     * @var ReservationAccessoryApiDto[]
     */
    public $accessories = [];
    /**
     * @var string|null
     */
    public $title;
    /**
     * @var string|null
     */
    public $description;
    /**
     * @var string
     */
    public $start;
    /**
     * @var string
     */
    public $end;
    /**
     * @var ReservationRecurrenceApiDto
     */
    public $recurrence;
    /**
     * @var ReservationReminderApiDto|null
     */
    public $startReminder;
    /**
     * @var ReservationReminderApiDto|null
     */
    public $endReminder;
    /**
     * @var int[]
     */
    public $inviteeIds = [];
    /**
     * @var int[]
     */
    public $coOwnerIds = [];
    /**
     * @var int[]
     */
    public $participantIds = [];
    /**
     * @var string[]
     */
    public $guestEmails = [];
    /**
     * @var string[]
     */
    public $participantEmails = [];
    /**
     * @var boolean
     */
    public $allowSelfJoin;
    /**
     * @var ReservationAttachmentApiDto[]
     */
    public $attachments = [];
    /**
     * @var boolean
     */
    public $requiresApproval;
    /**
     * @var string|null
     */
    public $checkinDate;
    /**
     * @var string|null
     */
    public $checkoutDate;
    /**
     * @var string|null
     */
    public $termsAcceptedDate;
    /**
     * @var AttributeValueApiDto[]
     */
    public $attributeValues = [];
    /**
     * @var ReservationMeetingLinkApiDto|null
     */
    public $meetingLink;

    /**
     * @param ReservationView $reservation
     * @return ReservationApiDto
     */
    public static function FromView(ReservationView $reservation, $timezone)
    {
        $dto = new ReservationApiDto();
        $dto->title = apidecode($reservation->Title);
        $dto->description = apidecode($reservation->Description);

        if ($reservation->StartReminder) {
            $dto->startReminder = ReservationReminderApiDto::FromView($reservation->StartReminder);
        } else {
            $dto->startReminder = ReservationReminderApiDto::FromConfiguration(Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_START_REMINDER));
        }
        if ($reservation->EndReminder) {
            $dto->endReminder = ReservationReminderApiDto::FromView($reservation->EndReminder);

        } else {
            $dto->endReminder = ReservationReminderApiDto::FromConfiguration(Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_END_REMINDER));
        }

        $dto->start = $reservation->StartDate->ToTimezone($timezone)->ToSystem();
        $dto->end = $reservation->EndDate->ToTimezone($timezone)->ToSystem();

        $dto->participantIds = array_map(function($u) {return self::user_map($u);}, $reservation->Participants);
        $dto->inviteeIds = array_map(function($u) {return self::user_map($u);}, $reservation->Invitees);
        $dto->coOwnerIds = array_map(function($u) {return self::user_map($u);}, $reservation->CoOwners);
        $dto->participantEmails = apidecode($reservation->ParticipatingGuests);
        $dto->guestEmails = apidecode($reservation->InvitedGuests);
        $dto->checkoutDate = $reservation->CheckoutDate->ToTimezone($timezone)->ToSystem();
        $dto->checkinDate = $reservation->CheckinDate->ToTimezone($timezone)->ToSystem();
        $dto->ownerId = intval($reservation->OwnerId);
        $dto->attachments = ReservationAttachmentApiDto::FromList($reservation->Attachments);
        $dto->accessories = ReservationAccessoryApiDto::FromList($reservation->Accessories);
        $dto->referenceNumber = $reservation->ReferenceNumber;
        $dto->allowSelfJoin = $reservation->AllowParticipation;
        $dto->resourceIds = array_map('intval', $reservation->ResourceIds());
        $dto->requiresApproval = $reservation->RequiresApproval();
        $dto->termsAcceptedDate = $reservation->TermsAcceptanceDate ? $reservation->TermsAcceptanceDate->ToTimezone($timezone)->ToSystem() : null;
        $dto->recurrence = ReservationRecurrenceApiDto::FromView($reservation, $timezone);
        $dto->meetingLink = ReservationMeetingLinkApiDto::FromView($reservation->MeetingLink);

        return $dto;
    }

    private static function user_map(ReservationUserView $u)
    {
        return intval($u->UserId);
    }
}

class ReservationRecurrenceApiDto
{
    /**
     * @var string
     */
    public $type = RepeatType::None;
    /**
     * @var int|null
     */
    public $interval = 1;
    /**
     * @var int[]|null
     */
    public $weekdays;
    /**
     * @var string|null
     */
    public $monthlyType;
    /**
     * @var string|null
     */
    public $terminationDate;
    /**
     * @var string[]|null
     */
    public $repeatDates = [];

    /**
     * @param ReservationView $reservation
     * @return ReservationRecurrenceApiDto
     */
    public static function FromView(ReservationView $reservation, $timezone)
    {
        $dto = new ReservationRecurrenceApiDto();
        if ($reservation->RepeatType === RepeatType::None) {
            return $dto;
        }

        $dto->interval = intval($reservation->RepeatInterval);
        $dto->type = $reservation->RepeatType;
        $dto->monthlyType = $reservation->RepeatMonthlyType;
        $dto->terminationDate = $reservation->RepeatTerminationDate->ToTimezone($timezone)->ToSystem();
        $dto->repeatDates = array_map(function (Date $d) {
            return $d->ToSystem();
        }, $reservation->CustomRepeatDates);
        $dto->weekdays = empty($reservation->RepeatWeekdays) ? [] : array_map('intval', $reservation->RepeatWeekdays);

        return $dto;
    }

    /**
     * @return ReservationRecurrenceApiDto
     */
    public static function None(): ReservationRecurrenceApiDto
    {
        $dto = new ReservationRecurrenceApiDto();
        $dto->type = RepeatType::None;
        return $dto;
    }
}

class ReservationReminderApiDto
{
    /**
     * @var int
     */
    public $value;
    /**
     * @var string
     */
    public $interval;

    /**
     * @param int $value
     * @param string $interval
     * @return ReservationReminderApiDto
     */
    public static function Create($value, $interval): ReservationReminderApiDto
    {
        $dto = new ReservationReminderApiDto();
        $dto->value = $value;
        $dto->interval = $interval;
        return $dto;
    }

    /**
     * @param ReservationReminderView|null $reminder
     * @return ReservationReminderApiDto|null
     */
    public static function FromView(?ReservationReminderView $reminder): ?ReservationReminderApiDto
    {
        if (empty($reminder)) {
            return null;
        }

        $dto = new ReservationReminderApiDto();
        $dto->value = $reminder->GetValue();
        $dto->interval = $reminder->GetInterval();

        return $dto;
    }

    /**
     * @param string $configString
     * @return ReservationReminderApiDto|null
     */
    public static function FromConfiguration($configString)
    {
        $pieces = self::GetReminderPieces($configString);
        if (empty($pieces)) {
            return null;
        }

        $dto = new ReservationReminderApiDto();
        $dto->interval = $pieces['interval'];
        $dto->value = $pieces['value'];
        return $dto;
    }

    private static function GetReminderPieces($reminder)
    {
        if (!empty($reminder)) {
            $parts = explode(' ', strtolower($reminder));

            if (count($parts) == 2) {
                $interval = trim($parts[1]);
                $pieces['value'] = intval($parts[0]);
                $pieces['interval'] = ($interval == 'minutes' || $interval == 'hours' || $interval == 'days') ? $interval : 'minutes';
                return $pieces;
            }

            if (count($parts) == 1 && is_numeric($parts[0])) {
                $pieces['value'] = intval($parts[0]);
                $pieces['interval'] = 'minutes';
                return $pieces;
            }
        }

        return null;
    }
}

class ReservationAttachmentApiDto
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $name;

    /**
     * @param ReservationAttachmentView[] $attachments
     */
    public static function FromList(array $attachments)
    {
        $dtos = [];
        foreach ($attachments as $attachment) {
            $dto = new ReservationAttachmentApiDto();
            $dto->id = $attachment->FileId();
            $dto->name = $attachment->FileName();
            $dtos[] = $dto;
        }
        return $dtos;
    }
}