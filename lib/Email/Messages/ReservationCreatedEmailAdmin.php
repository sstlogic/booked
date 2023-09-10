<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Email/namespace.php');

class ReservationCreatedEmailAdmin extends EmailMessage
{
	/**
	 * @var UserDto
	 */
	protected $adminDto;

	/**
	 * @var User
	 */
	protected $reservationOwner;

	/**
	 * @var ReservationSeries
	 */
	protected $reservationSeries;

	/**
	 * @var IResource
	 */
	protected $resource;

	/**
	 * @var IAttributeRepository
	 */
	protected $attributeRepository;

	/**
	 * @var string
	 */
	protected $timezone;
	/**
	 * @var IUserRepository
	 */
	private $userRepository;

	/**
	 * @param UserDto $adminDto
	 * @param User $reservationOwner
	 * @param ReservationSeries $reservationSeries
	 * @param IResource $primaryResource
	 * @param IAttributeRepository $attributeRepository
	 * @param IUserRepository $userRepository
	 */
	public function __construct(UserDto $adminDto,
                                User $reservationOwner,
                                ReservationSeries $reservationSeries,
                                IResource $primaryResource,
								IAttributeRepository $attributeRepository,
                                IUserRepository $userRepository)
	{
		parent::__construct($adminDto->Language());

		$this->adminDto = $adminDto;
		$this->reservationOwner = $reservationOwner;
		$this->reservationSeries = $reservationSeries;
		$this->resource = $primaryResource;
		$this->attributeRepository = $attributeRepository;
		$this->timezone = $adminDto->Timezone();
		$this->userRepository = $userRepository;
	}

	/**
	 * @see IEmailMessage::To()
	 */
	public function To()
	{
		$address = $this->adminDto->EmailAddress();
		$name = $this->adminDto->FullName();

		return array(new EmailAddress($address, $name));
	}

	public function From()
	{
		return new EmailAddress($this->reservationOwner->EmailAddress(), $this->reservationOwner->FullName());
	}

	/**
	 * @see IEmailMessage::Subject()
	 */
	public function Subject()
	{
		return $this->Translate('ReservationCreatedAdminSubjectWithResource', [$this->resource->GetName()]);
	}

	/**
	 * @see IEmailMessage::Body()
	 */
	public function Body()
	{
		$this->PopulateTemplate();
		return $this->FetchTemplate($this->GetTemplateName());
	}

	protected function GetTemplateName()
	{
		return 'ReservationCreatedAdmin.tpl';
	}

	protected function PopulateTemplate()
	{
        $format = Resources::GetInstance()->GetDateFormat("reservation_email", $this->adminDto->DateFormat(), $this->adminDto->TimeFormat());
        $this->Set('dateFormat', $format);

		$this->Set('UserName', $this->reservationOwner->FullName());

		$currentInstance = $this->reservationSeries->CurrentInstance();

		$this->Set('StartDate', $currentInstance->StartDate()->ToTimezone($this->timezone));
		$this->Set('EndDate', $currentInstance->EndDate()->ToTimezone($this->timezone));
		$this->Set('ResourceName', $this->resource->GetName());
		$this->Set('Title', $this->reservationSeries->Title());
		$this->Set('Description', $this->reservationSeries->Description());

		$repeatDates = [];
		$repeatRanges = [];
        $instances = [];
		if ($this->reservationSeries->IsRecurring())
		{
			foreach ($this->reservationSeries->Instances() as $repeated)
			{
				$repeatDates[] = $repeated->StartDate()->ToTimezone($this->timezone);
				$repeatRanges[] = $repeated->Duration()->ToTimezone($this->timezone);
                $instances[] = $repeated;
			}
		}
		$this->Set('RepeatDates', $repeatDates);
		$this->Set('RepeatRanges', $repeatRanges);
        $this->Set('RepeatDates', $repeatDates);
		$this->Set('RequiresApproval', $this->reservationSeries->RequiresApproval());
        $this->Set('RecurringReservations', $instances);
		$this->Set('ReservationUrl', UrlPaths::RESERVATION . "?" . QueryStringKeys::REFERENCE_NUMBER . '=' . $currentInstance->ReferenceNumber());

		$resourceNames = [];
		foreach ($this->reservationSeries->AllResources() as $resource)
		{
			$resourceNames[] = $resource->GetName();
		}
		$this->Set('ResourceNames', $resourceNames);
		$this->Set('Accessories', $this->reservationSeries->Accessories());
        $img = $this->reservationSeries->Resource()->GetImage();
        $this->Set('ResourceImage', null);
        if (!empty($img)) {
            $this->Set('ResourceImage', $this->GetFullImagePath($img));
        }

		$attributes = $this->attributeRepository->GetByCategory(CustomAttributeCategory::RESERVATION);
		$attributeValues = [];
		foreach ($attributes as $attribute)
		{
			$attributeValues[] = new \Booked\Attribute($attribute, $this->reservationSeries->GetAttributeValue($attribute->Id()));
		}

		$this->Set('Attributes', $attributeValues);

		$bookedBy = $this->reservationSeries->BookedBy();
		if ($bookedBy != null && ($bookedBy->UserId != $this->reservationOwner->Id()))
		{
			$this->Set('CreatedBy', new FullName($bookedBy->FirstName, $bookedBy->LastName));
		}

		$this->Set('ReferenceNumber', $this->reservationSeries->CurrentInstance()->ReferenceNumber());

		$participants = [];
		foreach ($currentInstance->Participants() as $id)
		{
			$participants[] = $this->userRepository->GetById($id);
		}
		$this->Set('Participants', $participants);
		$this->Set('ParticipatingGuests', $currentInstance->ParticipatingGuests());

		$invitees = [];
		foreach ($currentInstance->Invitees() as $id)
		{
			$invitees[] = $this->userRepository->GetById($id);
		}
		$this->Set('Invitees', $invitees);
		$this->Set('InvitedGuests', $currentInstance->InvitedGuests());

        $coowners = [];
        foreach ($currentInstance->CoOwners() as $id) {
            $coowners[] = $this->userRepository->GetById($id);
        }
        $this->Set('CoOwners', $coowners);

        $this->Set('CreditsCurrent', $currentInstance->GetCreditsRequired());
		$this->Set('CreditsTotal', $this->reservationSeries->GetCreditsRequired());
        $attachments = [];
        foreach($this->reservationSeries->AllAttachments() as $attachment) {
            $attachments[] = new class($attachment, $currentInstance) {
                public $href;
                public $name;

                public function __construct(ReservationAttachment $a, Reservation $i)
                {
                    $this->href = Configuration::Instance()->GetScriptUrl() . "/attachments/reservation-file.php?afid={$a->FileId()}&rn={$i->ReferenceNumber()}";
                    $this->name = $a->FileName();
                }
            };
        }

        $this->Set('Attachments', $attachments);
        $this->Set('Deleted', false);

        $this->Set('MeetingLink', null);
        if ($this->reservationSeries->MeetingLink() != null) {
            $this->Set('MeetingLink', $this->reservationSeries->MeetingLink()->Url());
        }
	}

    private function GetFullImagePath($img)
    {
        return Configuration::Instance()->GetKey(ConfigKeys::IMAGE_UPLOAD_URL) . '/' . $img;
    }
}