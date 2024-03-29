<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

class RequiresApprovalRule implements IReservationValidationRule
{
	/**
	 * @var IAuthorizationService
	 */
	private $authorizationService;

	public function __construct(IAuthorizationService $authorizationService)
	{
		$this->authorizationService = $authorizationService;
	}

	/**
	 * @param ReservationSeries $reservationSeries
	 * @param null|ReservationRetryParameter[] $retryParameters
	 * @return ReservationRuleResult
	 */
	function Validate($reservationSeries, $retryParameters)
	{
		$status = ReservationStatus::Created;

		/** @var BookableResource $resource */
		foreach ($reservationSeries->AllResources() as $resource)
		{
			if ($resource->GetRequiresApproval())
			{
				if (!$this->authorizationService->CanApproveForResource($reservationSeries->BookedBy(), $resource))
				{
					$status = ReservationStatus::Pending;
					break;
				}
			}
		}

		$reservationSeries->SetStatusId($status);

		return new ReservationRuleResult();
	}
}