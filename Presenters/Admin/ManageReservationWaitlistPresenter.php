<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'Pages/Admin/ManageReservationWaitlistPage.php');

class ManageReservationWaitlistPresenter extends ActionPresenter
{
    /**
     * @var IManageReservationWaitlistPage
     */
    private $page;
    /**
     * @var IUserRepository
     */
    private $userRepository;
    /**
     * @var IScheduleRepository
     */
    private $scheduleRepository;
    /**
     * @var IResourceRepository
     */
    private $resourceRepository;
    /**
     * @var IReservationWaitlistRepository
     */
    private $reservationWaitlistRepository;

    public function __construct(IManageReservationWaitlistPage $page, IUserRepository $userRepository, IScheduleRepository $scheduleRepository, IResourceRepository $resourceRepository, IReservationWaitlistRepository $reservationWaitlistRepository)
    {
        parent::__construct($page);
        $this->page = $page;
        $this->userRepository = $userRepository;
        $this->scheduleRepository = $scheduleRepository;
        $this->resourceRepository = $resourceRepository;
        $this->reservationWaitlistRepository = $reservationWaitlistRepository;

        $this->AddAction('filter', 'FilterWaitlist');
        $this->AddAction('delete', 'Delete');
    }

    public function PageLoad()
    {
        $users = $this->userRepository->GetAll();
        $schedules = $this->scheduleRepository->GetAll();
        $resources = $this->resourceRepository->GetResourceList();

        $this->page->BindUsers($users);
        $this->page->BindSchedules($schedules);
        $this->page->BindResources($resources);
    }

    public function FilterWaitlist() {
        $timezone = ServiceLocator::GetServer()->GetUserSession()->Timezone;
        $start = $this->page->GetFilterStartDate();
        $end = $this->page->GetFilterEndDate();
        if (empty($start)) {
            $start = Date::Now()->ToTimezone($timezone);
        }
        else {
            $start = Date::Parse($start, $timezone);
        }
        if (empty($end)) {
            $end = Date::Now()->AddDays(14)->ToTimezone($timezone);
        }
        else {
            $end = Date::Parse($end, $timezone);
        }
        $scheduleId = $this->page->GetFilterScheduleId();
        $resourceIds = $this->page->GetFilterResourceIds();
        $userId = $this->page->GetFilterUserId();

        $scheduleId = empty($scheduleId) ? -1 : intval($scheduleId);
        $resourceIds = array_map('intval', $resourceIds);
        $userId = empty($userId) ? -1 : intval($userId);

        $requests = $this->reservationWaitlistRepository->Search($userId, $resourceIds, $scheduleId, new DateRange($start, $end));
        $this->page->BindWaitlist($requests);
    }

    public function Delete() {
        $id = $this->page->GetWaitlistId();

        Log::Debug('Deleting waitlist request.', ['id' => $id]);
        $request = $this->reservationWaitlistRepository->LoadById($id);
        $this->reservationWaitlistRepository->Delete($request);
    }
}