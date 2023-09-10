<?php
/**
Copyright 2019-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'lib/Email/Messages/ReservationEmailMessage.php');

class ReservationSeriesEndingEmail extends EmailMessage
{
    /**
     * @var ExistingReservationSeries
     */
    private $reservationSeries;

    /**
     * @var string
     */
    private $timezone;

    /**
     * @var Reservation
     */
    private $currentInstance;
    private User $owner;

    public function __construct(ExistingReservationSeries $reservationSeries, User $owner)
	{
		parent::__construct($owner->Language());

		$this->reservationSeries = $reservationSeries;
		$this->timezone = $owner->Timezone();
        $this->owner = $owner;
		$this->currentInstance = $this->reservationSeries->CurrentInstance();
    }

	public function To()
	{
		return [new EmailAddress($this->owner->EmailAddress(), $this->owner->FullName())];
	}

	public function Subject()
	{
        $format = Resources::GetInstance()->GetDateFormat("general_date", $this->owner->DateFormat(), $this->owner->TimeFormat());

		return $this->Translate('ReservationSeriesEndingSubject', [
		    $this->reservationSeries->Resource()->GetName(),
            $this->currentInstance->StartDate()->ToTimezone($this->timezone)->Format($format)]);
	}

    public function Body()
    {
        $format = Resources::GetInstance()->GetDateFormat("reservation_email", $this->owner->DateFormat(), $this->owner->TimeFormat());
        $this->Set('dateFormat', $format);

        $this->Set('ResourceName', $this->reservationSeries->Resource()->GetName());
        $this->Set('Title', $this->reservationSeries->Title());
        $this->Set('Description', $this->reservationSeries->Description());
        $this->Set('StartDate', $this->currentInstance->StartDate()->ToTimezone($this->timezone));
        $this->Set('EndDate', $this->currentInstance->EndDate()->ToTimezone($this->timezone));
        $this->Set('ReservationUrl', sprintf("%s?%s=%s", UrlPaths::RESERVATION, QueryStringKeys::REFERENCE_NUMBER, $this->currentInstance->ReferenceNumber()));

        return $this->FetchTemplate('ReservationSeriesEnding.tpl');
    }
}