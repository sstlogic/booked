<?php

/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/ResourceDisplayPresenter.php');
require_once(ROOT_DIR . 'Pages/Reservation/IRequestedResourcePage.php');
require_once(ROOT_DIR . 'Pages/Ajax/AutoCompletePage.php');

interface IResourceDisplayPage extends IPage, IActionPage, IRequestedResourcePage
{
    public function DisplayLogin();

    /**
     * @return string
     */
    public function GetResourceId();

    /**
     * @return string
     */
    public function GetPublicResourceId();

    /**
     * @return string
     */
    public function GetEmail();

    /**
     * @return string
     */
    public function GetPassword();

    public function BindInvalidLogin();

    /**
     * @param BookableResource[] $resourceList
     */
    public function BindResourceList($resourceList);

    /**
     * @param $publicId
     */
    public function SetActivatedResourceId($publicId);

    public function BindResource(BookableResource $resource);

    /**
     * @param IDailyLayout $dailyLayout
     * @param Date $today
     * @param ReservationListItem[] $current
     * @param ReservationListItem|null $next
     * @param ReservationListItem[] $upcoming
     * @param bool $requiresCheckin
     * @param string $checkinReferenceNumber
     */
    public function DisplayAvailability(IDailyLayout $dailyLayout, Date $today, $current, $next, $upcoming, $requiresCheckin, $checkinReferenceNumber, $dtos);

    /**
     * @param bool $availableNow
     */
    public function SetIsAvailableNow($availableNow);

    public function DisplayNotEnabled();

    public function DisplayResourceShell();

    /**
     * @return string
     */
    public function GetTimezone();

    /**
     * @return string
     */
    public function GetBeginTime();

    /**
     * @return string
     */
    public function GetEndTime();

    /**
     * @param bool $success
     * @param ReservationResultCollector $resultCollector
     */
    public function SetReservationSaveResults($success, $resultCollector);

    /**
     * @param bool $success
     * @param ReservationResultCollector $resultCollector
     */
    public function SetReservationCheckinResults($success, $resultCollector);

    /**
     * @param Schedule $schedule
     */
    public function BindSchedule(Schedule $schedule);

    /**
     * @param Attribute[] $attributes
     */
    public function BindAttributes($attributes);

    /**
     * @return AttributeFormElement[]|array
     */
    public function GetAttributes();

    /**
     * @return string
     */
    public function GetReferenceNumber();

    /**
     * @param TermsOfService $termsOfService
     */
    public function SetTerms($termsOfService);

    /**
     * @return bool
     */
    public function GetTermsOfServiceAcknowledgement();

	public function DisplayInstructions();
}

class ResourceDisplayPage extends ActionPage implements IResourceDisplayPage
{
    /**
     * @var ResourceDisplayPresenter
     */
    private $presenter;

    public function __construct()
    {
        parent::__construct('Resource');
        $this->presenter = new ResourceDisplayPresenter($this,
            new ResourceRepository(),
            new ReservationService(new ReservationViewRepository(),
                new ReservationListingFactory()),
            PluginManager::Instance()->LoadAuthorization(),
            new WebAuthentication(PluginManager::Instance()->LoadAuthentication()),
            new ScheduleRepository(),
            new DailyLayoutFactory(),
            new GuestUserService(new Authentication(PluginManager::Instance()->LoadAuthorization(), new UserRepository(), new GroupRepository()),
                new GuestRegistration(new Password(),
                    new UserRepository(),
                    new GuestRegistrationNotificationStrategy(),
                    new GuestReservationPermissionStrategy($this))),
            new AttributeService(new AttributeRepository(), new GuestPermissionService()),
            new ReservationRepository(),
            new TermsOfServiceRepository());

        $this->Set('AllowAutocomplete', Configuration::Instance()->GetSectionKey(ConfigSection::TABLET_VIEW, ConfigKeys::TABLET_VIEW_AUTOCOMPLETE, new BooleanConverter()));
		$this->Set('Terms', null);
		$this->Set('ShouldLogout', false);
    }

    public function ProcessAction()
    {
        $this->presenter->ProcessAction();
    }

    public function ProcessDataRequest($dataRequest)
    {
        $this->presenter->ProcessDataRequest($dataRequest);
    }

    public function ProcessPageLoad()
    {
        $this->presenter->PageLoad();
    }

    public function GetResourceId()
    {
        return $this->GetForm(FormKeys::RESOURCE_ID);
    }

    public function DisplayLogin()
    {
        $this->Display('ResourceDisplay/resource-display-login.tpl');
    }

    public function EnforceCSRFCheck()
    {
        // no op
    }

    public function GetEmail()
    {
        return $this->GetForm(FormKeys::EMAIL);
    }

    public function GetPassword()
    {
        return $this->GetForm(FormKeys::PASSWORD);
    }

    public function BindInvalidLogin()
    {
        $this->SetJson(array('error' => true));
    }

    public function BindResourceList($resourceList)
    {
        $resources = array();
        foreach ($resourceList as $resource) {
            $resources[] = array('id' => $resource->GetId(), 'name' => $resource->GetName());
        }

        $this->SetJson(array('resources' => $resources));
    }

    public function SetActivatedResourceId($publicId)
    {
        $resourceDisplayUrl = Configuration::Instance()->GetScriptUrl() . '/' . Pages::DISPLAY_RESOURCE . '?' . QueryStringKeys::RESOURCE_ID . '=' . $publicId;
        $this->SetJson(array('location' => $resourceDisplayUrl));
    }

    public function GetPublicResourceId()
    {
        return $this->GetQuerystring(QueryStringKeys::RESOURCE_ID);
    }

    public function BindResource(BookableResource $resource)
    {
        $this->Set('ResourceName', $resource->GetName());
        $this->Set('ResourceId', $resource->GetId());
    }

    public function DisplayAvailability(IDailyLayout $dailyLayout, Date $today, $current, $next, $upcoming, $requiresCheckin, $checkinReferenceNumber, $dtos)
    {
        $this->Set('TimeFormat', Resources::GetInstance()->GetDateFormat('period_time'));
        $this->Set('Today', $today);
        $this->Set('Now', Date::Now());
        $this->Set('DailyLayout', $dailyLayout);
        $this->Set('SlotLabelFactory', new SlotLabelFactory(new NullUserSession()));
        $this->Set('CurrentReservations', $current);
        $this->Set('NextReservation', $next);
        $this->Set('UpcomingReservations', $upcoming);
        $this->Set('RequiresCheckin', $requiresCheckin);
        $this->Set('CheckinReferenceNumber', $checkinReferenceNumber);
        $this->Set('NoTitle', Resources::GetInstance()->GetString('NoTitleLabel'));
        $this->Set('dtos', $dtos);
        $this->Display('ResourceDisplay/resource-display-resource.tpl');
    }

    public function SetIsAvailableNow($availableNow)
    {
        $this->Set('AvailableNow', $availableNow);
    }

    public function DisplayNotEnabled()
    {
        $this->Display('ResourceDisplay/resource-display-not-enabled.tpl');
    }

    public function DisplayResourceShell()
    {
        $this->Set('PublicResourceId', $this->GetPublicResourceId());
        $this->Display('ResourceDisplay/resource-display-shell.tpl');
    }

    public function GetTimezone()
    {
        return $this->GetForm(FormKeys::TIMEZONE);
    }

    public function GetBeginTime()
    {
        return $this->GetForm(FormKeys::BEGIN_PERIOD);
    }

    public function GetEndTime()
    {
        return $this->GetForm(FormKeys::END_PERIOD);
    }

    public function SetReservationSaveResults($success, $resultCollector)
    {
        $this->SetJson(array('success' => $success, 'errors' => $resultCollector->Errors));
    }

    public function SetReservationCheckinResults($success, $resultCollector)
    {
        $this->SetJson(array('success' => $success, 'errors' => $resultCollector->Errors));
    }

    public function GetRequestedResourceId()
    {
        return $this->GetResourceId();
    }

	public function GetRequestedResourcePublicId()
	{
		return "";
	}

    public function GetRequestedScheduleId()
    {
        return $this->GetForm(FormKeys::SCHEDULE_ID);
    }

    public function BindSchedule(Schedule $schedule)
    {
        $this->Set('ScheduleId', $schedule->GetId());
        $this->Set('Timezone', $schedule->GetTimezone());
    }

    public function BindAttributes($attributes)
    {
        $this->Set('Attributes', $attributes);
    }

    public function GetAttributes()
    {
        return AttributeFormParser::GetAttributes($this->GetForm(FormKeys::ATTRIBUTE_PREFIX));
    }

    public function GetReferenceNumber()
    {
        return $this->GetForm(FormKeys::REFERENCE_NUMBER);
    }

    public function SetTerms($termsOfService)
    {
        $this->Set('Terms', $termsOfService);
    }

    public function GetTermsOfServiceAcknowledgement()
    {
        return $this->GetCheckbox(FormKeys::TOS_ACKNOWLEDGEMENT);
    }

	public function DisplayInstructions()
	{
		$this->Display('ResourceDisplay/resource-display-instructions.tpl');
	}


}