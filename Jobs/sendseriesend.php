<?php
/**
 * Copyright 2019-2023 Twinkle Toes Software, LLC
 */

@define('ROOT_DIR', dirname(__FILE__) . '/../');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'Jobs/JobCop.php');
require_once(ROOT_DIR . 'Jobs/JobServer.php');
require_once(ROOT_DIR . 'Jobs/BookedJob.php');
require_once(ROOT_DIR . 'lib/Email/Messages/ReservationSeriesEndingEmail.php');
require_once(ROOT_DIR . 'lib/SMS/namespace.php');
require_once(ROOT_DIR . 'lib/SMS/Messages/SeriesEndingSms.php');

class SendSeriesEndJob extends BookedJob
{
    public function __construct()
    {
        parent::__construct('send-series-end', 1440);
    }

    protected function Execute()
    {
        $sms = new SmsService();

        $emailEnabled = Configuration::Instance()->GetKey(ConfigKeys::ENABLE_EMAIL, new BooleanConverter());
        $smsEnabled = $sms->IsEnabled();

        $remindersEnabled = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_REMINDERS_ENABLED, new BooleanConverter());
        if (!$remindersEnabled || (!$emailEnabled && !$smsEnabled)) {
            return;
        }

        $reservationRepository = new ReservationRepository();
        $userRepository = new UserRepository();

        $instancesEnding = $this->BuildQuery();

        $reader = ServiceLocator::GetDatabase()->Query($instancesEnding);
        Log::Debug('Sending series ending emails', ['count' => $reader->NumRows()]);

        $seenReservations = [];

        while ($row = $reader->GetRow()) {
            $referenceNumber = $row[ColumnNames::REFERENCE_NUMBER];

            if (in_array($row[ColumnNames::REFERENCE_NUMBER], $seenReservations)) {
                continue;
            }

            $seenReservations[] = $row[ColumnNames::REFERENCE_NUMBER];

            $reservation = $reservationRepository->LoadByReferenceNumber($referenceNumber);

            if (!empty($reservation)) {
                Log::Debug('Sending series ending email', ['referenceNumber' => $referenceNumber, 'userId' => $reservation->UserId()]);

                $user = $userRepository->LoadById($reservation->UserId());

                if ($emailEnabled && $user->WantsEventEmail(new ReservationSeriesEndingEvent())) {
                    ServiceLocator::GetEmailService()->Send(new ReservationSeriesEndingEmail($reservation, $user));
                }

                if ($smsEnabled && $user->IsSmsOptedIn() && $user->WantsEventSms(new ReservationSeriesEndingEvent())) {
                    $sms->Send(new SeriesEndingSms($user, $reservation));
                }
            }
        }
        $reader->Free();
    }

    /**
     * @return AdHocCommand
     */
    private function BuildQuery()
    {
        $now = Date::Now();
        $searchStart = $now->AddDays(7)->ToDatabase();
        $searchEnd = $now->AddDays(8)->ToDatabase();

        $instancesEnding = new AdHocCommand("SELECT
  `ri`.`reference_number`, `sub`.`last_date`, `sub`.`timezone`, `sub`.`language`, `sub`.`email`
FROM
  `reservation_instances` `ri`
INNER JOIN
(
  SELECT
    `ri`.`series_id`, MAX(`ri`.`start_date`) AS `last_date`, `u`.`timezone`, `u`.`language`, `u`.`email`
  FROM
   `user_email_preferences` `uep`
  INNER JOIN
     `reservation_series` `rs` ON `rs`.`owner_id` = `uep`.`user_id`
  INNER JOIN
    `reservation_instances` `ri` ON `rs`.`series_id` = `ri`.`series_id`
  INNER JOIN
    `users` `u` ON `rs`.`owner_id` = `u`.`user_id` AND `u`.`status_id` = 1
  WHERE
    `repeat_type` <> 'none' 
    AND `rs`.`status_id` = 1
    AND `uep`.`event_category` = @event_category 
    AND `uep`.`event_type` = @event_type
  GROUP BY
    `ri`.`series_id`
) sub ON `ri`.`series_id` = `sub`.`series_id` and `sub`.`last_date` = `ri`.`start_date`
WHERE
  `sub`.`last_date` BETWEEN @startDate AND @endDate");
        $instancesEnding->AddParameter(new Parameter(ParameterNames::START_DATE, $searchStart));
        $instancesEnding->AddParameter(new Parameter(ParameterNames::END_DATE, $searchEnd));
        $instancesEnding->AddParameter(new Parameter(ParameterNames::EVENT_CATEGORY, EventCategory::Reservation));
        $instancesEnding->AddParameter(new Parameter(ParameterNames::EVENT_TYPE, ReservationEvent::SeriesEnding));
        return $instancesEnding;
    }
}

$sendSeriesEndJob = new SendSeriesEndJob();
$sendSeriesEndJob->Run();
