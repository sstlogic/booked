<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/ActionPage.php');
require_once(ROOT_DIR . 'Presenters/Api/ResourcesApiPresenter.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/namespace.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');

interface IResourcesApiPage extends IActionPage
{
    /**
     * @return int
     */
    public function GetScheduleId();

    /**
     * @return string
     */
    public function GetStartDate();

    /**
     * @return string
     */
    public function GetEndDate();

    /**
     * @return string
     */
    public function GetReferenceNumber();

    /**
     * @return int[]
     */
    public function GetResourceIds();
}

class ResourcesApiPage extends ActionPage implements IResourcesApiPage
{
    /**
     * @var ResourcesApiPresenter
     */
    private $presenter;

    public function __construct()
    {
        parent::__construct("", 1);

        $userSession = ServiceLocator::GetServer()->GetUserSession();

        $resourceAvailability = new ResourceAvailability(new ReservationViewRepository());

        $resourceService = new ResourceService(
            new ResourceRepository(),
            $userSession->IsLoggedIn() ? PluginManager::Instance()->LoadPermission() : new GuestPermissionService(),
            new AttributeService(new AttributeRepository()),
            new UserRepository(),
            new AccessoryRepository());

        $this->presenter = new ResourcesApiPresenter($this,
            $resourceService,
            new ReservationConflictIdentifier($resourceAvailability),
            $userSession,
            new ReservationRepository());
    }

    public function ProcessAction()
    {
        // no-op
    }

    public function ProcessDataRequest($dataRequest)
    {
        // no-op
    }

    public function ProcessPageLoad()
    {
        // no-op
    }

    protected function ProcessApiCall($json)
    {
        if ($this->AllowAccess()) {
            $this->presenter->ProcessApi($json);
        } else {
            $this->SetJsonResponse(['Unauthorized' => true], 'Unauthorized', 401);
        }
    }

    /**
     * @return bool
     */
    protected function AllowAccess(): bool
    {
        return $this->server->GetUserSession()->IsLoggedIn() || Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_VIEW_SCHEDULES, new BooleanConverter());
    }

    public function GetScheduleId()
    {
        return intval($this->GetQuerystring(QueryStringKeys::SCHEDULE_ID));
    }

    public function GetStartDate()
    {
        return $this->GetQuerystring(QueryStringKeys::START_DATE);
    }

    public function GetEndDate()
    {
        return $this->GetQuerystring(QueryStringKeys::END_DATE);
    }

    public function GetReferenceNumber()
    {
        return $this->GetQuerystring(QueryStringKeys::REFERENCE_NUMBER);
    }

    public function GetResourceIds()
    {
       $ids = $this->GetQuerystring(QueryStringKeys::RESOURCE_ID);
       if (empty($ids)) {
           return [];
       }

       return array_map('intval', explode(",", $ids));
    }
}

