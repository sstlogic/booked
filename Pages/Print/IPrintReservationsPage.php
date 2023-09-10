<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once ROOT_DIR . 'Pages/ActionPage.php';

interface IPrintReservationsPage extends IActionPage
{
    /**
     * @param ResourceDto[] $resources
     */
    public function BindResources($resources);

    public function SetDate(Date $date);

    /**
     * @param int $start
     * @param int $end
     */
    public function SetVisibleHours($start, $end);

    /**
     * @param ReservationListItem[] $reservations
     * @return void
     */
    public function SetReservations(array $reservations);

    /**
     * @return string
     */
    public function GetResourceId();

    /**
     * @return string
     */
    public function GetScheduleId();

    /**
     * @return string
     */
    public function GetDate();

    /**
     * @param bool $noResources
     */
    public function SetNoResources($noResources);

    /**
     * @param ResourceDto[] $selectedResources
     * @param int[] $ids
     */
    public function SetResources($selectedResources, $ids);

    /**
     * @param Schedule[] $schedules
     * @param Schedule $selectedSchedule
     */
    public function SetSchedules($schedules, $selectedSchedule);
}