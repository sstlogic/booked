<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/SecurePage.php');
require_once(ROOT_DIR . 'Presenters/Admin/ManageReservationWaitlistPresenter.php');
require_once(ROOT_DIR . 'Domain/namespace.php');
require_once(ROOT_DIR . 'Presenters/ApiDtos/UserApiDto.php');

interface IManageReservationWaitlistPage extends IActionPage
{
    /**
     * @param UserDto[] $users
     */
    public function BindUsers($users);

    /**
     * @param Schedule[] $schedules
     */
    public function BindSchedules($schedules);

    /**
     * @param BookableResource[] $resources
     */
    public function BindResources($resources);

    /**
     * @param ReservationWaitlistRequest[] W$requests
     */
    public function BindWaitlist($requests);

    /**
     * @return string
     */
    public function GetFilterScheduleId();

    /**
     * @return string[]
     */
    public function GetFilterResourceIds();

    /**
     * @return string
     */
    public function GetFilterUserId();

    /**
     * @return string
     */
    public function GetFilterStartDate();

    /**
     * @return string
     */
    public function GetFilterEndDate();

    /**
     * @return int
     */
    public function GetWaitlistId();
}

class ManageReservationWaitlistPage extends ActionPage implements IManageReservationWaitlistPage, IPageWithId
{
    private ManageReservationWaitlistPresenter $presenter;

    public function __construct()
    {
        parent::__construct('WaitlistRequests', 1);
        $user = $this->server->GetUserSession();

        $userRepository = new UserRepository();
        $scheduleRepository = new ScheduleRepository();
        $resourceRepository = new ResourceRepository();

        if ($user->IsScheduleAdmin) {
            $scheduleRepository = new ScheduleAdminScheduleRepository($userRepository, $user);
            $resourceRepository = new ResourceAdminResourceRepository($userRepository, $user);
        } else if ($user->IsResourceAdmin) {
            $resourceRepository = new ResourceAdminResourceRepository($userRepository, $user);
        } else if ($user->IsGroupAdmin) {
            $userRepository = new GroupAdminUserRepository(new GroupRepository(), $user);
        }

        $this->presenter = new ManageReservationWaitlistPresenter($this, $userRepository, $scheduleRepository, $resourceRepository, new ReservationWaitlistRepository());
        $this->Set('PageId', $this->GetPageId());
    }

    public function ProcessAction()
    {
        $this->presenter->ProcessAction();
    }

    public function ProcessDataRequest($dataRequest)
    {
    }

    public function ProcessPageLoad()
    {
        $tz = ServiceLocator::GetServer()->GetUserSession()->Timezone;
        $this->presenter->PageLoad();
        $this->Set('StartDate', Date::Now()->ToTimezone($tz));
        $this->Set('EndDate', Date::Now()->AddDays(14)->ToTimezone($tz));
        $this->Display('Admin/Reservations/manage_reservation_waitlist.tpl');
    }

    public function BindUsers($users)
    {
        $this->Set('Users', array_map('UserApiDto::FromUserDto', $users));
    }

    public function BindSchedules($schedules)
    {
        $this->Set('Schedules', $schedules);
    }

    public function BindResources($resources)
    {
        $this->Set('Resources', $resources);
    }

    public function BindWaitlist($requests)
    {
        $this->Set('Requests', $requests);
        $this->Display('Admin/Reservations/waitlist_request_results.tpl');
    }

    public function GetFilterScheduleId()
    {
        return $this->GetForm(FormKeys::SCHEDULE_ID);
    }

    public function GetFilterResourceIds()
    {
        $ids = $this->GetForm(FormKeys::RESOURCE_ID);
        if (empty($ids)) {
            return [];
        }
        if (!is_array($ids)) {
            return [$ids];
        }
        return $ids;
    }

    public function GetFilterUserId()
    {
        return $this->GetForm(FormKeys::USER_ID);
    }

    public function GetFilterStartDate()
    {
        return $this->GetForm(FormKeys::BEGIN_DATE);
    }

    public function GetFilterEndDate()
    {
        return $this->GetForm(FormKeys::END_DATE);
    }

    public function GetWaitlistId()
    {
        return intval($this->GetForm(FormKeys::WAITLIST_REQUEST_ID));
    }

    public function GetPageId(): int
    {
        return AdminPageIds::ReservationWaitlist;
    }
}