<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Email/Messages/ReservationEmailMessage.php');

class GuestAddedEmail extends ReservationEmailMessage
{
    /**
     * @var string
     */
    protected $guestEmail;

    public function __construct(User $reservationOwner, $guestEmail, ReservationSeries $reservationSeries, IAttributeRepository $attributeRepository, IUserRepository $userRepository)
	{
        parent::__construct($reservationOwner, $reservationSeries, $reservationOwner->Language(), $attributeRepository, $userRepository, $reservationOwner->DateFormat(), $reservationOwner->TimeFormat());

        $this->reservationOwner = $reservationOwner;
        $this->reservationSeries = $reservationSeries;
        $this->timezone = $reservationOwner->Timezone();
        $this->guestEmail = $guestEmail;
    }

    public function To()
    {
        return array(new EmailAddress($this->guestEmail));
    }

    public function Subject()
    {
        return $this->Translate('ParticipantAddedSubjectWithResource', array($this->reservationOwner->FullName(), $this->primaryResource->GetName()));
    }

    public function From()
    {
        return new EmailAddress($this->reservationOwner->EmailAddress(), $this->reservationOwner->FullName());
    }

    public function GetTemplateName()
    {
        return 'ReservationInvitation.tpl';
    }

    protected function PopulateTemplate()
    {
        $currentInstance = $this->reservationSeries->CurrentInstance();
        parent::PopulateTemplate();

        $this->Set('AcceptUrl', sprintf("%s?%s=%s&%s=%s&%s=%s", Pages::GUEST_INVITATION_RESPONSES, QueryStringKeys::REFERENCE_NUMBER, $currentInstance->ReferenceNumber(), QueryStringKeys::EMAIL, $this->guestEmail, QueryStringKeys::INVITATION_ACTION, InvitationAction::Accept));
        $this->Set('DeclineUrl', sprintf("%s?%s=%s&%s=%s&%s=%s", Pages::GUEST_INVITATION_RESPONSES, QueryStringKeys::REFERENCE_NUMBER, $currentInstance->ReferenceNumber(), QueryStringKeys::EMAIL, $this->guestEmail, QueryStringKeys::INVITATION_ACTION, InvitationAction::Decline));
    }

    protected function IncludePrivateAttributes()
    {
        return false;
    }
}

class GuestUpdatedEmail extends GuestAddedEmail
{
	public function Subject()
	{
		return $this->Translate('ParticipantUpdatedSubjectWithResource', array($this->reservationOwner->FullName(), $this->primaryResource->GetName()));
	}

	public function PopulateTemplate()
	{
		parent::PopulateTemplate();
		$this->Set("Deleted", false);
		$this->Set("Updated", true);
	}
}