<?php

/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

class iCalendarReservationView
{
    public $DateCreated;
    public $DateEnd;
    public $DateStart;
    public $Description;
    public $Organizer;
    public $RecurRule;
    public $ReferenceNumber;
    public $Summary;
    public $ReservationUrl;
    public $Location;
    public $StartReminder;
    public $EndReminder;
    public $LastModified;
    public $IsPending;

    /**
     * @var ReservationItemView
     */
    public $ReservationItemView;
    /**
     * @var string[]
     */
    public $Attachments = [];
    /**
     * @var string
     */
    public $OrganizerEmail;

    /**
     * @param ReservationItemView $res
     * @param UserSession $currentUser
     * @param IPrivacyFilter $privacyFilter
     * @param string|null $summaryFormat
     * @param string|null $descriptionFormat
     */
    public function __construct($res, UserSession $currentUser, IPrivacyFilter $privacyFilter, $summaryFormat = null, $descriptionFormat = null)
    {
        if ($summaryFormat == null) {
            $summaryFormat = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION_LABELS, ConfigKeys::RESERVATION_LABELS_ICS_SUMMARY);
        }
        if ($descriptionFormat == null) {
            $descriptionFormat = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION_LABELS, ConfigKeys::RESERVATION_LABELS_ICS_DESCRIPTION);
        }
        if (empty($summaryFormat)) {
            $summaryFormat = '{title}';
        }
        if (empty($descriptionFormat)) {
            $descriptionFormat = '{description}';
        }
        $factory = new SlotLabelFactory($currentUser);
        $this->ReservationItemView = $res;
        $canViewUser = $privacyFilter->CanViewUser($currentUser, $res, $res->OwnerId);
        $canViewDetails = $privacyFilter->CanViewDetails($currentUser, $res, $res->OwnerId);

        $privateNotice = 'Private';

        $this->DateCreated = $res->DateCreated;
        $this->DateEnd = $res->EndDate;
        $this->DateStart = $res->StartDate;
        $this->Description = $canViewDetails ? $factory->Format($res, $descriptionFormat) : $privateNotice;
        $this->Summary = $canViewDetails ? $factory->Format($res, $summaryFormat) : $privateNotice;
        $fullName = new FullName($res->OwnerFirstName, $res->OwnerLastName);
        $this->Organizer = $canViewUser ? $fullName->__toString() : $privateNotice;
        $this->OrganizerEmail = $canViewUser ? $res->OwnerEmailAddress : $privateNotice;
        $this->RecurRule = $this->CreateRecurRule($res);
        $this->ReferenceNumber = $res->ReferenceNumber;
        $this->ReservationUrl = sprintf("%s/%s?%s=%s", Configuration::Instance()->GetScriptUrl(), UrlPaths::RESERVATION, QueryStringKeys::REFERENCE_NUMBER,
            $res->ReferenceNumber);
        $this->Location = implode(', ', $res->GetResourceNames());

        $this->StartReminder = $res->StartReminder;
        $this->EndReminder = $res->EndReminder;
        $this->LastModified = empty($res->ModifiedDate) || $res->ModifiedDate->ToString() == '' ? $res->DateCreated : $res->ModifiedDate;
        $this->IsPending = $res->RequiresApproval;

        if ($res->OwnerId == $currentUser->UserId) {
            $this->OrganizerEmail = str_replace('@', '-noreply@', $res->OwnerEmailAddress . '');
        }

        foreach ($res->AttachmentIds as $attachmentId) {
            $this->Attachments[] = Configuration::Instance()->GetScriptUrl() . "/attachments/reservation-file.php?afid={$attachmentId}&rn={$res->ReferenceNumber}";
        }
    }

    /**
     * @param ReservationItemView $res
     * @return null|string
     */
    private function CreateRecurRule($res)
    {
        if (is_a($res, 'ReservationItemView')) {
            // don't populate the recurrence rule when a list of reservation is being exported
            return null;
        }
        ### !!!  THIS DOES NOT WORK BECAUSE EXCEPTIONS TO RECURRENCE RULES ARE NOT PROPERLY HANDLED !!!
        ### see bug report http://php.brickhost.com/forums/index.php?topic=11450.0

        if (empty($res->RepeatType) || $res->RepeatType == RepeatType::None) {
            return null;
        }

        $freqMapping = [RepeatType::Daily => 'DAILY', RepeatType::Weekly => 'WEEKLY', RepeatType::Monthly => 'MONTHLY', RepeatType::Yearly => 'YEARLY'];
        $freq = $freqMapping[$res->RepeatType];
        $interval = $res->RepeatInterval;
        $format = Resources::GetInstance()->GetDateFormat('ical');
        $end = $res->RepeatTerminationDate->SetTime($res->EndDate->GetTime())->Format($format);
        $rrule = sprintf('FREQ=%s;INTERVAL=%s;UNTIL=%s', $freq, $interval, $end);

        if ($res->RepeatType == RepeatType::Monthly) {
            if ($res->RepeatMonthlyType == RepeatMonthlyType::DayOfMonth) {
                $rrule .= ';BYMONTHDAY=' . $res->StartDate->Day();
            }
        }

        if (!empty($res->RepeatWeekdays)) {
            $dayMapping = array('SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA');
            $days = '';
            foreach ($res->RepeatWeekdays as $weekDay) {
                $days .= ($dayMapping[$weekDay] . ',');
            }
            $days = substr($days, 0, -1);
            $rrule .= (';BYDAY=' . $days);
        }

        return $rrule;
    }
}
