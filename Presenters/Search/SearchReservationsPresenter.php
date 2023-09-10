<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'Pages/Search/SearchReservationsPage.php');

class SearchReservationsPresenter extends ActionPresenter
{
    /**
     * @var ISearchReservationsPage
     */
    private $page;
    /**
     * @var UserSession
     */
    private $user;
    /**
     * @var IReservationViewRepository
     */
    private $reservationViewRepository;
    /**
     * @var IResourceService
     */
    private $resourceService;
    /**
     * @var IScheduleService
     */
    private $scheduleService;
    /**
     * @var IAttributeService
     */
    private $attributeService;

    public function __construct(ISearchReservationsPage    $page,
                                UserSession                $user,
                                IReservationViewRepository $reservationViewRepository,
                                IResourceService           $resourceService,
                                IScheduleService           $scheduleService,
                                IAttributeService          $attributeService)
    {
        parent::__construct($page);

        $this->page = $page;
        $this->user = $user;
        $this->reservationViewRepository = $reservationViewRepository;
        $this->resourceService = $resourceService;
        $this->scheduleService = $scheduleService;
        $this->attributeService = $attributeService;

        $this->AddAction('search', 'SearchReservations');
    }

    public function PageLoad()
    {
        $this->page->SetResources($this->resourceService->GetAllResources(false, $this->user));
        $this->page->SetSchedules($this->scheduleService->GetAll(false, $this->user));
        $this->page->SetAttributes($this->attributeService->GetAttributes(CustomAttributeCategory::RESERVATION, $this->user)->GetDefinitions());
        $this->page->SetCurrentUser($this->user);
        $this->page->SetToday(Date::Now()->ToTimezone($this->user->Timezone));
    }

    public function SearchReservations()
    {
        $allAttributes = $this->attributeService->GetAttributes(CustomAttributeCategory::RESERVATION, $this->user)->GetAttributes();
        $filterAttributes = $this->page->GetAttributeValues();

        $attributes = [];
        foreach ($filterAttributes as $fa) {
            if (!empty($fa->Value)) {
                foreach ($allAttributes as $aa) {
                    if ($aa->Id() == $fa->Id) {
                        $aa->SetValue($fa->Value);
                        $attributes[] = $aa;
                    }
                }
            }
        }

        $userId = $this->page->GetUserId();

        $currentUser = ServiceLocator::GetServer()->GetUserSession();
        $hideUsers = Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_HIDE_USER_DETAILS, new BooleanConverter());
        $hideReservations = Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_HIDE_RESERVATION_DETAILS, new BooleanConverter());
        if (!$currentUser->IsAdmin && ($hideUsers || $hideReservations)) {
            $userId = $currentUser->UserId;
        }

        $range = $this->GetSearchRange();
        $level = $this->page->GetUserLevel();
        $filter = ReservationsSearchFilter::GetFilter($range->GetBegin(),
            $range->GetEnd(),
            $userId,
            $this->page->GetResources(),
            $this->page->GetSchedules(),
            $this->page->GetTitle(),
            $this->page->GetDescription(),
            $this->page->GetReferenceNumber(),
            $attributes,
            $level);

        $list = $this->reservationViewRepository->GetList(0, 100, null, null, $filter, $level);

        $rids = $this->GetAllowedResourceIds();
        $results = [];
        /** @var ReservationItemView $r */
        foreach ($list->Results() as $r) {
            if (in_array($r->ResourceId, $rids)) {
                $results[] = $r;
            }
        }
        $this->page->ShowReservations($results, $this->user->Timezone);
    }

    /**
     * @return DateRange
     */
    private function GetSearchRange()
    {
        $range = $this->page->GetRequestedRange();
        $timezone = $this->user->Timezone;

        $today = Date::Now()->ToTimezone($timezone);

        if ($range == 'tomorrow') {
            return new DateRange($today->AddDays(1)->GetDate(), $today->AddDays(2)->GetDate());
        }

        $weekoffset = Configuration::Instance()->GetKey(ConfigKeys::FIRST_DAY_OF_WEEK, new IntConverter());

        if ($range == 'thisweek') {
            $weekday = $today->Weekday();
            $adjustedDays = (0 - $weekday);

            if ($weekday < 0) {
                $adjustedDays = $adjustedDays - 7;
            }

            $startDate = $today->AddDays($adjustedDays + $weekoffset)->GetDate();

            return new DateRange($startDate, $startDate->AddDays(7));
        }

        if ($range == 'nextweek') {
            $weekday = $today->Weekday();
            $adjustedDays = (0 - $weekday);

            if ($weekday < 0) {
                $adjustedDays = $adjustedDays - 7;
            }

            $startDate = $today->AddDays($adjustedDays + 7 + $weekoffset)->GetDate();

            return new DateRange($startDate, $startDate->AddDays(7));
        }

        if ($range == 'daterange') {
            $start = $this->page->GetRequestedStartDate();
            $end = $this->page->GetRequestedEndDate();

            if (empty($start)) {
                $start = Date::Now()->ToTimezone($timezone);
            }
            if (empty($end)) {
                $end = Date::Now()->ToTimezone($timezone);
            }
            return new DateRange(Date::Parse($start, $timezone), Date::Parse($end, $timezone)->AddDays(1));
        }

        return new DateRange($today->GetDate(), $today->AddDays(1)->GetDate());
    }

    private function GetAllowedResourceIds()
    {
        $resources = $this->resourceService->GetAllResources(false, $this->user);
        $rids = [];
        foreach ($resources as $r) {
            $rids[] = $r->Id;
        }

        return $rids;
    }

}

class ReservationsSearchFilter
{
    /**
     * @param Date|null $startDate
     * @param Date|null $endDate
     * @param int|null $userId
     * @param int[]|null $resourceIds
     * @param int[]|null $scheduleIds
     * @param string|null $title
     * @param string|null $description
     * @param string|null $referenceNumber
     * @param \Booked\Attribute[] $attributes
     * @param int|null $userLevel
     * @return SqlFilterNull
     */
    public static function GetFilter($startDate, $endDate, $userId, $resourceIds, $scheduleIds, $title, $description, $referenceNumber, $attributes, $userLevel)
    {
        $filter = new SqlFilterNull();
        $surroundFilter = null;
        $startFilter = null;
        $endFilter = null;

        if (!empty($referenceNumber)) {
            $filter->_And(new SqlFilterEquals(ColumnNames::REFERENCE_NUMBER, $referenceNumber));
            return $filter;
        }

        if (!empty($startDate) && !empty($endDate)) {
            $surroundFilter = new SqlFilterLessThan(new SqlRepeatingFilterColumn(TableNames::RESERVATION_INSTANCES_ALIAS, ColumnNames::RESERVATION_START, 1), $startDate->ToDatabase(), true);
            $surroundFilter->_And(new SqlFilterGreaterThan(new SqlRepeatingFilterColumn(TableNames::RESERVATION_INSTANCES_ALIAS, ColumnNames::RESERVATION_END, 1), $endDate->ToDatabase(), true));
        }
        if (!empty($startDate)) {
            $startFilter = new SqlFilterGreaterThan(new SqlRepeatingFilterColumn(TableNames::RESERVATION_INSTANCES_ALIAS, ColumnNames::RESERVATION_START, 2), $startDate->ToDatabase(), true);
            $endFilter = new SqlFilterGreaterThan(new SqlRepeatingFilterColumn(TableNames::RESERVATION_INSTANCES_ALIAS, ColumnNames::RESERVATION_END, 2), $startDate->ToDatabase(), true);
        }
        if (!empty($endDate)) {
            if ($startFilter == null) {
                $startFilter = new SqlFilterLessThan(new SqlRepeatingFilterColumn(TableNames::RESERVATION_INSTANCES_ALIAS, ColumnNames::RESERVATION_START, 3), $endDate->ToDatabase(), true);
            } else {
                $startFilter->_And(new SqlFilterLessThan(new SqlRepeatingFilterColumn(TableNames::RESERVATION_INSTANCES_ALIAS, ColumnNames::RESERVATION_START, 4), $endDate->ToDatabase(), true));
            }
            if ($endFilter == null) {
                $endFilter = new SqlFilterLessThan(new SqlRepeatingFilterColumn(TableNames::RESERVATION_INSTANCES_ALIAS, ColumnNames::RESERVATION_END, 3), $endDate->ToDatabase(), true);
            } else {
                $endFilter->_And(new SqlFilterLessThan(new SqlRepeatingFilterColumn(TableNames::RESERVATION_INSTANCES_ALIAS, ColumnNames::RESERVATION_END, 4), $endDate->ToDatabase(), true));
            }
        }
        if (!empty($title)) {
            $filter->_And(new SqlFilterLike(new SqlFilterColumn(TableNames::RESERVATION_SERIES_ALIAS, ColumnNames::RESERVATION_TITLE), $title));
        }
        if (!empty($description)) {
            $filter->_And(new SqlFilterLike(new SqlFilterColumn(TableNames::RESERVATION_SERIES_ALIAS, ColumnNames::RESERVATION_DESCRIPTION), $description));
        }
        if (!empty($scheduleIds)) {
            $filter->_And(new SqlFilterIn(new SqlFilterColumn(TableNames::RESOURCES, ColumnNames::SCHEDULE_ID), $scheduleIds));
        }
        if (!empty($resourceIds)) {
            $filter->_And(new SqlFilterIn(new SqlFilterColumn(TableNames::RESOURCES, ColumnNames::RESOURCE_ID), $resourceIds));
        }

        if (!empty($userId)) {
            if (empty($userLevel) || $userLevel == ReservationUserLevel::OWNER) {
                $filter->_And(new SqlFilterEquals(new SqlFilterColumn(TableNames::USERS, ColumnNames::USER_ID), $userId));
            } else {
                $filter->_And(new SqlFilterEquals(new SqlFilterColumn(TableNames::RESERVATION_USERS_ALIAS, ColumnNames::USER_ID), $userId));
            }
        }
        else {
            if (Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_HIDE_RESERVATION_DETAILS, new BooleanConverter())) {
                $filter->_And(new SqlFilterEquals(new SqlFilterColumn(TableNames::USERS, ColumnNames::USER_ID), ServiceLocator::GetServer()->GetUserSession()->UserId));
            }
        }

        if (!empty($attributes)) {
            $filter->_And(AttributeFilter::Create(TableNames::RESERVATION_SERIES_ALIAS . '.' . ColumnNames::SERIES_ID, $attributes));
        }

        if ($surroundFilter != null || $startFilter != null || $endFilter != null) {
            $dateFilter = new SqlFilterNull(true);
            $dateFilter->_Or($surroundFilter)->_Or($startFilter)->_Or($endFilter);
            $filter->_And($dateFilter);
        }

        return $filter;
    }
}
