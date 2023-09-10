<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Email/namespace.php');
require_once(ROOT_DIR . 'Pages/Pages.php');

class MissedCheckinAdminEmail extends EmailMessage
{
    /**
     * @var ReservationItemView
     */
    private $reservation;

    /**
     * @var UserDto
     */
    private $user;

    public function __construct(ReservationItemView $reservation, UserDto $user)
    {
        $this->reservation = $reservation;
        $this->user = $user;
        parent::__construct($user->Language());
    }

    /**
     * @return array|EmailAddress[]|EmailAddress
     */
    public function To()
    {
        return new EmailAddress($this->user->EmailAddress(), new FullName($this->user->FirstName(), $this->user->LastName()));
    }

    public function Subject()
    {
        return $this->Translate('MissedCheckinEmailSubject', [$this->reservation->ResourceName]);
    }

    public function Body()
    {
        $format = Resources::GetInstance()->GetDateFormat("reservation_email", $this->user->DateFormat(), $this->user->TimeFormat());
        $this->Set('dateFormat', $format);

        $this->Set('OwnerName', new FullName($this->reservation->OwnerFirstName, $this->reservation->OwnerLastName));
        $this->Set('StartDate', $this->reservation->StartDate->ToTimezone($this->reservation->OwnerTimezone));
        $this->Set('EndDate', $this->reservation->EndDate->ToTimezone($this->reservation->OwnerTimezone));
        $this->Set('ResourceName', $this->reservation->ResourceName);
        $this->Set('Title', $this->reservation->Title);
        $this->Set('Description', $this->reservation->Description);
        $this->Set('IsAutoRelease', $this->reservation->AutoReleaseMinutes != null);
        $this->Set('AutoReleaseTime', $this->reservation->StartDate->AddMinutes($this->reservation->AutoReleaseMinutes));
        $this->Set('ReservationUrl', sprintf("%s?%s=%s", Pages::RESERVATION, QueryStringKeys::REFERENCE_NUMBER,
            $this->reservation->ReferenceNumber));

        return $this->FetchTemplate('MissedCheckinAdminEmail.tpl');
    }
}