<?php
/**
 * Copyright 2013-2023 Twinkle Toes Software, LLC
 */

class ResourceParticipationRule implements IReservationValidationRule
{
    /**
     * @param ReservationSeries $reservationSeries
     * @param $retryParameters
     * @return ReservationRuleResult
     */
    public function Validate($reservationSeries, $retryParameters)
    {
        $errorMessage = new StringBuilder();
        foreach ($reservationSeries->AllResources() as $resource) {
            if (!$resource->HasMaxParticipants()) {
                continue;
            }

            $includeInvitees = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_LIMIT_INVITEES_TO_MAX_PARTICIPANTS, new BooleanConverter());

            foreach ($reservationSeries->Instances() as $instance) {
                $numberOfParticipants = count($instance->Participants());
                $numberOfParticipants += count($instance->ParticipatingGuests());
                if ($includeInvitees) {
                    $numberOfParticipants += count($instance->Invitees());
                    $numberOfParticipants += count($instance->InvitedGuests());
                }

                Log::Debug('ResourceParticipationRule',
                    ['resourceName' => $resource->GetName(), 'reservationId' => $instance->ReservationId(), 'maxParticipants' => $resource->GetMaxParticipants(),
                        'numberOfParticipants' => $numberOfParticipants]);
                if ($numberOfParticipants > $resource->GetMaxParticipants()) {
                    $errorMessage->AppendLine(Resources::GetInstance()->GetString('MaxParticipantsError', array($resource->GetName(), $resource->GetMaxParticipants())));
                }
            }
        }

        $message = $errorMessage->ToString();
        if (strlen($message) > 0) {
            return new ReservationRuleResult(false, $message);
        }
        return new ReservationRuleResult();
    }
}
