<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/UnavailableResourcesPresenter.php');

interface IAvailableResourcesPage
{
    public function GetStartDate();

    public function GetEndDate();

    public function GetStartTime();

    public function GetEndTime();

    public function GetReferenceNumber();

    /**
     * @param int[] $unavailableResourceIds
     */
    public function BindUnavailable($unavailableResourceIds, $skippedCheck = false);

    /**
     * @return int
     */
    public function GetScheduleId();
}

class UnavailableResourcesPage extends Page implements IAvailableResourcesPage
{
    public function __construct()
    {
        parent::__construct('', 1);
    }

    public function PageLoad()
    {
		$resourceAvailability = new ResourceAvailability(new ReservationViewRepository());
		$presenter = new UnavailableResourcesPresenter($this,
													   new ReservationConflictIdentifier($resourceAvailability),
													   ServiceLocator::GetServer()->GetUserSession(),
													   new ResourceRepository(),
													   new ReservationRepository(),
													   $resourceAvailability);
        $presenter->PageLoad();
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

    public function GetStartTime()
    {
        return $this->GetQuerystring(QueryStringKeys::START_TIME);
    }

    public function GetEndTime()
    {
        return $this->GetQuerystring(QueryStringKeys::END_TIME);
    }

    public function BindUnavailable($unavailableResourceIds, $skippedCheck = false)
    {
        $this->SetJson(['rids' => $unavailableResourceIds, 'skippedCheck' => $skippedCheck]);
    }

    public function GetScheduleId()
    {
        return $this->GetQuerystring(QueryStringKeys::SCHEDULE_ID);
    }
}