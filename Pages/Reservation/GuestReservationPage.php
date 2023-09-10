<?php

/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/Page.php');
require_once(ROOT_DIR . 'Pages/Reservation/IRequestedResourcePage.php');
require_once(ROOT_DIR . 'Presenters/Reservation/GuestReservationPresenter.php');

interface IGuestReservationPage extends IRequestedResourcePage
{

    /**
     * @return bool
     */
    public function GuestInformationCollected();

    /**
     * @return string
     */
    public function GetEmail();

    /**
     * @return bool
     */
    public function IsCreatingAccount();

    /**
     * @return bool
     */
    public function GetTermsOfServiceAcknowledgement();
}

class GuestReservationPage extends Page implements IGuestReservationPage
{
    public function __construct()
    {
        parent::__construct('NewReservation');
    }

    public function PageLoad()
    {
        if (Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_ALLOW_GUEST_BOOKING, new BooleanConverter())) {
            $presenter = $this->GetPresenter();
            $presenter->PageLoad();

            if ($this->GuestInformationCollected()) {
                $this->Redirect(Configuration::Instance()->GetScriptUrl() . '/'. UrlPaths::RESERVATION . '?' . $_SERVER['QUERY_STRING']);
            } else {

                $this->Set('ReturnUrl', Pages::SCHEDULE);
                $this->Display('Reservation/collect-guest.tpl');
            }
        } else {
            $this->RedirectToError(ErrorMessages::INSUFFICIENT_PERMISSIONS);
        }
    }

    protected function GetPresenter()
    {
        return new GuestReservationPresenter(
            $this,
            new GuestRegistration(new Password(), new UserRepository(), new GuestRegistrationNotificationStrategy(), new GuestReservationPermissionStrategy($this)),
            new WebAuthentication(PluginManager::Instance()->LoadAuthentication()),
            new ScheduleRepository());
    }

    public function GuestInformationCollected()
    {
        return !ServiceLocator::GetServer()->GetUserSession()->IsGuest();
    }

    public function GetEmail()
    {
        return $this->GetForm(FormKeys::EMAIL);
    }

    public function IsCreatingAccount()
    {
        return $this->IsPostBack() && !$this->GuestInformationCollected();
    }

    public function GetTermsOfServiceAcknowledgement()
    {
        return $this->GetCheckbox(FormKeys::TOS_ACKNOWLEDGEMENT);
    }

    public function GetRequestedResourceId()
    {
        return $this->server->GetQuerystring(QueryStringKeys::RESOURCE_ID);
    }

    public function GetRequestedResourcePublicId()
    {
        return $this->server->GetQuerystring(QueryStringKeys::PUBLIC_ID);
    }

    public function GetRequestedScheduleId()
    {
        return $this->server->GetQuerystring(QueryStringKeys::SCHEDULE_ID);
    }
}