<?php
/**
Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ResourceMinParticipationRule implements IReservationValidationRule
{
	/**
	 * @param ReservationSeries $reservationSeries
	 * @param $retryParameters
	 * @return ReservationRuleResult
	 */
	public function Validate($reservationSeries, $retryParameters)
	{
		$errorMessage = new StringBuilder();
		foreach ($reservationSeries->AllResources() as $resource)
		{
			if (!$resource->HasMinParticipants())
			{
				continue;
			}

			foreach ($reservationSeries->Instances() as $instance)
			{
				$numberOfParticipants = count($instance->Participants()) + count($instance->Invitees()) + count($instance->InvitedGuests()) + count($instance->ParticipatingGuests());

//				Log::Debug('ResourceMinParticipationRule Resource=%s,InstanceId=%s,MinParticipants=%s,CurrentParticipants=%s',
//						   $resource->GetName(), $instance->ReservationId(), $resource->GetMinParticipants(),
//						   $numberOfParticipants);
				if ($numberOfParticipants < $resource->GetMinParticipants())
				{
					$errorMessage->AppendLine(Resources::GetInstance()->GetString('MinParticipantsError',
																				  array($resource->GetName(), $resource->GetMinParticipants())));
				}
			}
		}

		$message = $errorMessage->ToString();
		if (strlen($message) > 0)
		{
			return new ReservationRuleResult(false, $message);
		}
		return new ReservationRuleResult();
	}
}
