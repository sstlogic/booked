<?php
/**
 * Copyright 2018-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/Reservation/ReservationUserAvailabilityPresenter.php');

interface IReservationUserAvailabilityPage
{

    /**
     * @return int[]
     */
    public function GetResourceIds();

    /**
     * @return int[]
     */
    public function GetInviteeIds();

    /**
     * @return int[]
     */
    public function GetParticipantIds();

    /**
     * @return int
     */
    public function GetScheduleId();

    /**
     * @return int
     */
    public function GetOwnerId();

    /**
     * @param DailyLayout $dailyLayout
     * @param BookableResource[] $resources
     * @param UserDto $user
     * @param UserDto[] $participants
     * @param UserDto[] $invitees
     * @param DateRange $dateRange
     */
    public function Bind($dailyLayout, $resources, $user, $participants, $invitees, $dateRange);

    /**
     * @return string
     */
    public function GetStartDate();

    /**
     * @return string
     */
    public function GetStartTime();

    /**
     * @return string
     */
    public function GetEndDate();

    /**
     * @return string
     */
    public function GetEndTime();

    /**
     * @param string $timezone
     * @return DateRange|null
     */
    public function GetDuration(string $timezone);
}

class ReservationUserAvailabilityPage extends Page implements IReservationUserAvailabilityPage
{
    /**
     * @var ReservationUserAvailabilityPresenter
     */
    private $presenter;
    /**
     * @var object|null
     */
    private $json;


    public function __construct()
    {
        parent::__construct('', 1);
        $this->presenter = new ReservationUserAvailabilityPresenter(
            $this,
            new ReservationViewRepository(),
            new ScheduleRepository(),
            new UserRepository(),
            new ResourceRepository());
    }

    /**
     * @return object|null
     */
    protected function GetJsonPost()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contents = file_get_contents("php://input");
            if (empty($contents)) {
                return null;
            }
            return json_decode($contents);
        }

        return null;
    }

    public function PageLoad()
    {
        if (!$this->server->GetUserSession()->IsAdmin && Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_HIDE_USER_DETAILS, new BooleanConverter())) {
            return;
        }

        $this->json = $this->GetJsonPost();

        $this->Set('DisplaySlotFactory', new StaticDisplaySlotFactory());
        $this->presenter->PageLoad($this->server->GetUserSession());
    }

    public function GetResourceIds()
    {
        if (!empty($this->json)) {
            return $this->json->rid;
        }
        return $this->GetQuerystring(QueryStringKeys::RESOURCE_ID, true);
    }

    public function GetInviteeIds()
    {
        if (!empty($this->json)) {
            return $this->json->invitees;
        }
        return $this->GetQuerystring(QueryStringKeys::INVITEE_ID, true);
    }

    public function GetParticipantIds()
    {
        if (!empty($this->json)) {
            return $this->json->participants;
        }
        return $this->GetQuerystring(QueryStringKeys::PARTICIPANT_ID, true);
    }

    public function GetScheduleId()
    {
        if (!empty($this->json)) {
            return intval($this->json->sid);
        }
        return $this->GetQuerystring(QueryStringKeys::SCHEDULE_ID);
    }

    public function GetOwnerId()
    {
        if (!empty($this->json)) {
            return intval($this->json->ownerId);
        }
        return $this->server->GetUserSession()->UserId;
    }

    public function Bind($dailyLayout, $resources, $user, $participants, $invitees, $dateRange)
    {
        $this->Set('DailyLayout', $dailyLayout);
        $this->Set('BoundDates', $dateRange->Dates());
        $this->Set('Resources', $resources);
        $this->Set('User', $user);
        $this->Set('Participants', $participants);
        $this->Set('Invitees', $invitees);
        $this->Display('Reservation/availability.tpl');
    }

    public function GetStartDate()
    {
        return $this->GetQuerystring(QueryStringKeys::START_DATE);
    }

    public function GetStartTime()
    {
        return $this->GetQuerystring(QueryStringKeys::START_TIME);
    }

    public function GetEndDate()
    {
        return $this->GetQuerystring(QueryStringKeys::END_DATE);
    }

    public function GetEndTime()
    {
        return $this->GetQuerystring(QueryStringKeys::END_TIME);
    }

    public function GetDuration(string $timezone)
    {
        if (!empty($this->json)) {
            $start = Date::ParseExact($this->json->start)->ToTimezone($timezone);
            $end = Date::ParseExact($this->json->end)->ToTimezone($timezone);
            return new DateRange($start, $end);
        }
        return null;
    }
}
