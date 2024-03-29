<?php
/**
 * Copyright 2018-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Access/ScheduleRepository.php');

class ScheduleAvailabilityRule implements IReservationValidationRule
{
    /**
     * @var IScheduleRepository
     */
    protected $scheduleRepository;

    public function __construct(IScheduleRepository $scheduleRepository)
    {
        $this->scheduleRepository = $scheduleRepository;
    }

    public function Validate($reservationSeries, $retryParameters)
    {
        $schedule = $this->scheduleRepository->LoadById($reservationSeries->ScheduleId());

        if (!$schedule->HasAvailability())
        {
            return new ReservationRuleResult();
        }

        $reservations = $reservationSeries->SortedInstances();

        reset($reservations);
        $key = key($reservations);
        $first = $reservations[$key];

        end($reservations);
        $key = key($reservations);
        $last = $reservations[$key];
        reset($reservations);

        $beginAvailability = $schedule->GetAvailabilityBegin();
        $endAvailability = $schedule->GetAvailabilityEnd();
        $isValid = $first->StartDate()->GreaterThanOrEqual($beginAvailability) &&
            $last->EndDate()->LessThanOrEqual($endAvailability);

        $resources = Resources::GetInstance();
        $format = $resources->GetDateFormat(ResourceKeys::DATE_GENERAL);
        $tz = $schedule->GetTimezone();

        return new ReservationRuleResult($isValid, $resources->GetString('ScheduleAvailabilityError',
            array($beginAvailability->ToTimezone($tz)->Format($format), $endAvailability->ToTimezone($tz)->Format($format)))
        );
    }

    /**
     * @param array $conflicts
     * @return string
     */
    protected function GetErrorString($conflicts)
    {
        $errorString = new StringBuilder();

        $errorString->Append(Resources::GetInstance()->GetString('ConflictingAccessoryDates'));
        $errorString->AppendLine();
        $format = Resources::GetInstance()->GetDateFormat(ResourceKeys::DATE_GENERAL);

        foreach ($conflicts as $conflict) {
            $errorString->Append(sprintf('(%s) %s', $conflict['date']->ToTimezone($this->timezone)->Format($format), $conflict['name']));
            $errorString->AppendLine();
        }

        return $errorString->ToString();
    }
}