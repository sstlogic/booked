<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/SearchAvailabilityPresenter.php');
require_once(ROOT_DIR . 'Pages/SecurePage.php');
require_once(ROOT_DIR . 'Pages/ActionPage.php');
require_once(ROOT_DIR . 'lib/Application/Schedule/namespace.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');

interface ISearchAvailabilityPage extends IActionPage, IRepeatOptionsComposite
{
    /**
     * @param ResourceDto[] $resources
     */
    public function SetResources($resources);

    /**
     * @param ResourceType[] $resourceTypes
     */
    public function SetResourceTypes($resourceTypes);

    /**
     * @param AvailableOpeningView[] $openings
     */
    public function ShowOpenings($openings);

    /**
     * @return int
     */
    public function GetRequestedHours();

    /**
     * @return int
     */
    public function GetRequestedMinutes();

    /**
     * @return string
     */
    public function GetRequestedRange();

    /**
     * @return string
     */
    public function GetRequestedStartDate();

    /**
     * @return string
     */
    public function GetRequestedEndDate();

    /**
     * @return int[]
     */
    public function GetResources();

    /**
     * @return int
     */
    public function GetResourceType();

    /**
     * @return int
     */
    public function GetMaxParticipants();

    /**
     * @return AttributeFormElement[]
     */
    public function GetResourceAttributeValues();

    /**
     * @return AttributeFormElement[]
     */
    public function GetResourceTypeAttributeValues();

    /**
     * @param Attribute[] $attributes
     */
    public function SetResourceAttributes($attributes);

    /**
     * @param Attribute[] $attributes
     */
    public function SetResourceTypeAttributes($attributes);

    /**
     * @return string|null
     */
    public function GetStartTime();

    /**
     * @return string|null
     */
    public function GetEndTime();

    /**
     * @return bool
     */
    public function SearchingSpecificTime();

    /**
     * @return bool
     */
    public function SearchingWithinRange();

    /**
     * @return string|null
     */
    public function GetRangeStartTime();

    /**
     * @return string|null
     */
    public function GetRangeEndTime();

    /**
     * @param Schedule[] $schedules
     */
    public function SetSchedules($schedules);

    /**
     * @return int|null
     */
    public function GetScheduleId();

    /**
     * @param DateRange $dateRange
     * @param int[] $resourceIds
     */
    public function AllowWaitlist(DateRange $dateRange, $resourceIds);

    /**
     * @return int[]
     */
    public function GetWaitlistResourceIds();

    /**
     * @return string
     */
    public function GetWaitlistStart();

    /**
     * @return string
     */
    public function GetWaitlistEnd();
}

class SearchAvailabilityPage extends ActionPage implements ISearchAvailabilityPage
{
    /**
     * @var SearchAvailabilityPresenter
     */
    private $presenter;

    public function __construct()
    {
        parent::__construct('FindATime');
        $resourceService = ResourceService::Create();
        $user = ServiceLocator::GetServer()->GetUserSession();

        $this->presenter = new SearchAvailabilityPresenter($this,
            $user,
            $resourceService,
            new ReservationService(new ReservationViewRepository(), new ReservationListingFactory()),
            new ScheduleService(new ScheduleRepository(), $resourceService, new DailyLayoutFactory()));

        $now = Date::Now();
        $this->Set('Today', $now->ToTimezone($user->Timezone));
        $this->Set('Tomorrow', $now->AddDays(1)->ToTimezone($user->Timezone));
        if ($now->Minute() < 30) {
            $startTime = $now->SetTime(new Time($now->Hour(), 30));
        } else {
            $startTime = $now->SetTime(new Time($now->Hour() + 1, 0));
        }
        $this->Set('StartTime', $startTime);
        $this->Set('TimeFormat', Resources::GetInstance()->GetDateFormat('timepicker'));
    }

    public function ProcessAction()
    {
        $this->presenter->ProcessAction();
    }

    public function ProcessDataRequest($dataRequest)
    {
        // TODO: Implement ProcessDataRequest() method.
    }

    public function ProcessPageLoad()
    {
        $this->presenter->PageLoad();
        $this->Display('SearchAvailability/search-availability.tpl');
    }

    public function SetResources($resources)
    {
        $this->Set('Resources', $resources);
    }

    public function SetResourceTypes($resourceTypes)
    {
        $this->Set('ResourceTypes', $resourceTypes);
    }

    public function ShowOpenings($openings)
    {
        $this->Set('Openings', $openings);
        $this->Display('SearchAvailability/search-availability-results.tpl');
    }

    public function GetRequestedHours()
    {
        return intval($this->GetForm(FormKeys::HOURS));
    }

    public function GetRequestedMinutes()
    {
        return intval($this->GetForm(FormKeys::MINUTES));
    }

    public function GetRequestedRange()
    {
        return $this->GetForm(FormKeys::AVAILABILITY_RANGE);
    }

    public function GetRequestedStartDate()
    {
        return $this->GetForm(FormKeys::BEGIN_DATE);
    }

    public function GetRequestedEndDate()
    {
        return $this->GetForm(FormKeys::END_DATE);
    }

    public function GetResources()
    {
        $resources = $this->GetForm(FormKeys::RESOURCE_ID);
        if (empty($resources)) {
            return array();
        }

        return $resources;
    }

    public function GetResourceType()
    {
        return $this->GetForm(FormKeys::RESOURCE_TYPE_ID);
    }

    public function GetMaxParticipants()
    {
        return $this->GetForm(FormKeys::MAX_PARTICIPANTS);
    }

    public function GetResourceAttributeValues()
    {
        return AttributeFormParser::GetAttributes($this->GetForm('r' . FormKeys::ATTRIBUTE_PREFIX));
    }

    public function GetResourceTypeAttributeValues()
    {
        return AttributeFormParser::GetAttributes($this->GetForm('rt' . FormKeys::ATTRIBUTE_PREFIX));
    }

    public function SetResourceAttributes($attributes)
    {
        $this->Set('ResourceAttributes', $attributes);
    }

    public function SetResourceTypeAttributes($attributes)
    {
        $this->Set('ResourceTypeAttributes', $attributes);
    }

    public function SetSchedules($schedules)
    {
        $this->Set('Schedules', $schedules);
    }

    public function GetRepeatType()
    {
        return $this->GetForm(FormKeys::REPEAT_OPTIONS);
    }

    public function GetRepeatInterval()
    {
        return $this->GetForm(FormKeys::REPEAT_EVERY);
    }

    public function GetRepeatWeekdays()
    {
        $days = array();

        $sun = $this->GetForm(FormKeys::REPEAT_SUNDAY);
        if (!empty($sun)) {
            $days[] = 0;
        }

        $mon = $this->GetForm(FormKeys::REPEAT_MONDAY);
        if (!empty($mon)) {
            $days[] = 1;
        }

        $tue = $this->GetForm(FormKeys::REPEAT_TUESDAY);
        if (!empty($tue)) {
            $days[] = 2;
        }

        $wed = $this->GetForm(FormKeys::REPEAT_WEDNESDAY);
        if (!empty($wed)) {
            $days[] = 3;
        }

        $thu = $this->GetForm(FormKeys::REPEAT_THURSDAY);
        if (!empty($thu)) {
            $days[] = 4;
        }

        $fri = $this->GetForm(FormKeys::REPEAT_FRIDAY);
        if (!empty($fri)) {
            $days[] = 5;
        }

        $sat = $this->GetForm(FormKeys::REPEAT_SATURDAY);
        if (!empty($sat)) {
            $days[] = 6;
        }

        return $days;
    }

    public function GetRepeatMonthlyType()
    {
        return $this->GetForm(FormKeys::REPEAT_MONTHLY_TYPE);
    }

    public function GetRepeatTerminationDate()
    {
        return $this->GetForm(FormKeys::END_REPEAT_DATE);
    }

    public function GetRepeatCustomDates()
    {
        $dates = $this->GetForm(FormKeys::REPEAT_CUSTOM_DATES);
        if (!is_array($dates) || empty($dates)) {
            return [];
        }

        return $dates;
    }

    public function GetStartTime()
    {
        return $this->GetForm(FormKeys::BEGIN_TIME);
    }

    public function GetEndTime()
    {
        return $this->GetForm(FormKeys::END_TIME);
    }

    public function SearchingSpecificTime()
    {
        return $this->GetForm(FormKeys::AVAILABILITY_SEARCH_TYPE) == "2";
    }

    public function SearchingWithinRange()
    {
        return !$this->GetCheckbox(FormKeys::ANY_TIME);
    }

    public function GetRangeStartTime()
    {
        return $this->GetForm(FormKeys::BEGIN_TIME_RANGE);
    }

    public function GetRangeEndTime()
    {
        return $this->GetForm(FormKeys::END_TIME_RANGE);
    }

    public function GetScheduleId()
    {
        $sid = $this->GetForm(FormKeys::SCHEDULE_ID);
        if (empty($sid)) {
            return null;
        }

        return intval($sid);
    }

    public function AllowWaitlist(DateRange $dateRange, $resourceIds)
    {
       $this->Set('WaitlistTime', $dateRange);
       $this->Set('WaitlistResources', $resourceIds);
    }

    public function GetWaitlistResourceIds()
    {
        $ids = $this->GetForm(FormKeys::RESOURCE_ID);
        if (!is_array($ids)) {
            return [$ids];
        }

        return $ids;
    }

    public function GetWaitlistStart()
    {
        return $this->GetForm(FormKeys::BEGIN_DATE);
    }

    public function GetWaitlistEnd()
    {
        return $this->GetForm(FormKeys::END_DATE);
    }
}