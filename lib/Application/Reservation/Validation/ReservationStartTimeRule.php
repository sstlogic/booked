<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'Domain/Values/ReservationStartTimeConstraint.php');

class ReservationStartTimeRule implements IReservationValidationRule
{
    /**
     * @var IScheduleRepository
     */
    private $scheduleRepository;

    public function __construct(IScheduleRepository $scheduleRepository)
    {
        $this->scheduleRepository = $scheduleRepository;
    }

    /**
     * @param ReservationSeries $reservationSeries
     * @param $retryParameters
     * @return ReservationRuleResult
     * @throws Exception
     */
	public function Validate($reservationSeries, $retryParameters)
	{
        $constraint = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_START_TIME_CONSTRAINT);

        if (empty($constraint))
        {
            $constraint = ReservationStartTimeConstraint::_DEFAULT;
        }

        if ($constraint == ReservationStartTimeConstraint::NONE)
        {
            return new ReservationRuleResult();
        }

        $currentInstance = $reservationSeries->CurrentInstance();

        $dateThatShouldBeLessThanNow = $currentInstance->StartDate();
        if ($constraint == ReservationStartTimeConstraint::CURRENT)
        {
            $timezone = $dateThatShouldBeLessThanNow->Timezone();
            /** @var $currentPeriod SchedulePeriod */
			$currentPeriod = $this->scheduleRepository
					->GetLayout($reservationSeries->ScheduleId(), new ScheduleLayoutFactory($timezone))
					->GetPeriod($currentInstance->EndDate());
            $dateThatShouldBeLessThanNow = $currentPeriod->BeginDate();
        }

		$startIsInFuture = $dateThatShouldBeLessThanNow->Compare(Date::Now()) >= 0;
		return new ReservationRuleResult($startIsInFuture, Resources::GetInstance()->GetString('StartIsInPast'));
	}
}