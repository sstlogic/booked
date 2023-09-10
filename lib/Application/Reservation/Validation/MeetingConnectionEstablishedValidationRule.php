<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/Validation/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/ExternalMeetings/namespace.php');

class MeetingConnectionEstablishedValidationRule implements IReservationValidationRule
{
    /**
     * @var IUserRepository
     */
    private $userRepository;
    /**
     * @var IMeetingConnectionFactory
     */
    private $meetingConnectionFactory;

    public function __construct(IUserRepository $userRepository, IMeetingConnectionFactory $meetingConnectionFactory)
    {

        $this->userRepository = $userRepository;
        $this->meetingConnectionFactory = $meetingConnectionFactory;
    }

    public function Validate($reservationSeries, $retryParameters)
    {
        if (!$reservationSeries->CanSaveMeetingLink()) {
            return new ReservationRuleResult();
        }

        $currentMeetingLink = $reservationSeries->MeetingLink();
        $previousMeetingLink = $reservationSeries->PreviousMeetingLink();

        $meetingToUse = null;
        if (!empty($currentMeetingLink) && $currentMeetingLink->Type() != ReservationMeetingLinkType::Link) {
            $meetingToUse = $currentMeetingLink;
        }
        if (!empty($previousMeetingLink) && $previousMeetingLink->Type() != ReservationMeetingLinkType::Link) {
            $meetingToUse = $previousMeetingLink;
        }

        if (empty($meetingToUse)) {
            return new ReservationRuleResult();
        }

        $api = $this->meetingConnectionFactory->CreateApi($meetingToUse->Type());

        $oauth = $this->userRepository->GetOAuth($reservationSeries->BookedBy()->UserId, $api->ProviderId());
        if (empty($oauth))
        {
            return new ReservationRuleResult(false, Resources::GetInstance()->GetString('ErrorConnectingToExternalMeeting', $api->Name()));
        }

        if ($oauth->RequiresRefresh()) {
            try {
                $refreshResponse = $api->Refresh($oauth->RefreshToken());
                $this->userRepository->AddOrUpdateOAuth($oauth->UserId(), $refreshResponse->AccessToken(), $refreshResponse->RefreshToken(), $refreshResponse->ExpiresAt(), $oauth->ProviderId());
            } catch (Throwable $ex) {
                Log::Error("Error refreshing token", ['exception' => $ex]);
                $this->userRepository->RemoveOAuth($oauth->UserId(), $api->ProviderId());
                return new ReservationRuleResult(false, Resources::GetInstance()->GetString('ErrorConnectingToExternalMeeting', $api->Name()));
            }
        }
        return new ReservationRuleResult();
    }
}