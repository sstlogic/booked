<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ReservationSchedulerLoader
{
    /**
     * @var IReservationApiPage
     */
    private $page;
    /**
     * @var IScheduleRepository
     */
    private $scheduleRepository;
    /**
     * @var IResourceService
     */
    private $resourceService;
    /**
     * @var ReservationView|null
     */
    private $reservationView;

    public function __construct(IReservationApiPage $page, IScheduleRepository $scheduleRepository, IResourceService $resourceService, ?ReservationView $reservationView = null)
    {
        $this->page = $page;
        $this->scheduleRepository = $scheduleRepository;
        $this->resourceService = $resourceService;
        $this->reservationView = $reservationView;
    }

    /**
     * @param string $timezone
     * @return LoadedSchedule
     */
    public function Load($timezone): LoadedSchedule
    {
        $schedule = null;
        $resourceIds = $this->page->GetResourceIds();
        $resourceId = !empty($resourceIds) ? $resourceIds[0] : null;
        $publicId = $this->page->GetResourcePublicId();

        if (!empty($this->reservationView)) {
            $scheduleId = $this->reservationView->ScheduleId;
            $schedule = $this->scheduleRepository->LoadById($scheduleId);
        } elseif (!empty($this->page->GetScheduleId())) {
            $scheduleId = $this->page->GetScheduleId();
            $schedule = $this->scheduleRepository->LoadById($scheduleId);
        } elseif (!empty($resourceId) || !empty($publicId)) {
            $resource = $this->resourceService->GetResource(!empty($resourceId) ? $resourceId : $publicId);
            $scheduleId = $resource->GetScheduleId();
            $schedule = $this->scheduleRepository->LoadById($scheduleId);
        }

        if (empty($schedule)) {
            $schedule = $this->scheduleRepository->LoadDefaultSchedule();
        }

        $layout = $this->scheduleRepository->GetLayout($schedule->GetId(), new ScheduleLayoutFactory($timezone));
        return new LoadedSchedule($schedule, $layout);
    }
}


class LoadedSchedule
{
    /**
     * @var Schedule
     */
    public $schedule;

    /**
     * @var IScheduleLayout
     */
    public $layout;

    public function __construct(Schedule $schedule, IScheduleLayout $layout)
    {
        $this->schedule = $schedule;
        $this->layout = $layout;
    }
}