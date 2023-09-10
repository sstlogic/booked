<?php
/**
 * Copyright 2018-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Email/Messages/ReservationEmailMessage.php');

class ReservationShareEmail extends ReservationEmailMessage
{
    /**
     * @var string
     */
    private $email;

    public function __construct(User $reservationOwner, $emailToShare, ReservationSeries $reservationSeries, IAttributeRepository $attributeRepository, IUserRepository $userRepository)
    {
        parent::__construct($reservationOwner, $reservationSeries, $reservationOwner->Language(), $attributeRepository, $userRepository, $reservationOwner->DateFormat(), $reservationOwner->TimeFormat());

        $this->reservationOwner = $reservationOwner;
        $this->reservationSeries = $reservationSeries;
        $this->timezone = $reservationOwner->Timezone();
        $this->email = $emailToShare;
    }

    public function To()
    {
        return array(new EmailAddress($this->email));
    }

    public function Subject()
    {
        return $this->Translate('ReservationShareSubject', [$this->reservationOwner->FullName(), $this->primaryResource->GetName()]);
    }

    public function From()
    {
        return new EmailAddress($this->reservationOwner->EmailAddress(), $this->reservationOwner->FullName());
    }

    public function GetTemplateName()
    {
        return 'ReservationCreated.tpl';
    }

    protected function IncludePrivateAttributes()
    {
        return false;
    }
}