<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

@define('ROOT_DIR', dirname(__FILE__) . '/../');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'Jobs/JobCop.php');
require_once(ROOT_DIR . 'Jobs/JobServer.php');
require_once(ROOT_DIR . 'Jobs/BookedJob.php');
require_once(ROOT_DIR . 'lib/Email/namespace.php');
require_once(ROOT_DIR . 'lib/Email/Messages/ReservationEndTimeExtendedEmail.php');

class AutoExtendJob extends BookedJob
{
    public function __construct()
    {
        parent::__construct('auto-extend', 1);
    }

    protected function Execute()
    {
        $fiveMinAgo = Date::Now()->SubtractMinutes(5);
        $now = Date::Now();
        $alreadySeen = array();

        $missedCheckOutSql = 'SELECT `ri`.`reservation_instance_id`, `r`.`resource_id`, `r`.`name`, `ri`.`start_date`, `ri`.`end_date`, `ri`.`reference_number`,
       `u`.`fname`, `u`.`lname`, `u`.`timezone`, `u`.`language`, `u`.`email`, `u`.`date_format`, `u`.`time_format`
    FROM `reservation_instances` `ri` 
    INNER JOIN `reservation_series` `rs` ON `rs`.`series_id` = `ri`.`series_id`
    INNER JOIN `reservation_resources` `rr` ON `rs`.`series_id` = `rr`.`series_id`
    INNER JOIN `resources` `r` ON `r`.`resource_id` = `rr`.`resource_id`
    INNER JOIN `users` `u` ON `u`.`user_id` = `rs`.`owner_id`
    WHERE `r`.`enable_check_in` = 1 
      AND `r`.`auto_extend_reservations` = 1
      AND `ri`.`checkout_date` IS NULL 
      AND `ri`.`end_date` >= @five_minutes_ago 
      AND `ri`.`end_date` <= @now
      AND `rs`.`status_id` <> 2';

        $missedCheckoutCommand = new AdHocCommand($missedCheckOutSql);
        $missedCheckoutCommand->AddParameter(new Parameter('@five_minutes_ago', $fiveMinAgo->ToDatabase()));
        $missedCheckoutCommand->AddParameter(new Parameter('@now', $now->ToDatabase()));
        $missedCheckoutReader = ServiceLocator::GetDatabase()->Query($missedCheckoutCommand);

        Log::Debug("Auto extending reservations", ['count' => $missedCheckoutReader->NumRows()]);

        while ($missedCheckoutRow = $missedCheckoutReader->GetRow()) {
            $currentEndDate = Date::FromDatabase($missedCheckoutRow[ColumnNames::RESERVATION_END]);
            $reservationInstanceId = $missedCheckoutRow[ColumnNames::RESERVATION_INSTANCE_ID];

            if (array_key_exists($reservationInstanceId, $alreadySeen)) {
                continue;
            }
            $alreadySeen[$reservationInstanceId] = 1;

            $nextReservationSql = 'SELECT `ri`.`start_date` 
    FROM `reservation_instances` `ri` 
    INNER JOIN `reservation_series` `rs` ON `rs`.`series_id` = `ri`.`series_id`
    INNER JOIN `reservation_resources` `rr` ON `rs`.`series_id` = `rr`.`series_id`
    WHERE `ri`.`start_date` > @endDate AND `rr`.`resource_id` = @resourceid
    ORDER BY `ri`.`start_date` ASC
    LIMIT 1';
            $nextReservationCommand = new AdHocCommand($nextReservationSql);
            $nextReservationCommand->AddParameter(new Parameter(ParameterNames::END_DATE, $currentEndDate->ToDatabase()));
            $nextReservationCommand->AddParameter(new Parameter(ParameterNames::RESOURCE_ID, $missedCheckoutRow[ColumnNames::RESOURCE_ID]));

            $nextReservationReader = ServiceLocator::GetDatabase()->Query($nextReservationCommand);
            if ($nextReservationRow = $nextReservationReader->GetRow()) {
                $newEndDate = Date::FromDatabase($nextReservationRow[ColumnNames::RESERVATION_START]);
            } else {
                $newEndDate = $currentEndDate->AddWeeks(1);
            }

            $updateEndDateCommand = new AdHocCommand('UPDATE `reservation_instances` SET `end_date` = @endDate WHERE `reservation_instance_id` = @reservationid');
            $updateEndDateCommand->AddParameter(new Parameter(ParameterNames::END_DATE, $newEndDate->ToDatabase()));
            $updateEndDateCommand->AddParameter(new Parameter(ParameterNames::RESERVATION_INSTANCE_ID, $reservationInstanceId));

            ServiceLocator::GetDatabase()->Execute($updateEndDateCommand);

            $this->SendEmail($missedCheckoutRow, $newEndDate);

            Log::Debug('Extended reservation end date', ['instanceId' => $reservationInstanceId, 'currentEnd' => $currentEndDate, 'newEnd' => $newEndDate]);
        }

        $missedCheckoutReader->Free();
    }

    private function SendEmail($row, $newEndDate)
    {
        $fname = $row[ColumnNames::FIRST_NAME];
        $lname = $row[ColumnNames::LAST_NAME];
        $email = $row[ColumnNames::EMAIL];
        $referenceNumber = $row[ColumnNames::REFERENCE_NUMBER];
        $resourceName = $row[ColumnNames::RESOURCE_NAME];
        $timezone = $row[ColumnNames::TIMEZONE_NAME];
        $language = $row[ColumnNames::LANGUAGE_CODE];
        $originalStartDate = Date::FromDatabase($row[ColumnNames::RESERVATION_START]);
        $originalEndDate = Date::FromDatabase($row[ColumnNames::RESERVATION_END]);
        $dateFormat = $row[ColumnNames::DATE_FORMAT];
        $timeFormat = $row[ColumnNames::TIME_FORMAT];

        ServiceLocator::GetEmailService()->Send(new ReservationEndTimeExtendedEmail($email, $fname, $lname, $language, $timezone, $referenceNumber, $resourceName, $originalStartDate, $originalEndDate, $newEndDate, $dateFormat, $timeFormat));
    }
}

$autoExtendJob = new AutoExtendJob();
$autoExtendJob->Run();