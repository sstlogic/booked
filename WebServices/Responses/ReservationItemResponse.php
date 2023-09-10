<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/WebService/namespace.php');

class ReservationItemResponse extends RestResponse
{
	public $referenceNumber;
	public $startDate;
	public $endDate;
	public $firstName;
	public $lastName;
	public $resourceName;
	public $title;
	public $description;
	public $requiresApproval;
	public $isRecurring;
	public $scheduleId;
	public $userId;
	public $resourceId;
	public $duration;
	public $bufferTime;
	public $bufferedStartDate;
	public $bufferedEndDate;
	public $participants = [];
	public $invitees = [];
	public $participatingGuests = [];
	public $invitedGuests = [];
	public $coOwners = [];
	public $startReminder;
	public $endReminder;
	public $color;
	public $textColor;
	public $checkInDate;
	public $checkOutDate;
	public $originalEndDate;
	public $isCheckInEnabled;
	public $autoReleaseMinutes;
	public $resourceStatusId;
    public $creditsConsumed;
    /**
     * @var CustomAttributeResponse[]
     */
    public $customAttributes = [];

    public function __construct(ReservationItemView $reservationItemView, IRestServer $server, $showUser, $showDetails)
	{
		$this->referenceNumber = $reservationItemView->ReferenceNumber;
		$this->startDate = $reservationItemView->StartDate->ToIso();
		$this->endDate = $reservationItemView->EndDate->ToIso();
		$this->duration = $reservationItemView->GetDuration()->__toString();
		$this->resourceName = apidecode($reservationItemView->ResourceName);

		if ($showUser)
		{
			$this->firstName = apidecode($reservationItemView->FirstName);
			$this->lastName = apidecode($reservationItemView->LastName);
			$this->participants = apidecode(array_values($reservationItemView->ParticipantNames));
			$this->invitees = apidecode(array_values($reservationItemView->InviteeNames));
			$this->participatingGuests = apidecode($reservationItemView->ParticipatingGuests);
			$this->invitedGuests = apidecode($reservationItemView->InvitedGuests);
            $this->coOwners = apidecode(array_values($reservationItemView->CoOwnerNames));
		}

		if ($showDetails)
		{
			$this->title = apidecode($reservationItemView->Title);
			$this->description = apidecode($reservationItemView->Description);

            foreach ($reservationItemView->Attributes->All() as $id => $value) {
                $this->customAttributes[] = new CustomAttributeResponse($server, $id, null, apidecode($value));
            }
		}

		$this->requiresApproval = (bool)$reservationItemView->RequiresApproval;
		$this->isRecurring = (bool)$reservationItemView->IsRecurring;

		$this->scheduleId = $reservationItemView->ScheduleId;
		$this->userId = $reservationItemView->UserId;
		$this->resourceId = $reservationItemView->ResourceId;
		$this->bufferTime = $reservationItemView->GetBufferTime()->__toString();
		$bufferedDuration = $reservationItemView->BufferedTimes();
		$this->bufferedStartDate = $bufferedDuration->GetBegin()->ToIso();
		$this->bufferedEndDate = $bufferedDuration->GetEnd()->ToIso();
		$this->resourceStatusId = $reservationItemView->ResourceStatusId;

		if ($reservationItemView->StartReminder != null)
		{
			$this->startReminder = $reservationItemView->StartReminder->MinutesPrior();
		}

		if ($reservationItemView->EndReminder != null)
		{
			$this->endReminder = $reservationItemView->EndReminder->MinutesPrior();
		}

		$this->color = $reservationItemView->GetColor();
		$this->textColor = $reservationItemView->GetTextColor();
		$this->checkInDate = $reservationItemView->CheckinDate->ToIso();
		$this->checkOutDate = $reservationItemView->CheckoutDate->ToIso();
		$this->originalEndDate = $reservationItemView->OriginalEndDate->ToIso();
		$this->isCheckInEnabled = $reservationItemView->IsCheckInEnabled;
		$this->autoReleaseMinutes = $reservationItemView->AutoReleaseMinutes;
        $this->creditsConsumed = $reservationItemView->CreditsConsumed;

		$this->AddService($server, WebServices::GetResource,
						  [WebServiceParams::ResourceId => $reservationItemView->ResourceId]);
		$this->AddService($server, WebServices::GetReservation,
						  [WebServiceParams::ReferenceNumber => $reservationItemView->ReferenceNumber]);
		$this->AddService($server, WebServices::GetUser,
						  [WebServiceParams::UserId => $reservationItemView->UserId]);
		$this->AddService($server, WebServices::GetSchedule,
						  [WebServiceParams::ScheduleId => $reservationItemView->ScheduleId]);

	}

	public static function Example()
	{
		return new ExampleReservationItemResponse();
	}
}

class ExampleReservationItemResponse extends ReservationItemResponse
{
	public function __construct()
	{
		$this->description = 'reservation description';
		$this->endDate = Date::Now()->ToIso();
		$this->firstName = 'first';
		$this->isRecurring = true;
		$this->lastName = 'last';
		$this->referenceNumber = 'refnum';
		$this->requiresApproval = true;
		$this->resourceId = 123;
		$this->resourceName = 'resourcename';
		$this->scheduleId = 22;
		$this->startDate = Date::Now()->ToIso();
		$this->title = 'reservation title';
		$this->userId = 11;
		$this->participants = ['participant name'];
		$this->invitees = ['invitee name'];
        $this->coOwners = ['co owner name'];
        $this->invitedGuests = ['guest@email.com'];
        $this->participatingGuests = ['guest@email.com'];
		$this->autoReleaseMinutes = 1;
		$this->bufferedStartDate = Date::Now()->ToIso();
		$this->bufferedEndDate = Date::Now()->ToIso();
		$this->bufferTime = TimeInterval::FromMinutes(1.5)->__toString();
		$this->checkInDate = Date::Now()->ToIso();
		$this->checkOutDate = Date::Now()->ToIso();
		$this->originalEndDate = Date::Now()->ToIso();
		$this->color = '#FFFFFF';
		$this->duration = DateDiff::FromTimeString('1:45')->__toString();
		$this->endReminder = 10;
		$this->isCheckInEnabled = true;
		$this->startReminder = 10;
		$this->textColor = '#000000';
        $this->creditsConsumed = 15;
        $this->customAttributes = [CustomAttributeResponse::Example()];
	}
}