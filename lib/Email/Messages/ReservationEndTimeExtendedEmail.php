<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Email/namespace.php');

class ReservationEndTimeExtendedEmail extends EmailMessage
{
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $fname;
    /**
     * @var string
     */
    private $lname;
    /**
     * @var string
     */
    private $language;
    /**
     * @var string
     */
    private $timezone;
    /**
     * @var string
     */
    private $referenceNumber;
    /**
     * @var string
     */
    private $resourceName;
    /**
     * @var Date
     */
    private $originalStartDate;
    /**
     * @var Date
     */
    private $originalEndDate;
    /**
     * @var Date
     */
    private $newEndDate;
    /**
     * @var int|null
     */
    private $dateFormat;
    /**
     * @var int|null
     */
    private $timeFormat;

    /**
     * @param $email string
     * @param $fname string
     * @param $lname string
     * @param $language string
     * @param $timezone string
     * @param $referenceNumber string
     * @param $resourceName string
     * @param $originalStartDate Date
     * @param $originalEndDate Date
     * @param $newEndDate Date
     * @param $dateFormat int|null
     * @param $timeFormat int|null
     */
    public function __construct($email, $fname, $lname, $language, $timezone, $referenceNumber, $resourceName, $originalStartDate, $originalEndDate, $newEndDate, $dateFormat, $timeFormat)
    {
        parent::__construct($language);
        $this->email = $email;
        $this->fname = $fname;
        $this->lname = $lname;
        $this->language = $language;
        $this->timezone = $timezone;
        $this->referenceNumber = $referenceNumber;
        $this->resourceName = $resourceName;
        $this->originalStartDate = $originalStartDate;
        $this->originalEndDate = $originalEndDate;
        $this->newEndDate = $newEndDate;
        $this->dateFormat = $dateFormat;
        $this->timeFormat = $timeFormat;
    }

    public function To()
    {
        return new EmailAddress($this->email, FullName::AsString($this->fname, $this->lname));
    }

    public function Subject()
    {
        return $this->Translate('ReservationExtendedEmailSubject', [$this->resourceName]);
    }

    public function Body()
    {
        $format = Resources::GetInstance()->GetDateFormat("reservation_email", $this->dateFormat, $this->timeFormat);
        $this->Set('dateFormat', $format);

        $this->Set('ResourceName', $this->resourceName);
        $this->Set('OriginalStart', $this->originalStartDate->ToTimezone($this->timezone));
        $this->Set('OriginalEnd', $this->originalEndDate->ToTimezone($this->timezone));
        $this->Set('NewEnd', $this->newEndDate->ToTimezone($this->timezone));
        $this->Set('ReservationUrl', sprintf("%s?%s=%s", UrlPaths::RESERVATION, QueryStringKeys::REFERENCE_NUMBER, $this->referenceNumber));
        return $this->FetchTemplate('ReservationEndTimeExtendedEmail.tpl');
    }
}