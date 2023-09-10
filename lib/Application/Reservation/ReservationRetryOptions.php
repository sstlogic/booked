<?php

interface IReservationRetryOptions
{
	/**
	 * @param ReservationSeries $series
	 * @param ReservationRetryParameter[] $retryParameters
	 */
	public function AdjustReservation(ReservationSeries $series, $retryParameters);
}

class ReservationRetryOptions implements IReservationRetryOptions
{
	/**
	 * @var IReservationConflictIdentifier
	 */
	private $conflictIdentifier;
	/**
	 * @var IScheduleRepository
	 */
	private $scheduleRepository;

	public function __construct(IReservationConflictIdentifier $conflictIdentifier, IScheduleRepository $scheduleRepository)
	{
		$this->conflictIdentifier = $conflictIdentifier;
		$this->scheduleRepository = $scheduleRepository;
	}

	public function AdjustReservation(ReservationSeries $series, $retryParameters)
	{
		$shouldSkipConflicts = ReservationRetryParameter::GetValue(ReservationRetryParameter::$SKIP_CONFLICTS, $retryParameters,
																   new BooleanConverter()) == true;
		if (!$shouldSkipConflicts)
		{
			return;
		}

		Log::Debug('Retrying reservation skipping conflicts');

		$conflicts = $this->conflictIdentifier->GetConflicts($series);

		foreach ($conflicts->Conflicts() as $conflict)
		{
			$series->RemoveInstance($conflict->Reservation);
		}

		if (Configuration::Instance()->GetSectionKey(ConfigSection::CREDITS, ConfigKeys::CREDITS_ENABLED, new BooleanConverter()))
		{
			$series->CalculateCredits($this->scheduleRepository->GetLayout($series->ScheduleId(),
																		   new ScheduleLayoutFactory($series->CurrentInstance()->StartDate()->Timezone())));
		}
	}
}