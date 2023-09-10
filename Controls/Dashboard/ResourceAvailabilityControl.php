<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/Dashboard/ResourceAvailabilityControlPresenter.php');
require_once(ROOT_DIR . 'Controls/Dashboard/DashboardItem.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Schedule/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Attributes/namespace.php');

interface IResourceAvailabilityControl
{
    /**
     * @param AvailableDashboardItem[] $items
     * @param int $total
     */
    public function SetAvailable($items, $total);

    /**
     * @param UnavailableDashboardItem[] $items
     * @param int $total
     */
    public function SetUnavailable($items, $total);

    /**
     * @param UnavailableDashboardItem[] $items
     * @param int $total
     */
    public function SetUnavailableAllDay($items, $total);

    /**
     * @param ResourceDto[] $favoriteResources
     * @param int[] $favoriteIds
     */
    public function SetFavorites($favoriteResources, $favoriteIds);

    /**
     * @param ResourceDto[] $allResources
     */
    public function SetAllResources($allResources);
}

class AvailableDashboardItem
{
    /**
     * @var ResourceDto
     */
    private $resource;
    /**
     * @var ReservationItemView|null
     */
    private $next;

    /**
     * @param ResourceDto $resource
     * @param ReservationItemView|null $nextItem
     */
    public function __construct(ResourceDto $resource, $nextItem = null)
    {
        $this->resource = $resource;
        $this->next = $nextItem;
    }

    /**
     * @return string
     */
    public function ResourceName()
    {
        return $this->resource->GetName();
    }

    /**
     * @return int
     */
    public function ResourceId()
    {
        return $this->resource->GetId();
    }

    /**
     * @return Date|null
     */
    public function NextTime()
    {
        if ($this->next != null) {
            return $this->next->StartDate;
        }

        return null;
    }

    /**
     * @return bool
     */
    public function HasColor()
    {
        if ($this->resource != null) {
            $color = $this->resource->GetColor();
            return !empty($color);
        }

        return false;
    }

    /**
     * @return string
     */
    public function GetTextColor()
    {
        if ($this->resource != null) {
            return $this->resource->GetTextColor();
        }

        return '';
    }

    /**
     * @return string
     */
    public function GetColor()
    {
        if ($this->resource != null) {
            return $this->resource->GetColor();
        }

        return '';
    }

    public function CanBook()
    {
        return $this->resource->CanBook;
    }
}

class UnavailableDashboardItem
{
    /**
     * @var ResourceDto
     */
    private $resource;

    /**
     * @var ReservationItemView
     */
    private $currentReservation;

    public function __construct(ResourceDto $resource, ReservationItemView $currentReservation)
    {
        $this->resource = $resource;
        $this->currentReservation = $currentReservation;
    }

    /**
     * @return string
     */
    public function ResourceName()
    {
        return $this->resource->GetName();
    }

    /**
     * @return int
     */
    public function ResourceId()
    {
        return $this->resource->GetId();
    }

    /**
     * @return Date|null
     */
    public function ReservationEnds()
    {
        return $this->currentReservation->EndDate;
    }

    public function GetColor()
    {
        return $this->currentReservation->GetColor();
    }

    public function GetTextColor()
    {
        return $this->currentReservation->GetTextColor();
    }

    public function CanBook()
    {
        return $this->resource->CanBook;
    }
}

class ResourceAvailabilityControl extends DashboardItem implements IResourceAvailabilityControl
{
    /**
     * @var ResourceAvailabilityControlPresenter
     */
    public $presenter;

    public function __construct(SmartyPage $smarty)
    {
        parent::__construct($smarty);

        $this->presenter = new ResourceAvailabilityControlPresenter($this,
            new ResourceService(new ResourceRepository(),
                new SchedulePermissionService(PluginManager::Instance()->LoadPermission()),
                new AttributeService(new AttributeRepository()),
                new UserRepository(),
                new AccessoryRepository()
            ),
            new ReservationViewRepository());
    }

    public function PageLoad()
    {
        $userSession = ServiceLocator::GetServer()->GetUserSession();
        $this->Set('Timezone', $userSession->Timezone);

        $this->presenter->PageLoad($userSession);

        $this->Display('resource_availability.tpl');
    }

    public function BindAvailability()
    {
        $userSession = ServiceLocator::GetServer()->GetUserSession();
        $this->Set('Timezone', $userSession->Timezone);
        $this->presenter->PageLoad($userSession);
        $this->Display('availability_details.tpl');
    }

    public function SetAvailable($items, $total)
    {
        $this->Assign('Available', $items);
        $this->Assign('TotalAvailable', $total);
    }

    public function SetUnavailable($items, $total)
    {
        $this->Assign('Unavailable', $items);
        $this->Assign('TotalUnavailable', $total);
    }

    public function SetUnavailableAllDay($items, $total)
    {
        $this->Assign('UnavailableAllDay', $items);
        $this->Assign('TotalUnavailableAllDay', $total);
    }


    public function SetFavorites($favoriteResources, $favoriteIds)
    {
        $this->Assign('FavoriteResources', $favoriteResources);
        $this->Assign('FavoriteIds', $favoriteIds);
        $this->Assign('CanAddFavorites', count($favoriteIds) < 10);
    }

    public function SetAllResources($allResources)
    {
        $this->Assign('AllResources', $allResources);
    }
}
