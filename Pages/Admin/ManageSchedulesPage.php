<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/IPageable.php');
require_once(ROOT_DIR . 'Pages/Admin/AdminPage.php');
require_once(ROOT_DIR . 'Presenters/Admin/ManageSchedulesPresenter.php');
require_once(ROOT_DIR . 'Domain/Access/ScheduleRepository.php');

interface IUpdateSchedulePage
{
    /**
     * @return int
     */
    public function GetScheduleId();

    /**
     * @return string
     */
    public function GetScheduleName();

    /**
     * @return string
     */
    public function GetStartDay();

    /**
     * @return string
     */
    public function GetDaysVisible();

    /**
     * @return string
     */
    public function GetReservableSlots();

    /**
     * @return string
     */
    public function GetBlockedSlots();

    /**
     * @return string[]
     */
    public function GetDailyReservableSlots();

    /**
     * @return string[]
     */
    public function GetDailyBlockedSlots();

    /**
     * @return string
     */
    public function GetLayoutTimezone();

    /**
     * @return bool
     */
    public function GetUsingSingleLayout();

    /**
     * @return int
     */
    public function GetSourceScheduleId();

    /**
     * @return int
     */
    public function GetTargetScheduleId();

    /**
     * @return string
     */
    public function GetValue();

    /**
     * @return int
     */
    public function GetMaximumConcurrentReservations();

    /**
     * @return bool
     */
    public function GetIsUnlimitedConcurrentReservations();

    /**
     * @return int
     */
    public function GetMaximumResourcesPerReservation();

    /**
     * @return bool
     */
    public function GetIsUnlimitedMaximumResourcesPerReservation();
}

interface IManageSchedulesPage extends IUpdateSchedulePage, IActionPage
{
    /**
     * @param Schedule[] $schedules
     * @param array|IScheduleLayout[] $layouts
     * @param Schedule[] $sourceSchedules
     */
    public function BindSchedules($schedules, $layouts, $sourceSchedules);

    /**
     * @param GroupItemView[] $groups
     */
    public function BindGroups($groups);

    public function SetTimezones($timezoneValues, $timezoneOutput);

    /**
     * @return int
     */
    public function GetAdminGroupId();

    /**
     * @return int[]
     */
    public function GetPeakWeekdays();

    /**
     * @return bool
     */
    public function GetPeakAllDay();

    /**
     * @return bool
     */
    public function GetPeakEveryDay();

    /**
     * @return bool
     */
    public function GetPeakAllYear();

    /**
     * @return string
     */
    public function GetPeakBeginTime();

    /**
     * @return string
     */
    public function GetPeakEndTime();

    /**
     * @return int
     */
    public function GetPeakBeginDay();

    /**
     * @return int
     */
    public function GetPeakBeginMonth();

    /**
     * @return int
     */
    public function GetPeakEndDay();

    /**
     * @return int
     */
    public function GetPeakEndDMonth();

    public function DisplayPeakTimes(IScheduleLayout $layout);

    /**
     * @return bool
     */
    public function GetDeletePeakTimes();

    /**
     * @param BookableResource[] $resources
     */
    public function BindResources($resources);

    /**
     * @return bool
     */
    public function GetAvailableAllYear();

    /**
     * @return string
     */
    public function GetAvailabilityBegin();

    /**
     * @return string
     */
    public function GetAvailabilityEnd();

    /**
     * @param Schedule $schedule
     * @param string $timezone
     */
    public function DisplayAvailability($schedule, $timezone);

    /**
     * @return int
     */
    public function GetLayoutType();

    /**
     * @return string
     */
    public function GetLayoutStart();

    /**
     * @return string
     */
    public function GetLayoutEnd();

    /**
     * @param array $events
     */
    public function BindEvents($events);

    /**
     * @return string
     */
    public function GetSlotStart();

    /**
     * @return string
     */
    public function GetSlotEnd();

    /**
     * @return string
     */
    public function GetCustomLayoutStartRange();

    /**
     * @return string
     */
    public function GetCustomLayoutEndRange();

    /**
     * @return string
     */
    public function GetSlotId();

    /**
     * @return int
     */
    public function GetDefaultStyle();

    /**
     * @return bool
     */
    public function GetReservationsCanEndInBlockedSlots();
}

class ManageSchedulesPage extends ActionPage implements IManageSchedulesPage, IPageWithId
{
    protected ManageSchedulesPresenter $presenter;

    public function __construct()
    {
        parent::__construct('ManageSchedules', 1);

        $this->presenter = new ManageSchedulesPresenter($this, new ManageScheduleService(new ScheduleRepository(), new ResourceRepository()),
            new GroupRepository());

        $this->Set('CreditsEnabled', Configuration::Instance()->GetSectionKey(ConfigSection::CREDITS, ConfigKeys::CREDITS_ENABLED, new BooleanConverter()));
        $this->Set('PageId', $this->GetPageId());
    }

    public function ProcessPageLoad()
    {
        $this->presenter->PageLoad();

        $resources = Resources::GetInstance();
        $this->Set('DayNames', $resources->GetDays('full'));
        $this->Set('DayNamesAbbr', Resources::GetInstance()->GetDays('abbr'));
        $this->Set('Today', Resources::GetInstance()->GetString('Today'));
        $this->Set('TimeFormat', Resources::GetInstance()->GetDateFormat('timepicker_js'));
        $this->Set('DefaultDate', Date::Now()->SetTimeString('08:00'));
        $this->Set('Months', Resources::GetInstance()->GetMonths('full'));
        $this->Set('DayList', range(1, 31));
        $this->Set('StyleNames', array(
            ScheduleStyle::Standard => $resources->GetString('StandardView'),
            ScheduleStyle::Wide => $resources->GetString('WideView'),
            ScheduleStyle::Tall => $resources->GetString('TallView'),
            ScheduleStyle::CondensedWeek => $resources->GetString('WeekView'),
        ));
        $this->Display('Admin/Schedules/manage_schedules.tpl');
    }

    public function DisplayPeakTimes(IScheduleLayout $layout)
    {
        $this->Set('Layout', $layout);
        $this->Set('Months', Resources::GetInstance()->GetMonths('full'));
        $this->Set('DayNamesAbbr', Resources::GetInstance()->GetDays('abbr'));
        $this->Display('Admin/Schedules/manage_peak_times.tpl');
    }

    public function ProcessAction()
    {
        $this->presenter->ProcessAction();
    }

    public function SetTimezones($timezoneValues, $timezoneOutput)
    {
        $this->Set('TimezoneValues', $timezoneValues);
        $this->Set('TimezoneOutput', $timezoneOutput);
    }

    public function BindSchedules($schedules, $layouts, $sourceSchedules)
    {
        $this->Set('Schedules', $schedules);
        $this->Set('Layouts', $layouts);
        $this->Set('SourceSchedules', $sourceSchedules);
    }

    public function GetScheduleId()
    {
        $id = $this->GetQuerystring(QueryStringKeys::SCHEDULE_ID);
        if (empty($id)) {
            $id = $this->GetForm(FormKeys::PK);
        }

        return $id;
    }

    public function GetScheduleName()
    {
        return $this->server->GetForm(FormKeys::SCHEDULE_NAME);
    }

    function GetStartDay()
    {
        return $this->server->GetForm(FormKeys::SCHEDULE_WEEKDAY_START);
    }

    function GetDaysVisible()
    {
        return $this->server->GetForm(FormKeys::SCHEDULE_DAYS_VISIBLE);
    }

    public function GetReservableSlots()
    {
        return $this->server->GetForm(FormKeys::SLOTS_RESERVABLE);
    }

    public function GetBlockedSlots()
    {
        return $this->server->GetForm(FormKeys::SLOTS_BLOCKED);
    }

    public function GetDailyReservableSlots()
    {
        $slots = array();
        foreach (DayOfWeek::Days() as $day) {
            $slots[$day] = $this->server->GetForm(FormKeys::SLOTS_RESERVABLE . "_$day");
        }
        return $slots;
    }

    public function GetDailyBlockedSlots()
    {
        $slots = array();
        foreach (DayOfWeek::Days() as $day) {
            $slots[$day] = $this->server->GetForm(FormKeys::SLOTS_BLOCKED . "_$day");
        }
        return $slots;
    }

    public function GetUsingSingleLayout()
    {
        $singleLayout = $this->server->GetForm(FormKeys::USING_SINGLE_LAYOUT);

        return !empty($singleLayout);
    }

    public function GetLayoutTimezone()
    {
        return $this->server->GetForm(FormKeys::TIMEZONE);
    }

    public function GetSourceScheduleId()
    {
        return $this->server->GetForm(FormKeys::SCHEDULE_ID);
    }

    public function GetTargetScheduleId()
    {
        return $this->server->GetForm(FormKeys::SCHEDULE_ID);
    }

    public function ProcessDataRequest($dataRequest)
    {
        $this->presenter->ProcessDataRequest($dataRequest);
    }

    /**
     * @param GroupItemView[] $groups
     */
    public function BindGroups($groups)
    {
        $this->Set('AdminGroups', $groups);
        $groupLookup = array();
        foreach ($groups as $group) {
            $groupLookup[$group->Id] = $group;
        }
        $this->Set('GroupLookup', $groupLookup);
    }

    /**
     * @return int
     */
    public function GetAdminGroupId()
    {
        return $this->server->GetForm(FormKeys::SCHEDULE_ADMIN_GROUP_ID);
    }

    public function GetValue()
    {
        return $this->GetForm(FormKeys::VALUE);
    }

    public function GetPeakWeekdays()
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

    public function GetPeakAllDay()
    {
        $allDay = $this->GetForm(FormKeys::PEAK_ALL_DAY);
        return !empty($allDay);
    }

    public function GetPeakEveryDay()
    {
        $everyDay = $this->GetForm(FormKeys::PEAK_EVERY_DAY);
        return !empty($everyDay);
    }

    public function GetPeakAllYear()
    {
        $allYear = $this->GetForm(FormKeys::PEAK_ALL_YEAR);
        return !empty($allYear);
    }

    public function GetPeakBeginTime()
    {
        return $this->GetForm(FormKeys::BEGIN_TIME);
    }

    public function GetPeakEndTime()
    {
        return $this->GetForm(FormKeys::END_TIME);
    }

    public function GetPeakBeginDay()
    {
        return $this->GetForm(FormKeys::PEAK_BEGIN_DAY);
    }

    public function GetPeakBeginMonth()
    {
        return $this->GetForm(FormKeys::PEAK_BEGIN_MONTH);
    }

    public function GetPeakEndDay()
    {
        return $this->GetForm(FormKeys::PEAK_END_DAY);
    }

    public function GetPeakEndDMonth()
    {
        return $this->GetForm(FormKeys::PEAK_END_MONTH);
    }

    public function GetDeletePeakTimes()
    {
        $delete = $this->GetForm(FormKeys::PEAK_DELETE);
        return $delete == '1';
    }

    public function BindResources($resources)
    {
        $this->Set('Resources', $resources);
    }

    public function GetAvailableAllYear()
    {
        return $this->GetCheckbox(FormKeys::AVAILABLE_ALL_YEAR);
    }

    public function GetAvailabilityBegin()
    {
        return $this->GetForm(FormKeys::AVAILABLE_BEGIN_DATE);
    }

    public function GetAvailabilityEnd()
    {
        return $this->GetForm(FormKeys::AVAILABLE_END_DATE);
    }

    public function DisplayAvailability($schedule, $timezone)
    {
        $this->Set('schedule', $schedule);
        $this->Set('timezone', $timezone);
        $this->Display('Admin/Schedules/manage_availability.tpl');
    }

    public function GetLayoutType()
    {
        return $this->GetForm(FormKeys::LAYOUT_TYPE);
    }

    public function GetLayoutStart()
    {
        return $this->GetQuerystring(QueryStringKeys::START_DATE);
    }

    public function GetLayoutEnd()
    {
        return $this->GetQuerystring(QueryStringKeys::END_DATE);
    }

    public function GetSlotStart()
    {
        return $this->GetForm(FormKeys::BEGIN_DATE);
    }

    public function GetSlotEnd()
    {
        return $this->GetForm(FormKeys::END_DATE);
    }

    public function BindEvents($events)
    {
        $this->SetJson($events);
    }

    public function GetCustomLayoutStartRange()
    {
        return $this->GetQuerystring(QueryStringKeys::START);
    }

    public function GetCustomLayoutEndRange()
    {
        return $this->GetQuerystring(QueryStringKeys::END);
    }

    public function GetSlotId()
    {
        return $this->GetForm(FormKeys::LAYOUT_PERIOD_ID);
    }

    public function GetDefaultStyle()
    {
        return $this->GetForm(FormKeys::SCHEDULE_DEFAULT_STYLE);
    }

    public function GetMaximumConcurrentReservations()
    {
        return intval($this->GetForm(FormKeys::MAXIMUM_CONCURRENT_RESERVATIONS));
    }

    public function GetIsUnlimitedConcurrentReservations()
    {
        return $this->GetCheckbox(FormKeys::MAXIMUM_CONCURRENT_UNLIMITED);
    }

    public function GetMaximumResourcesPerReservation()
    {
        return intval($this->GetForm(FormKeys::MAXIMUM_RESOURCES_PER_RESERVATION));
    }

    public function GetIsUnlimitedMaximumResourcesPerReservation()
    {
        return $this->GetCheckbox(FormKeys::MAXIMUM_RESOURCES_PER_RESERVATION_UNLIMITED);
    }

    public function GetReservationsCanEndInBlockedSlots()
    {
        $val = $this->GetForm(FormKeys::VALUE);
        return $val != "0";
    }

    public function GetPageId(): int
    {
        return AdminPageIds::Schedules;
    }
}