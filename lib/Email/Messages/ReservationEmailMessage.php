<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Email/namespace.php');
require_once(ROOT_DIR . 'Pages/Pages.php');
require_once(ROOT_DIR . 'Pages/Export/CalendarExportDisplay.php');
require_once(ROOT_DIR . 'lib/Application/Schedule/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/namespace.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Graphics/namespace.php');

abstract class ReservationEmailMessage extends EmailMessage
{
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
    protected $primaryResource;

    /**
     * @var string
     */
    protected $timezone;

    /**
     * @var IAttributeRepository
     */
    protected $attributeRepository;

    /**
     * @var IUserRepository
     */
    protected $userRepository;

    protected $showQrCode = true;
    protected $dateFormat;
    protected $timeFormat;

    public function __construct(User                 $reservationOwner,
                                ReservationSeries    $reservationSeries,
                                                     $language,
                                IAttributeRepository $attributeRepository,
                                IUserRepository      $userRepository,
                                                     $dateFormat,
                                                     $timeFormat)
    {
        if (empty($language)) {
            $language = $reservationOwner->Language();
        }
        parent::__construct($language);

        $this->reservationOwner = $reservationOwner;
        $this->reservationSeries = $reservationSeries;
        $this->timezone = $reservationOwner->Timezone();
        $this->attributeRepository = $attributeRepository;
        $this->primaryResource = $reservationSeries->Resource();
        $this->userRepository = $userRepository;
        $this->dateFormat = $dateFormat;
        $this->timeFormat = $timeFormat;
    }

    /**
     * @return string
     */
    protected abstract function GetTemplateName();

    /**
     * @return ReservationAction|string
     */
    protected function GetAction()
    {
        return ReservationAction::Create;
    }

    public function To()
    {
        $address = $this->reservationOwner->EmailAddress();
        $name = $this->reservationOwner->FullName();

        return [new EmailAddress($address, $name)];
    }

    public function Body()
    {
        $this->PopulateTemplate();
        return $this->FetchTemplate($this->GetTemplateName());
    }

    public function From()
    {
        $bookedBy = $this->reservationSeries->BookedBy();
        if ($bookedBy != null) {
            $name = new FullName($bookedBy->FirstName, $bookedBy->LastName);
            return new EmailAddress($bookedBy->Email, $name->__toString());
        }
        return new EmailAddress($this->reservationOwner->EmailAddress(), $this->reservationOwner->FullName());
    }

    protected abstract function IncludePrivateAttributes();

    protected function PopulateTemplate()
    {
        $format = Resources::GetInstance()->GetDateFormat("reservation_email", $this->dateFormat, $this->timeFormat);
        $this->Set('dateFormat', $format);

        $this->Set('UpdatedBy', null);
        $this->Set('ApprovedBy', null);

        $currentInstance = $this->reservationSeries->CurrentInstance();
        $this->Set('UserName', $this->reservationOwner->FullName());
        $this->Set('StartDate', $currentInstance->StartDate()->ToTimezone($this->timezone));
        $this->Set('EndDate', $currentInstance->EndDate()->ToTimezone($this->timezone));
        $this->Set('ResourceName', $this->reservationSeries->Resource()->GetName());
        $img = $this->reservationSeries->Resource()->GetImage();
        $this->Set('ResourceImage', null);
        if (!empty($img)) {
            $this->Set('ResourceImage', $this->GetFullImagePath($img));
        }
        $this->Set('Title', $this->reservationSeries->Title() . '');
        $this->Set('Description', $this->reservationSeries->Description() . '');

        $repeatDates = [];
        $repeatRanges = [];
        $instances = [];
        if ($this->reservationSeries->IsRecurring()) {
            foreach ($this->reservationSeries->Instances() as $repeated) {
                $repeatDates[] = $repeated->StartDate()->ToTimezone($this->timezone);
                $repeatRanges[] = $repeated->Duration()->ToTimezone($this->timezone);
                $instances[] = $repeated;
            }
        }
        $this->Set('RepeatDates', $repeatDates);
        $this->Set('RepeatRanges', $repeatRanges);
        $this->Set('RecurringReservations', $instances);
        $this->Set('RequiresApproval', $this->reservationSeries->RequiresApproval());

        $this->Set('ReservationUrl', sprintf("%s?%s=%s", UrlPaths::RESERVATION, QueryStringKeys::REFERENCE_NUMBER, $currentInstance->ReferenceNumber()));

        $icalUrl = sprintf("export/%s?%s=%s", Pages::CALENDAR_EXPORT, QueryStringKeys::REFERENCE_NUMBER, $currentInstance->ReferenceNumber());
        $this->Set('ICalUrl', $icalUrl);

        $googleDateFormat = Resources::GetInstance()->GetDateFormat('google');
        $googleCalendarUrl = sprintf("https://www.google.com/calendar/event?action=TEMPLATE&text=%s&dates=%s/%s&ctz=%s&details=%s&location=%s&trp=false&sprop=&sprop=name:",
            urlencode($this->reservationSeries->Title()),
            $currentInstance->StartDate()->ToUtc()->Format($googleDateFormat),
            $currentInstance->EndDate()->ToUtc()->Format($googleDateFormat),
            $currentInstance->StartDate()->Timezone(),
            urlencode($this->reservationSeries->Description() . ''),
            urlencode($this->reservationSeries->Resource()->GetName()));
        $this->Set('GoogleCalendarUrl', $googleCalendarUrl);

        $resourceNames = [];
        foreach ($this->reservationSeries->AllResources() as $resource) {
            $resourceNames[] = $resource->GetName();
        }
        $this->Set('ResourceNames', $resourceNames);
        $this->Set('Accessories', $this->reservationSeries->Accessories());

        $attributes = $this->attributeRepository->GetByCategory(CustomAttributeCategory::RESERVATION);
        $attributeValues = [];
        foreach ($attributes as $attribute) {
            $attributeValue = $this->reservationSeries->GetAttributeValue($attribute->Id());
            if (!empty($attributeValue) && !$attribute->AdminOnly() && (!$attribute->IsPrivate() || $this->IncludePrivateAttributes())) {
                $attributeValues[] = new \Booked\Attribute($attribute, $attributeValue);
            }
        }

        $this->Set('Attributes', $attributeValues);
        $this->Set('CreatedBy', $this->GetBookedBy());

        $this->Set('CheckInEnabled', false);
        $minimumAutoRelease = null;
        foreach ($this->reservationSeries->AllResources() as $resource) {
            if ($resource->IsCheckInEnabled()) {
                $this->Set('CheckInEnabled', true);
            }

            if ($resource->IsAutoReleased()) {
                if ($minimumAutoRelease == null || $resource->GetAutoReleaseMinutes() < $minimumAutoRelease) {
                    $minimumAutoRelease = $resource->GetAutoReleaseMinutes();
                }
            }
        }

        $this->PopulateIcsAttachment($currentInstance, $attributeValues);

        $this->Set('AutoReleaseMinutes', $minimumAutoRelease);
        $this->Set('ReferenceNumber', $currentInstance->ReferenceNumber());

        $participants = [];
        foreach ($currentInstance->Participants() as $id) {
            $participants[] = $this->GetUser($id);
        }
        $this->Set('Participants', $participants);
        $this->Set('ParticipatingGuests', $currentInstance->ParticipatingGuests());

        $invitees = [];
        foreach ($currentInstance->Invitees() as $id) {
            $invitees[] = $this->GetUser($id);
        }
        $this->Set('Invitees', $invitees);
        $this->Set('InvitedGuests', $currentInstance->InvitedGuests());

        $coowners = [];
        foreach ($currentInstance->CoOwners() as $id) {
            $coowners[] = $this->GetUser($id);
        }
        $this->Set('CoOwners', $coowners);

        $this->Set('CreditsCurrent', $currentInstance->GetCreditsRequired());
        $this->Set('CreditsTotal', $this->reservationSeries->GetCreditsRequired());

        if ($this->showQrCode) {
            $qrPath = sprintf('%s/%s?%s=%s', Configuration::Instance()->GetScriptUrl(), Pages::RESERVATION, QueryStringKeys::REFERENCE_NUMBER, $currentInstance->ReferenceNumber());

            ob_start();
            try {
                @QRCode::png($qrPath, null);
                $imageString = base64_encode(ob_get_contents());
            } catch (Exception $ex) {
            }
            ob_end_clean();

            if (isset($imageString)) {
                $this->AddEmbeddedImage($imageString, 'qrcode');
            }
        }

        $attachments = [];
        foreach ($this->reservationSeries->AllAttachments() as $attachment) {
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
        $this->Set('Updated', false);
        $this->Set('Deleted', false);

        $this->Set('MeetingLink', null);
        if ($this->reservationSeries->MeetingLink() != null) {
            $this->Set('MeetingLink', $this->reservationSeries->MeetingLink()->Url());
        }
    }

    /**
     * @param $id int
     * @return UserDto
     */
    private function GetUser($id)
    {
        return $this->userRepository->GetById($id);
    }

    private function GetFullImagePath($img)
    {
        return Configuration::Instance()->GetKey(ConfigKeys::IMAGE_UPLOAD_URL) . '/' . $img;
    }

    /**
     * @param Reservation $currentInstance
     * @param \Booked\Attribute[] $attributeValues
     */
    protected function PopulateIcsAttachment($currentInstance, $attributeValues)
    {
        $rv = new ReservationItemView($currentInstance->ReferenceNumber(),
            $currentInstance->StartDate()->ToUTC(),
            $currentInstance->EndDate()->ToUTC(),
            $this->reservationSeries->Resource()->GetName(),
            $this->reservationSeries->Resource()->GetResourceId(),
            $currentInstance->ReservationId(),
            null,
            $this->reservationSeries->Title(),
            $this->reservationSeries->Description(),
            $this->reservationSeries->ScheduleId(),
            $this->reservationOwner->FirstName(),
            $this->reservationOwner->LastName(),
            $this->reservationOwner->Id(),
            $this->reservationOwner->GetAttribute(UserAttribute::Phone),
            $this->reservationOwner->GetAttribute(UserAttribute::Organization),
            $this->reservationOwner->GetAttribute(UserAttribute::Position)
        );

        $ca = new CustomAttributes();
        foreach ($attributeValues as $attribute) {
            $ca->Add($attribute->Id(), $attribute->Value());
        }
        $rv->Attributes = $ca;
        $rv->UserPreferences = $this->reservationOwner->GetPreferences();
        $rv->OwnerEmailAddress = $this->reservationOwner->EmailAddress();

        $icsView = new iCalendarReservationView($rv, $this->reservationSeries->BookedBy(), new NullPrivacyFilter());

        $display = new CalendarExportDisplay();
        $icsContents = $display->Render(array($icsView));
        $this->AddStringAttachment($icsContents, 'reservation.ics');
    }

    protected function GetBookedBy()
    {
        $bookedBy = $this->reservationSeries->BookedBy();
        if ($bookedBy != null && ($bookedBy->UserId != $this->reservationOwner->Id())) {
            return FullName::AsString($bookedBy->FirstName, $bookedBy->LastName);
        }
        return null;
    }
}