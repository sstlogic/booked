<?php
/**
 * Copyright 2013-2023 Twinkle Toes Software, LLC
 */

class ReminderNotice
{
    private $seriesId;
    private $reservationId;
    private $referenceNumber;
    private $startDate;
    private $endDate;
    private $title;
    private $description;
    private $resourceNames;
    private $emailAddress;
    private $firstName;
    private $lastName;
    private $timezone;
    private $reminder_minutes;
    private $language;
    private $ownerId;
    private $dateFormat;
    private $timeFormat;

    public function Description()
    {
        return $this->description;
    }

    public function EmailAddress()
    {
        return $this->emailAddress;
    }

    public function EndDate()
    {
        return $this->endDate;
    }

    public function FirstName()
    {
        return $this->firstName;
    }

    public function LastName()
    {
        return $this->lastName;
    }

    public function ReferenceNumber()
    {
        return $this->referenceNumber;
    }

    public function ReminderMinutes()
    {
        return $this->reminder_minutes;
    }

    public function ReservationId()
    {
        return $this->reservationId;
    }

    public function ResourceNames()
    {
        return $this->resourceNames;
    }

    public function ResourceName()
    {
        return trim(explode(',', $this->ResourceNames())[0]);
    }

    public function SeriesId()
    {
        return $this->seriesId;
    }

    public function StartDate()
    {
        return $this->startDate;
    }

    public function Timezone()
    {
        return $this->timezone;
    }

    public function Title()
    {
        return $this->title;
    }

    public function Language()
    {
        return $this->language;
    }

    public function OwnerId()
    {
        return $this->ownerId;
    }

    public function DateFormat()
    {
        return $this->dateFormat;
    }

    public function TimeFormat()
    {
        return $this->timeFormat;
    }

    /**
     * @param int $seriesId
     * @param int $reservationId
     * @param string $referenceNumber
     * @param Date $startDate
     * @param Date $endDate
     * @param string $title
     * @param string $description
     * @param string $resourceNames
     * @param string $emailAddress
     * @param string $firstName
     * @param string $lastName
     * @param string $timezone
     * @param int $reminder_minutes
     * @param string $language
     * @param int $ownerId
     * @param int|null $dateFormat
     * @param int|null $timeFormat
     */
    public function __construct($seriesId, $reservationId, $referenceNumber, Date $startDate, Date $endDate, $title,
                                $description, $resourceNames, $emailAddress, $firstName, $lastName, $timezone,
                                $reminder_minutes, $language, $ownerId, $dateFormat, $timeFormat)
    {
        $this->seriesId = $seriesId;
        $this->reservationId = $reservationId;
        $this->referenceNumber = $referenceNumber;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->title = $title;
        $this->description = $description;
        $this->resourceNames = str_replace('!sep!', ', ', $resourceNames);
        $this->emailAddress = $emailAddress;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->timezone = $timezone;
        $this->reminder_minutes = $reminder_minutes;
        $this->language = $language;
        $this->ownerId = $ownerId;
        $this->dateFormat = $dateFormat;
        $this->timeFormat = $timeFormat;
    }

    /**
     * @param array $row
     * @return ReminderNotice
     */
    public static function FromRow($row)
    {
        $seriesId = $row[ColumnNames::SERIES_ID];
        $reservationId = $row[ColumnNames::RESERVATION_INSTANCE_ID];
        $referenceNumber = $row[ColumnNames::REFERENCE_NUMBER];
        $startDate = Date::FromDatabase($row[ColumnNames::RESERVATION_START]);
        $endDate = Date::FromDatabase($row[ColumnNames::RESERVATION_END]);
        $title = $row[ColumnNames::RESERVATION_TITLE];
        $description = $row[ColumnNames::RESERVATION_DESCRIPTION];
        $resourceNames = $row[ColumnNames::RESOURCE_NAMES];
        $emailAddress = $row[ColumnNames::EMAIL];
        $firstName = $row[ColumnNames::FIRST_NAME];
        $lastName = $row[ColumnNames::LAST_NAME];
        $timezone = $row[ColumnNames::TIMEZONE_NAME];
        $reminder_minutes = $row[ColumnNames::REMINDER_MINUTES_PRIOR];
        $language = $row[ColumnNames::LANGUAGE_CODE];
        $ownerId = $row[ColumnNames::OWNER_USER_ID];
        $dateFormat = $row[ColumnNames::DATE_FORMAT];
        $timeFormat = $row[ColumnNames::TIME_FORMAT];

        return new ReminderNotice($seriesId, $reservationId, $referenceNumber,
            $startDate, $endDate, $title, $description,
            $resourceNames, $emailAddress, $firstName,
            $lastName, $timezone, $reminder_minutes, $language, $ownerId, $dateFormat, $timeFormat);
    }
}