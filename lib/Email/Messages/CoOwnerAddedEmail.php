<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'lib/Email/Messages/ReservationEmailMessage.php');
require_once(ROOT_DIR . 'lib/Email/Messages/ReservationCreatedEmail.php');
require_once(ROOT_DIR . 'lib/Email/Messages/ReservationUpdatedEmail.php');
require_once(ROOT_DIR . 'lib/Email/Messages/ReservationDeletedEmail.php');

class CoOwnerAddedEmail extends ReservationCreatedEmail
{
    /**
     * @var User
     */
    private $coowner;

    public function __construct(User $reservationOwner, User $coowner, ReservationSeries $reservationSeries, IAttributeRepository $attributeRepository, IUserRepository $userRepository)
	{
		parent::__construct($reservationOwner, $reservationSeries, $coowner->Language(), $attributeRepository, $userRepository, $coowner->DateFormat(), $coowner->TimeFormat());

		$this->reservationOwner = $reservationOwner;
		$this->reservationSeries = $reservationSeries;
		$this->timezone = $coowner->Timezone();
		$this->coowner = $coowner;
	}

	public function Subject()
	{
		return $this->Translate('CoOwnerAddedSubjectWithResource', [$this->reservationOwner->FullName(), $this->primaryResource->GetName()]);
	}

    public function To()
    {
        $address = $this->coowner->EmailAddress();
        $name = $this->coowner->FullName();

        return new EmailAddress($address, $name);
    }

    protected function IncludePrivateAttributes()
    {
       return false;
    }
}

class CoOwnerUpdatedEmail extends ReservationUpdatedEmail
{
    /**
     * @var User
     */
    private $coowner;

    public function __construct(User $reservationOwner, User $coowner, ReservationSeries $reservationSeries, IAttributeRepository $attributeRepository, IUserRepository $userRepository)
    {
        parent::__construct($reservationOwner, $reservationSeries, $coowner->Language(), $attributeRepository, $userRepository, $coowner->DateFormat(), $coowner->TimeFormat());

        $this->reservationOwner = $reservationOwner;
        $this->reservationSeries = $reservationSeries;
        $this->timezone = $coowner->Timezone();
        $this->coowner = $coowner;
    }

	public function Subject()
	{
        $bookedBy = $this->reservationSeries->BookedBy();
        if ($bookedBy != null && $bookedBy->UserId == $this->coowner->Id()) {
            return $this->Translate('ReservationUpdatedSubjectWithResource', [$this->primaryResource->GetName()]);
        }
		return $this->Translate('CoOwnerUpdatedSubjectWithResource', [$this->reservationOwner->FullName(), $this->primaryResource->GetName()]);
	}

    public function To()
    {
        $address = $this->coowner->EmailAddress();
        $name = $this->coowner->FullName();

        return new EmailAddress($address, $name);
    }

    public function From()
    {
        return new EmailAddress($this->reservationOwner->EmailAddress(), $this->reservationOwner->FullName());
    }

    protected function IncludePrivateAttributes()
    {
        return false;
    }
}

class CoOwnerDeletedEmail extends ReservationDeletedEmail
{
    /**
     * @var User
     */
    private $coowner;

    public function __construct(User $reservationOwner, User $coowner, ReservationSeries $reservationSeries, IAttributeRepository $attributeRepository, IUserRepository $userRepository)
    {
        parent::__construct($reservationOwner, $reservationSeries, $coowner->Language(), $attributeRepository, $userRepository, $coowner->DateFormat(), $coowner->TimeFormat());

        $this->reservationOwner = $reservationOwner;
        $this->reservationSeries = $reservationSeries;
        $this->timezone = $coowner->Timezone();
        $this->coowner = $coowner;
    }

	public function Subject()
	{
        $bookedBy = $this->reservationSeries->BookedBy();
        if ($bookedBy != null && $bookedBy->UserId == $this->coowner->Id()) {
            return $this->Translate('ReservationDeletedSubjectWithResource', [$this->primaryResource->GetName()]);
        }
		return $this->Translate('CoOwnerDeletedSubjectWithResource', [$this->reservationOwner->FullName(), $this->primaryResource->GetName()]);
	}

    public function To()
    {
        $address = $this->coowner->EmailAddress();
        $name = $this->coowner->FullName();

        return new EmailAddress($address, $name);
    }

    public function From()
    {
        return new EmailAddress($this->reservationOwner->EmailAddress(), $this->reservationOwner->FullName());
    }

    protected function IncludePrivateAttributes()
    {
        return false;
    }
}