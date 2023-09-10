<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/SecurePage.php');
require_once(ROOT_DIR . 'Pages/Ajax/AutoCompletePage.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Config/namespace.php');
require_once(ROOT_DIR . 'lib/Common/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Schedule/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/namespace.php');
require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'Presenters/Calendar/CalendarFilters.php');

class CalendarActions
{
    const ActionEnableSubscription = 'enable';
    const ActionDisableSubscription = 'disable';
}

interface ICommonCalendarPage extends IActionPage
{
    /**
     * @return string
     */
    public function GetDay();

    /**
     * @return string
     */
    public function GetMonth();

    /**
     * @return string
     */
    public function GetYear();

    /**
     * @return string
     */
    public function GetCalendarType();

    /**
     * @param CalendarReservation[] $reservationList
     */
    public function BindEvents($reservationList);

    /**
     * @return string
     */
    public function GetStartDate();

    /**
     * @return string
     */
    public function GetEndDate();

    /**
     * @param ResourceGroup $selectedGroup
     */
    public function BindSelectedGroup($selectedGroup);

    /**
     * @param int $firstDay
     */
    public function SetFirstDay($firstDay);

    /**
     * @param CalendarSubscriptionDetails $subscriptionDetails
     */
    public function BindSubscription(CalendarSubscriptionDetails $subscriptionDetails);

    /**
     * @param Date $displayDate
     */
    public function SetDisplayDate($displayDate);

    /**
     * @param string $calendarType
     */
    public function BindCalendarType($calendarType);

    /**
     * @param CalendarFilters $filters
     */
    public function BindFilters($filters);

    /**
     * @return null|int
     */
    public function GetScheduleId();

    /**
     * @return null|int
     */
    public function GetResourceId();

    /**
     * @return null|int
     */
    public function GetGroupId();

    /**
     * @param $scheduleId null|int
     */
    public function SetScheduleId($scheduleId);

    /**
     * @param $resourceId null|int
     */
    public function SetResourceId($resourceId);

    /**
     * @param $groupId null|int
     */
    public function SetGroupId($groupId);

    public function RenderSubscriptionDetails();

    /**
     * @return null|int
     */
    public function GetUserId();

    /**
     * @return null|int
     */
    public function GetParticipantId();
}

abstract class CommonCalendarPage extends ActionPage implements ICommonCalendarPage
{

    public function GetDay()
    {
        return $this->GetQuerystring(QueryStringKeys::DAY);
    }

    public function GetMonth()
    {
        return $this->GetQuerystring(QueryStringKeys::MONTH);
    }

    public function GetYear()
    {
        return $this->GetQuerystring(QueryStringKeys::YEAR);
    }

    public function GetCalendarType()
    {
        return $this->GetQuerystring(QueryStringKeys::CALENDAR_TYPE);
    }

    public function BindCalendarType($calendarType)
    {
        $calendarType = empty($calendarType) ? CalendarTypes::Month : $calendarType;
        if (!in_array($calendarType, [CalendarTypes::Day, CalendarTypes::Month, CalendarTypes::Week])) {
            $calendarType = CalendarTypes::Month;
        }
        $this->Set('CalendarType', $calendarType);
    }

    /**
     * @param Date $displayDate
     * @return void
     */
    public function SetDisplayDate($displayDate)
    {
        $this->Set('DisplayDate', $displayDate);

        $months = Resources::GetInstance()->GetMonths('full');
        $this->Set('MonthName', $months[$displayDate->Month() - 1]);
        $this->Set('MonthNames', $months);
        $this->Set('MonthNamesShort', Resources::GetInstance()->GetMonths('abbr'));

        $days = Resources::GetInstance()->GetDays('full');
        $this->Set('DayName', $days[$displayDate->Weekday()]);
        $this->Set('DayNames', $days);//[$days[1], $days[2], $days[3], $days[4], $days[5], $days[6], $days[0]]);  //fullcalendar looks to be 1 based
        $short = Resources::GetInstance()->GetDays('abbr');
        $this->Set('DayNamesShort', $short);//[$short[1], $short[2], $short[3], $short[4], $short[5], $short[6], $short[0]]);  //fullcalendar looks to be 1 based
        $this->Set('ShowWeekNumbers', Configuration::Instance()->GetSectionKey(ConfigSection::SCHEDULE, ConfigKeys::SCHEDULE_SHOW_WEEK_NUMBERS, new BooleanConverter()));
    }

    /**
     * @param CalendarFilters $filters
     * @return void
     */
    public function BindFilters($filters)
    {
        $this->Set('filters', $filters);
        $this->Set('IsAccessible', !$filters->IsEmpty());
        $this->Set('ShowResourceGroups', $filters->HasGroups());
        $this->Set('ResourceGroupsAsJson', json_encode($filters->GetResourceGroupTree()->GetGroups(false)));;
    }

    public function GetScheduleId()
    {
        return $this->GetQuerystring(QueryStringKeys::SCHEDULE_ID);
    }

    public function GetResourceId()
    {
        return $this->GetQuerystring(QueryStringKeys::RESOURCE_ID);
    }

    public function GetGroupId()
    {
        return $this->GetQuerystring(QueryStringKeys::GROUP_ID);
    }

    public function GetUserId()
    {
        return $this->GetQuerystring(QueryStringKeys::USER_ID);
    }

    public function GetParticipantId()
    {
        return $this->GetQuerystring(QueryStringKeys::PARTICIPANT_ID);
    }

    /**
     * @param $scheduleId null|int
     * @return void
     */
    public function SetScheduleId($scheduleId)
    {
        $this->Set('ScheduleId', $scheduleId);
    }

    /**
     * @param $resourceId null|int
     * @return void
     */
    public function SetResourceId($resourceId)
    {
        $rid = intval($resourceId);
        $this->Set('ResourceId', empty($rid) ? '' : $rid);
    }

    /**
     * @param $groupId null|int
     * @return void
     */
    public function SetGroupId($groupId)
    {
        $gid = intval($groupId);
        $this->Set('gid', empty($gid) ? '' : $gid);
    }

    /**
     * @param CalendarSubscriptionDetails $details
     */
    public function BindSubscription(CalendarSubscriptionDetails $details)
    {
        $this->Set('IsSubscriptionAllowed', $details->IsAllowed());
        $this->Set('IsSubscriptionEnabled', $details->IsEnabled());
        $this->Set('SubscriptionUrl', $details->Url());
    }

    /**
     * @param int $firstDay
     */
    public function SetFirstDay($firstDay)
    {
        $this->Set('FirstDay', intval($firstDay == Schedule::Today ? Configuration::Instance()->GetKey(ConfigKeys::FIRST_DAY_OF_WEEK, new IntConverter()) : $firstDay));
    }

    /**
     * @param ResourceGroup $selectedGroup
     */
    public function BindSelectedGroup($selectedGroup)
    {
        $this->Set('SelectedGroupName', $selectedGroup->name);
        $this->Set('SelectedGroupNode', $selectedGroup->id);
        $this->Set('SelectedGroupId', $selectedGroup->id);
    }

    public function BindEvents($reservationList)
    {
        $events = array();
        foreach ($reservationList as $r) {
            $events[] = $r->AsFullCalendarEvent();
        }

        $this->SetJson($events);
    }

    public function GetStartDate()
    {
        return $this->GetQuerystring(QueryStringKeys::START);
    }

    public function GetEndDate()
    {
        return $this->GetQuerystring(QueryStringKeys::END);
    }
}

abstract class CommonCalendarPresenter extends ActionPresenter
{
    /**
     * @var ICommonCalendarPage
     */
    protected $page;

    /**
     * @var IReservationViewRepository
     */
    protected $reservationRepository;

    /**
     * @var IScheduleRepository
     */
    protected $scheduleRepository;

    /**
     * @var IResourceService
     */
    protected $resourceService;

    /**
     * @var ICalendarSubscriptionService
     */
    protected $subscriptionService;

    /**
     * @var IPrivacyFilter
     */
    protected $privacyFilter;

    /**
     * @var IUserRepository
     */
    protected $userRepository;

    /**
     * @var SlotLabelFactory
     */
    protected $slotLabelFactory;

    public function __construct(ICommonCalendarPage $page,
                                ICalendarFactory $calendarFactory,
                                IReservationViewRepository $reservationRepository,
                                IScheduleRepository $scheduleRepository,
                                IUserRepository $userRepository,
                                IResourceService $resourceService,
                                ICalendarSubscriptionService $subscriptionService,
                                IPrivacyFilter $privacyFilter,
                                SlotLabelFactory $factory)
    {
        parent::__construct($page);
        $this->page = $page;
        $this->reservationRepository = $reservationRepository;
        $this->scheduleRepository = $scheduleRepository;
        $this->resourceService = $resourceService;
        $this->subscriptionService = $subscriptionService;
        $this->privacyFilter = $privacyFilter;
        $this->userRepository = $userRepository;
        $this->slotLabelFactory = $factory;
    }

    public function PageLoad($userSession)
    {
        $resources = $this->GetAllResources($userSession);

        $schedules = $this->scheduleRepository->GetAll();
        $selectedScheduleId = $this->page->GetScheduleId();
        $selectedResourceId = $this->page->GetResourceId();
        $selectedGroupId = $this->page->GetGroupId();

        // todo need a way to know if it's cleared vs first load
//        $user = $this->userRepository->LoadById($userSession->UserId);
//        $calendarPreference = UserCalendarFilter::Deserialize($user->GetPreference(UserPreferences::CALENDAR_FILTER));
//
//        if (empty($selectedScheduleId)) {
//            $selectedScheduleId = $calendarPreference->ScheduleId;
//        }
//        if (empty($selectedResourceId)) {
//            $selectedResourceId = $calendarPreference->ResourceId;
//        }
//        if (empty($selectedGroupId)) {
//            $selectedGroupId = $calendarPreference->GroupId;
//        }

        $selectedSchedule = $this->GetSelectedSchedule($schedules, $selectedScheduleId);
        if ($selectedSchedule->GetId() == 0) {
            $selectedScheduleId = null;
        }

        $resourceGroups = $this->resourceService->GetResourceGroups(null, $userSession);

        if (!empty($selectedGroupId)) {
            $tempResources = array();
            $resourceIds = $resourceGroups->GetResourceIds($selectedGroupId);
            $selectedGroup = $resourceGroups->GetGroup($selectedGroupId);
            $this->page->BindSelectedGroup($selectedGroup);

            /** @var ResourceDTO $resource */
            foreach ($resources as $resource) {
                if (in_array($resource->GetId(), $resourceIds)) {
                    $tempResources[] = $resource;
                }
            }

            $resources = $tempResources;
        }

        $this->BindSubscriptionDetails($userSession, $selectedResourceId, $selectedScheduleId);

        $this->page->BindFilters(new CalendarFilters($schedules, $resources, $selectedScheduleId, $selectedResourceId, $resourceGroups));

        $this->page->SetDisplayDate($this->GetStartDate());
        $this->page->SetScheduleId($selectedScheduleId);
        $this->page->SetResourceId($selectedResourceId);
        $this->page->SetGroupId($selectedGroupId);

        $this->page->SetFirstDay($selectedSchedule->GetWeekdayStart());

        $calendarType = $this->page->GetCalendarType();
        if (empty($calendarType))
        {
            $calendarType = ServiceLocator::GetServer()->GetCookie(CookieKeys::CALENDAR_VIEW);
        }
        $this->page->BindCalendarType($calendarType);
    }

    protected function BindCalendarEvents()
    {
        $userSession = ServiceLocator::GetServer()->GetUserSession();

        $selectedResourceId = $this->page->GetResourceId();
        $selectedScheduleId = $this->page->GetScheduleId();
        $selectedGroupId = $this->page->GetGroupId();
        $selectedUserId = $this->page->GetUserId();
        $selectedParticipantId = $this->page->GetParticipantId();

        if (!empty($selectedGroupId)) {
            $resourceGroups = $this->resourceService->GetResourceGroups($selectedScheduleId, $userSession);
            $selectedResourceId = $resourceGroups->GetResourceIds($selectedGroupId);
        }

        if ($userSession->UserId != 0) {
            $user = $this->userRepository->LoadById($userSession->UserId);
            $userCalendarFilter = new UserCalendarFilter($selectedResourceId, $selectedScheduleId, $selectedGroupId);
            $user->ChangePreference(UserPreferences::CALENDAR_FILTER, $userCalendarFilter->Serialize());
            $this->userRepository->Update($user);
        }
        $this->BindEvents($userSession, $selectedScheduleId, $selectedResourceId, $selectedUserId, $selectedParticipantId);
    }

    public function ProcessDataRequest($dataRequest)
    {
        if ($dataRequest == 'events') {
            $this->BindCalendarEvents();
        }
        else {
            $this->BindSubscriptionDetails(ServiceLocator::GetServer()->GetUserSession(), $this->page->GetResourceId(), $this->page->GetScheduleId());
            $this->page->RenderSubscriptionDetails();
        }
    }

    protected function GetAllResources($userSession)
    {
        $showInaccessible = Configuration::Instance()->GetSectionKey(ConfigSection::SCHEDULE, ConfigKeys::SCHEDULE_SHOW_INACCESSIBLE_RESOURCES, new BooleanConverter());
        return $this->resourceService->GetAllResources($showInaccessible, $userSession);
    }

    /**
     * @param array|Schedule[] $schedules
     * @param int $scheduleId
     * @return Schedule
     */
    protected function GetSelectedSchedule($schedules, $scheduleId)
    {
        if (empty($schedules)) {
            $schedules = $this->scheduleRepository->GetAll();
        }

        $default = new NullSchedule();

        /** @var $schedule Schedule */
        foreach ($schedules as $schedule) {
            if (!empty($scheduleId) && $schedule->GetId() == $scheduleId) {
                return $schedule;
            }
        }

        return $default;
    }

    /**
     * @return Date
     */
    protected function GetStartDate()
    {
        $timezone = ServiceLocator::GetServer()->GetUserSession()->Timezone;

        $startDate = $this->page->GetStartDate();

        if (empty($startDate)) {
            return Date::Now()->ToTimezone($timezone);
        }
        return Date::Parse($startDate, $timezone);
    }

    /**
     * @return Date
     */
    protected function GetEndDate()
    {
        $timezone = ServiceLocator::GetServer()->GetUserSession()->Timezone;

        $endDate = $this->page->GetEndDate();

        return Date::Parse($endDate, $timezone);
    }

    /**
     * @param UserSession $userSession
     * @param int $selectedScheduleId
     * @param int $selectedResourceId
     * @param int|null $selectedUserId
     * @param int|null $selectedParticipantId
     */
    protected abstract function BindEvents($userSession, $selectedScheduleId, $selectedResourceId, $selectedUserId, $selectedParticipantId);

    /**
     * @param UserSession $userSession
     * @param int $resourceId
     * @param int $scheduleId
     */
    protected abstract function BindSubscriptionDetails($userSession, $resourceId, $scheduleId);
}

class UserCalendarFilter
{
    public $ResourceId;
    public $ScheduleId;
    public $GroupId;

    public function __construct($resourceId, $scheduleId, $groupId)
    {

        $this->ResourceId = $resourceId;
        $this->ScheduleId = $scheduleId;
        $this->GroupId = $groupId;
    }

    /**
     * @return string
     */
    public function Serialize()
    {
        return "{$this->ResourceId}|{$this->ScheduleId}|{$this->GroupId}";
    }

    /**
     * @param string $string
     * @return UserCalendarFilter
     */
    public static function Deserialize($string)
    {
        $parts = explode('|', $string);
        $resourceId = isset($parts[0]) ? $parts[0] : null;
        $scheduleId = isset($parts[1]) ? $parts[1] : null;
        $groupId = isset($parts[2]) ? $parts[2] : null;
        return new UserCalendarFilter($resourceId, $scheduleId, $groupId);
    }
}