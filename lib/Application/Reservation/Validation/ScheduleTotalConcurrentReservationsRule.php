<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Access/ScheduleRepository.php');
require_once(ROOT_DIR . 'Domain/Access/ReservationViewRepository.php');

class ScheduleTotalConcurrentReservationsRule implements IReservationValidationRule
{
	/**
	 * @var IReservationViewRepository
	 */
	protected $reservationRepository;

	/**
	 * @var IScheduleRepository
	 */
	protected $scheduleRepository;
	private $timezone;

	public function __construct(IScheduleRepository $scheduleRepository, IReservationViewRepository $reservationRepository, $timezone)
	{
		$this->reservationRepository = $reservationRepository;
		$this->scheduleRepository = $scheduleRepository;
		$this->timezone = $timezone;
	}

	public function Validate($reservationSeries, $retryParameters)
	{
		$schedule = $this->scheduleRepository->LoadById($reservationSeries->ScheduleId());
		if (!$schedule->EnforceConcurrentReservationMaximum())
		{
			return new ReservationRuleResult();
		}

		$isValid = true;
		$invalidDates = [];
		$totalConcurrentReservations = $schedule->GetTotalConcurrentReservations();

		foreach ($reservationSeries->Instances() as $instance)
		{
			if ($reservationSeries->IsMarkedForDelete($instance->ReservationId()))
			{
				continue;
			}

			$reservations = $this->reservationRepository->GetConflicts(new DateRange($instance->StartDate(), $instance->EndDate()), null, $reservationSeries->ScheduleId());
            /** @var ReservationConflictView[] $allConflicts */
			$allConflicts = [];
			foreach ($reservations as $existingItem)
			{
				if ($existingItem->GetReferenceNumber() == $instance->ReferenceNumber())
				{
					continue;
				}

				if ($existingItem->BufferedTimes()->Overlaps($instance->Duration()))
				{
				    $allConflicts[] = $existingItem;
				}
			}

			// check if existing reservations conflict with each other to get true concurrent count
			$conflictsByRef = [];
            $maxConflicts = count($allConflicts) != 0 ? 1 : 0;
			foreach ($allConflicts as $c1) {
                $conflictsByRef[$c1->GetReferenceNumber()][] = $c1->GetReferenceNumber();
			    foreach ($allConflicts as $c2) {
			        if ($c1->GetReferenceNumber() == $c2->GetReferenceNumber()) {
			            continue;
                    }
			        if ($c1->BufferedTimes()->Overlaps($c2->GetDateRange())) {
                        if (!array_key_exists($c1->GetReferenceNumber(), $conflictsByRef)) {
                            $conflictsByRef[$c1->GetReferenceNumber()] = [];
                        }
						if (!in_array($c2->GetReferenceNumber(), $conflictsByRef[$c1->GetReferenceNumber()])) {
							$conflictsByRef[$c1->GetReferenceNumber()][] = $c2->GetReferenceNumber();
						}
                    }
                }
            }

            foreach ($conflictsByRef as $ref => $conflictList) {
                $localConflicts = 0;
                foreach ($conflictList as $otherRef) {
                    $localConflicts = count(array_intersect($conflictsByRef[$ref], $conflictsByRef[$otherRef]));
                }

                if ($localConflicts > $maxConflicts) {
                    $maxConflicts = $localConflicts;
                }
            }

			if ($maxConflicts + count($reservationSeries->AllResourceIds()) > $totalConcurrentReservations)
			{
				$isValid = false;
				$invalidDates[] = $instance->StartDate();
			}
		}

		return new ReservationRuleResult($isValid, $this->GetErrorMessage($invalidDates, $totalConcurrentReservations));

	}

	/**
	 * @param $invalidDates Date[]
	 * @param $totalConcurrentReservationLimit int
	 * @return string;
	 */
	private function GetErrorMessage($invalidDates, $totalConcurrentReservationLimit)
	{
		$uniqueDates = array_unique($invalidDates);
		sort($uniqueDates);
		$resources = Resources::GetInstance();
		$format = $resources->GetDateFormat(ResourceKeys::DATE_GENERAL);
		$formatted = [];
		foreach($uniqueDates as $d) {
			$formatted[] = $d->ToTimezone($this->timezone)->Format($format);
		}

		$datesAsString = implode(",", $formatted);

		$errorString = new StringBuilder();
		$errorString->AppendLine(Resources::GetInstance()->GetString('ScheduleTotalReservationsError', array($totalConcurrentReservationLimit)));
		$errorString->Append($datesAsString);
		return $errorString->ToString();
	}
}