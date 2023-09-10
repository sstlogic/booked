<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/WebService/namespace.php');
require_once(ROOT_DIR . 'WebServices/Responses/RecurrenceRequestResponse.php');
require_once(ROOT_DIR . 'WebServices/Responses/ResourceItemResponse.php');
require_once(ROOT_DIR . 'WebServices/Responses/ReservationAccessoryResponse.php');
require_once(ROOT_DIR . 'WebServices/Responses/CustomAttributes/CustomAttributeResponse.php');
require_once(ROOT_DIR . 'WebServices/Responses/AttachmentResponse.php');
require_once(ROOT_DIR . 'WebServices/Responses/ReservationUserResponse.php');
require_once(ROOT_DIR . 'WebServices/Responses/ReminderRequestResponse.php');

class ReservationResponse extends RestResponse
{
    public $referenceNumber;
    public $startDate;
    public $endDate;
    public $title;
    public $description;
    public $requiresApproval;
    public $isRecurring;
    public $scheduleId;
    public $resourceId;
    /**
     * @var ReservationUserResponse
     */
    public $owner;
    /**
     * @var array|ReservationUserResponse[]
     */
    public $participants = [];
    /**
     * @var array|ReservationUserResponse[]
     */
    public $invitees = [];
    /**
     * @var array|ReservationUserResponse[]
     */
    public $coOwners = [];
    /**
     * @var string[]
     */
    public $participatingGuests = [];
    /**
     * @var string[]
     */
    public $invitedGuests = [];
    /**
     * @var array|CustomAttributeResponse[]
     */
    public $customAttributes = [];
    /**
     * @var RecurrenceRequestResponse
     */
    public $recurrenceRule;
    /**
     * @var array|AttachmentResponse[]
     */
    public $attachments = array();
    /**
     * @var array|ResourceItemResponse[]
     */
    public $resources = array();
    /**
     * @var array|ReservationAccessoryResponse[]
     */
    public $accessories = array();
    /**
     * @var ReminderRequestResponse
     */
    public $startReminder;
    /**
     * @var ReminderRequestResponse
     */
    public $endReminder;
    /**
     * @var bool
     */
    public $allowParticipation;

    public $checkInDate;
    public $checkOutDate;
    public $originalEndDate;
    public $isCheckInAvailable;
    public $isCheckoutAvailable;
    public $autoReleaseMinutes;
    public $meetingLink;

    /**
     * @param IRestServer $server
     * @param ReservationView $reservation
     * @param IPrivacyFilter $privacyFilter
     * @param IEntityAttributeList $attributes
     */
    public function __construct(IRestServer          $server,
                                ReservationView      $reservation,
                                IPrivacyFilter       $privacyFilter,
                                IEntityAttributeList $attributes)
    {
        $this->owner = ReservationUserResponse::Masked();

        $canViewUser = $privacyFilter->CanViewUser($server->GetSession(), $reservation);
        $canViewDetails = $privacyFilter->CanViewDetails($server->GetSession(), $reservation);

        $this->referenceNumber = $reservation->ReferenceNumber;
        $this->startDate = $reservation->StartDate->ToTimezone($server->GetSession()->Timezone)->ToIso();
        $this->endDate = $reservation->EndDate->ToTimezone($server->GetSession()->Timezone)->ToIso();
        $this->requiresApproval = $reservation->RequiresApproval();
        $this->isRecurring = $reservation->IsRecurring();
        $repeatTerminationDate = $reservation->RepeatTerminationDate != null ? $reservation->RepeatTerminationDate->ToIso() : null;
        $this->recurrenceRule = new RecurrenceRequestResponse($reservation->RepeatType, $reservation->RepeatInterval, $reservation->RepeatMonthlyType, $reservation->RepeatWeekdays, $repeatTerminationDate, array_map(function ($d) {
            return $d->ToIso();
        }, $reservation->CustomRepeatDates));
        $this->resourceId = $reservation->ResourceId;
        $this->scheduleId = $reservation->ScheduleId;
        $this->AddService($server, WebServices::GetSchedule, [WebServiceParams::ScheduleId => $reservation->ScheduleId]);

        foreach ($reservation->Resources as $resource) {
            $this->resources[$resource->Id()] = new ResourceItemResponse($server, $resource->Id(), $resource->GetName());
        }

        foreach ($reservation->Accessories as $accessory) {
            $this->accessories[] = new ReservationAccessoryResponse($server, $accessory->AccessoryId, $accessory->Name, $accessory->QuantityReserved, $accessory->QuantityAvailable);
        }

        if ($canViewDetails) {
            $this->title = apidecode($reservation->Title);
            $this->description = apidecode($reservation->Description);

            foreach ($attributes->GetAttributes($reservation->ReservationId) as $attribute) {
                $this->customAttributes[] = new CustomAttributeResponse($server, $attribute->Id(),
                    $attribute->Label(),
                    $reservation->GetAttributeValue($attribute->Id()));
            }
            foreach ($reservation->Attachments as $attachment) {
                $this->attachments[] = new AttachmentResponse($server, $attachment->FileId(), $attachment->FileName(), $reservation->ReferenceNumber);
            }
        }

        if ($canViewUser) {
            $this->owner = new ReservationUserResponse($server, $reservation->OwnerId, $reservation->OwnerFirstName,
                $reservation->OwnerLastName,
                $reservation->OwnerEmailAddress);
            foreach ($reservation->Participants as $participant) {
                $this->participants[] = new ReservationUserResponse($server, $participant->UserId,
                    $participant->FirstName,
                    $participant->LastName, $participant->Email);
            }
            foreach ($reservation->Invitees as $invitee) {
                $this->invitees[] = new ReservationUserResponse($server, $invitee->UserId,
                    $invitee->FirstName, $invitee->LastName,
                    $invitee->Email);
            }
            foreach ($reservation->CoOwners as $coOwner) {
                $this->coOwners[] = new ReservationUserResponse($server, $coOwner->UserId,
                    $coOwner->FirstName, $coOwner->LastName,
                    $coOwner->Email);
            }
            foreach ($reservation->ParticipatingGuests as $guest) {
                $this->participatingGuests[] = $guest;
            }
            foreach ($reservation->InvitedGuests as $guest) {
                $this->invitedGuests[] = $guest;
            }
        }

        if ($reservation->StartReminder != null) {
            $this->startReminder = new ReminderRequestResponse($reservation->StartReminder->GetValue(), $reservation->StartReminder->GetInterval());
        }
        if ($reservation->EndReminder != null) {
            $this->endReminder = new ReminderRequestResponse($reservation->EndReminder->GetValue(), $reservation->EndReminder->GetInterval());
        }

        if ($reservation->RequiresApproval()) {
            $this->AddService($server, WebServices::ApproveReservation, array(WebServiceParams::ReferenceNumber => $reservation->ReferenceNumber));
        }

        $this->allowParticipation = $reservation->AllowParticipation;

        $this->checkInDate = $reservation->CheckinDate->ToIso();
        $this->checkOutDate = $reservation->CheckoutDate->ToIso();
        $this->originalEndDate = $reservation->OriginalEndDate->ToIso();
        $this->isCheckInAvailable = $reservation->IsCheckinAvailable(false);
        $this->isCheckoutAvailable = $reservation->IsCheckoutAvailable(false);
        $this->autoReleaseMinutes = $reservation->AutoReleaseMinutes();
        $this->meetingLink = $reservation->MeetingLink ? $reservation->MeetingLink->Url() : null;
    }

    /**
     * @return ReservationResponse
     */
    public static function Example()
    {
        return new ExampleReservationResponse();
    }
}

class ExampleReservationResponse extends ReservationResponse
{
    public function __construct()
    {
        $this->accessories = [ReservationAccessoryResponse::Example()];
        $this->attachments = [AttachmentResponse::Example()];
        $this->customAttributes = [CustomAttributeResponse::Example()];
        $this->description = 'reservation description';
        $this->endDate = Date::Now()->ToIso();
        $this->invitees = [ReservationUserResponse::Example()];
        $this->isRecurring = true;
        $this->owner = ReservationUserResponse::Example();
        $this->participants = [ReservationUserResponse::Example()];
        $this->invitees = [ReservationUserResponse::Example()];
        $this->coOwners = [ReservationUserResponse::Example()];
        $this->participatingGuests = ['guest@email.com'];
        $this->invitedGuests = ['guest@email.com'];
        $this->recurrenceRule = RecurrenceRequestResponse::Example();
        $this->referenceNumber = 'refnum';
        $this->requiresApproval = true;
        $this->resourceId = 123;
        $this->resources = [ResourceItemResponse::Example()];
        $this->scheduleId = 123;
        $this->startDate = Date::Now()->ToIso();
        $this->title = 'reservation title';
        $this->startReminder = ReminderRequestResponse::Example();
        $this->endReminder = ReminderRequestResponse::Example();
        $this->meetingLink = "https://www.bookedscheduler.com";
    }
}