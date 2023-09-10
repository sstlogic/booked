<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/SecurePage.php');
require_once(ROOT_DIR . 'Pages/ActionPage.php');
require_once(ROOT_DIR . 'Presenters/Maps/ResourceMapsPresenter.php');

interface IResourceMapsPage extends IActionPage
{
    /**
     * @param ResourceMap[] $maps
     */
    public function BindMaps(array $maps);

    /**
     * @param ResourceDto[] $resources
     */
    public function BindResources(array $resources);

    public function GetMapId(): ?string;

    public function GetDate(): ?string;

    public function GetStartTime(): ?string;

    public function GetEndTime(): ?string;

    /**
     * @param ResourceMap $map
     * @param array|int[] $unavailableResourceIds
     * @param DateRange $duration
     * @param array|int[] $selectedResourceIds
     */
    public function BindAvailability(ResourceMap $map, array $unavailableResourceIds, DateRange $duration, array $selectedResourceIds);

    public function GetResourceIds(): array;
}

class ResourceMapsPage extends ActionPage implements IResourceMapsPage
{
    private ResourceMapsPresenter $presenter;

    public function __construct()
    {
        parent::__construct('ResourceMaps', 1);

        $this->presenter = new ResourceMapsPresenter($this,
            new ResourceMapsRepository(),
            ResourceService::Create(),
            new ReservationConflictIdentifier(new ResourceAvailability(new ReservationViewRepository()))
        );
    }

    public function ProcessAction()
    {
        $this->presenter->ProcessAction();;
    }

    public function ProcessDataRequest($dataRequest)
    {
        $this->presenter->ProcessDataRequest($dataRequest);
    }

    public function ProcessPageLoad()
    {
        $user = ServiceLocator::GetServer()->GetUserSession();
        $this->Set("DefaultDate", Date::Now()->ToTimezone($user->Timezone));
        $this->presenter->PageLoad($user);
        $this->Display('Maps/view-maps.tpl');
    }

    public function BindMaps(array $maps)
    {
        $this->Set('Maps', $maps);
    }

    public function BindResources(array $resources)
    {
        $this->Set('Resources', $resources);
    }


    public function BindAvailability(ResourceMap $map, array $unavailableResourceIds, DateRange $duration, array $selectedResourceIds)
    {
        $dto = new stdClass();
        $dto->publicId = $map->GetPublicId();
        $dto->imageUrl = $map->GetImageUrl();
        $dto->layers = $map->GetData()->layers;
        $dto->unavailableIds = $unavailableResourceIds;
        $dto->selectedResourceIds = $selectedResourceIds;
        $reservationDateFormat = Resources::GetInstance()->SystemDateTimeFormat();
        $scheduleDateFormat = Resources::GetInstance()->GetDateFormat('url');
        $dto->reserveTemplate = sprintf("%s/%s?%s=[rid]&%s=%s&%s=%s", Configuration::Instance()->GetScriptUrl(), UrlPaths::RESERVATION, QueryStringKeys::RESOURCE_ID, QueryStringKeys::START_DATE, $duration->GetBegin()->Format($reservationDateFormat), QueryStringKeys::END_TIME, $duration->GetEnd()->Format($reservationDateFormat));
        $dto->scheduleTemplate = sprintf("%s/%s?%s[]=[rid]&%s=[sid]&%s=%s#%s", Configuration::Instance()->GetScriptUrl(), Pages::SCHEDULE, QueryStringKeys::RESOURCE_ID, QueryStringKeys::SCHEDULE_ID, QueryStringKeys::START_DATE, $duration->GetBegin()->Format($scheduleDateFormat), $duration->GetBegin()->Format($scheduleDateFormat));
        $this->SetJson($dto);
    }

    public function GetMapId(): ?string
    {
        return $this->GetForm(FormKeys::MAP_ID);
    }

    public function GetDate(): ?string
    {
        return $this->GetForm(FormKeys::BEGIN_DATE);
    }

    public function GetStartTime(): ?string
    {
        return $this->GetForm(FormKeys::BEGIN_TIME);
    }

    public function GetEndTime(): ?string
    {
        return $this->GetForm(FormKeys::END_TIME);
    }

    public function GetResourceIds(): array
    {
        return $this->GetForm(FormKeys::RESOURCE_ID, true);
    }
}