<?php
define('ROOT_DIR', '../../');
require_once(ROOT_DIR . 'Pages/Page.php');
require_once(ROOT_DIR . 'Pages/SecurePage.php');

class ReservationReactPage extends Page
{
    public function __construct()
    {
        parent::__construct("CreateReservation", 1);
    }

    public function PageLoad()
    {
        $this->Set('ReturnUrl', $this->GetReturnUrl());
        $isAdmin = $this->server->GetUserSession()->IsAdmin;
        $this->Set('MaxUploadSize', UploadedFile::GetMaxSizeAsBytes());
        $this->Set('MaxUploadCount', UploadedFile::GetMaxUploadCount());
        $config = Configuration::Instance();
        $hideUsers = $config->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_HIDE_USER_DETAILS, new BooleanConverter());
        $this->Set('AllowedUploadExtensions', $config->GetSectionKey(ConfigSection::UPLOADS, ConfigKeys::UPLOAD_RESERVATION_EXTENSIONS));
        $this->Set('UploadsEnabled', $config->GetSectionKey(ConfigSection::UPLOADS, ConfigKeys::UPLOAD_ENABLE_RESERVATION_ATTACHMENTS, new BooleanConverter()));
        $this->Set('AllowParticipation', $isAdmin || (!$hideUsers && !$config->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_PREVENT_PARTICIPATION, new BooleanConverter())));
        $this->Set('AllowGuestParticipation', $config->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_ALLOW_GUESTS, new BooleanConverter()));
        $remindersEnabled = $config->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_REMINDERS_ENABLED, new BooleanConverter());
        $emailEnabled = $config->GetKey(ConfigKeys::ENABLE_EMAIL, new BooleanConverter());
        $this->Set('RemindersEnabled', $remindersEnabled && $emailEnabled);
        $this->Set('TitleRequired', !$isAdmin && $config->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_TITLE_REQUIRED, new BooleanConverter()));
        $this->Set('DescriptionRequired', !$isAdmin && $config->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_DESCRIPTION_REQUIRED, new BooleanConverter()));
        $this->Set('PreventRecurrence', !$isAdmin && $config->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_PREVENT_RECURRENCE, new BooleanConverter()));
        $this->Set('AllowWaitList', Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_ALLOW_WAITLIST, new BooleanConverter()) && $this->server->GetUserSession()->IsLoggedIn());
        $this->Set('LimitParticipants', !$isAdmin && Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_LIMIT_INVITEES_TO_MAX_PARTICIPANTS, new BooleanConverter()));
        $this->Set('CheckboxThreshold', max(1, Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_MAXIMUM_RESOURCE_CHECKLIST, new IntConverter())));
        $this->Set('DeleteReasonRequired', !$isAdmin && $config->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_DELETE_REASON_REQUIRED, new BooleanConverter()));
        $this->Set('MeetingLinkEnabled', $config->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_ALLOW_MEETING_LINKS, new BooleanConverter()));
        $this->Set('NoGutter', true);
        $this->Display('Reservation/reserve-spa.tpl');
    }

    protected function GetReturnUrl()
    {
        $redirect = Page::CleanRedirect($this->GetQuerystring(QueryStringKeys::REDIRECT));
        if (!empty($redirect)) {
            return $redirect;
        }
        return $this->GetLastPage(Pages::SCHEDULE);
    }
}

$config = Configuration::Instance();
$guestReservations = $config->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_ALLOW_GUEST_BOOKING, new BooleanConverter()) && ServiceLocator::GetServer()->GetUserSession()->IsGuest();
$viewReservations = $config->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_VIEW_RESERVATIONS, new BooleanConverter());

$page = new ReservationReactPage();
if (!$guestReservations && !$viewReservations) {
    $page = new SecurePageDecorator($page);
}

$page->PageLoad();
