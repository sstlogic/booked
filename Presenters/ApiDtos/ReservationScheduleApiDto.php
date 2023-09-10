<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once ROOT_DIR . 'Presenters/ApiDtos/ApiHelperFunctions.php';

class ReservationScheduleApiDto {
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $name;
    /**
     * @var ScheduleSlotsApiDto|null
     */
    public $slots;
    /**
     * @var int|null
     */
    public $maximumResourcesPerReservation;
    /**
     * @var boolean
     */
    public $canEndInUnavailableSlots;
    /**
     * @var boolean
     */
    public $usesAppointments;
    /**
     * @var string|null
     */
    public $availableStartDate;
    /**
     * @var string|null
     */
    public $availableEndDate;
    /**
     * @var UpcomingAppointmentDto[]
     */
    public $upcomingAppointments = [];

    /**
     * @param Schedule $schedule
     * @param IScheduleLayout $layout
     * @return ReservationScheduleApiDto
     */
    public static function FromSchedule(Schedule $schedule, IScheduleLayout $layout): ReservationScheduleApiDto
    {
        $dto = new ReservationScheduleApiDto();
        $dto->id = intval($schedule->GetId());
        $dto->name = apidecode($schedule->GetName());
        $dto->availableStartDate = $schedule->HasAvailability() ? $schedule->GetAvailabilityBegin()->ToSystem() : null;
        $dto->availableEndDate = $schedule->HasAvailability() ? $schedule->GetAvailabilityEnd()->ToSystem() : null;
        $dto->usesAppointments = $schedule->HasCustomLayout();
        $dto->canEndInUnavailableSlots = $schedule->GetAllowBlockedEndSlot();
        $dto->maximumResourcesPerReservation = empty($schedule->GetMaxResourcesPerReservation()) ? null : intval($schedule->GetMaxResourcesPerReservation());
        $dto->slots = ScheduleSlotsApiDto::FromLayout($layout);
        return $dto;
    }

}