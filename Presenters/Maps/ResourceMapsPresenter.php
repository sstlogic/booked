<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'Presenters/Maps/ResourceMapsPresenter.php');
require_once(ROOT_DIR . 'Domain/Access/ResourceMapsRepository.php');
require_once(ROOT_DIR . 'Domain/Access/ResourceRepository.php');
require_once(ROOT_DIR . 'lib/Application/Schedule/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/namespace.php');

class ResourceMapsActions
{
    const Search = "search";
}

class ResourceMapsPresenter extends ActionPresenter
{
    private IResourceMapsPage $page;
    private IResourceMapsRepository $mapsRepository;
    private IResourceService $resourceService;
    private IReservationConflictIdentifier $conflictIdentifier;

    public function __construct(IResourceMapsPage $page, IResourceMapsRepository $mapsRepository, IResourceService $resourceService, IReservationConflictIdentifier $conflictIdentifier)
    {
        parent::__construct($page);
        $this->page = $page;
        $this->mapsRepository = $mapsRepository;
        $this->resourceService = $resourceService;
        $this->conflictIdentifier = $conflictIdentifier;

        $this->AddAction(ResourceMapsActions::Search, "LoadMapAvailability");
    }

    public function PageLoad(UserSession $user)
    {
        $showInaccessible = Configuration::Instance()->GetSectionKey(ConfigSection::SCHEDULE, ConfigKeys::SCHEDULE_SHOW_INACCESSIBLE_RESOURCES, new BooleanConverter());
        $maps = $this->mapsRepository->LoadPublished();
        $resources = $this->resourceService->GetAllResources(!$showInaccessible, $user);

        $this->page->BindMaps($maps);
        $this->page->BindResources($resources);
    }

    public function ProcessDataRequest(string $dr)
    {
        if ($dr == "map") {
            $this->LoadMapAvailability();
        }
    }

    public function LoadMapAvailability()
    {
        $mapId = $this->page->GetMapId();
        $date = $this->page->GetDate();
        $startTime = $this->page->GetStartTime();
        $endTime = $this->page->GetEndTime();
        $searchResourceIds = array_map('intval', $this->page->GetResourceIds());

        $map = $this->mapsRepository->LoadById($mapId);

        if (!empty($map)) {
            $user = ServiceLocator::GetServer()->GetUserSession();
            $date = Date::Parse($date, $user->Timezone);

            $duration = new DateRange($date->SetTimeString($startTime), $date->SetTimeString($endTime));

            $resources = [];
            foreach ($map->GetData()->layers as $layer) {
                if (empty($searchResourceIds) || in_array($layer->resourceId, $searchResourceIds)) {
                    $resources[] = $this->resourceService->GetResource($layer->resourceId);
                }
            }
            $unavailableResourceIds = $this->conflictIdentifier->GetUnavailableResourceIds($resources, $duration, null, new ReservationRepository(), $user);
            $this->page->BindAvailability($map, $unavailableResourceIds, $duration, $searchResourceIds);
        }
    }
}