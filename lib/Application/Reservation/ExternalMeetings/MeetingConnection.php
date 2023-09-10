<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/ExternalMeetings/IMeetingConnectionApi.php');

class MeetingConnection
{
    /**
     * @var IUserRepository
     */
    private $userRepository;
    /**
     * @var IMeetingConnectionApi
     */
    private $api;

    public function __construct(IUserRepository $userRepository, IMeetingConnectionApi $api)
    {
        $this->userRepository = $userRepository;
        $this->api = $api;
    }

    /**
     * @param ReservationSeries $reservation
     * @return ReservationMeetingLink|null
     */
    public function Add(ReservationSeries $reservation)
    {
        $oauth = $this->userRepository->GetOAuth($reservation->BookedBy()->UserId, $this->api->ProviderId());

        return $this->api->Create($reservation, $oauth->AccessToken());
    }

    /**
     * @param ExistingReservationSeries $reservation
     * @return ReservationMeetingLink|null
     */
    public function Update(ExistingReservationSeries $reservation)
    {
        $oauth = $this->userRepository->GetOAuth($reservation->BookedBy()->UserId, $this->api->ProviderId());

        $previous = $reservation->PreviousMeetingLink();
        $meeting = $reservation->MeetingLink();

        if ((empty($previous) || $previous->Type() == ReservationMeetingLinkType::Link) && !empty($meeting) && empty($meeting->Id())) {
            return $this->api->Create($reservation, $oauth->AccessToken());
        }

        if (!empty($previous) && empty($meeting) || empty($meeting->Id())) {
            return $this->api->Delete($previous->Id(), $oauth->AccessToken());
        }

        return $this->api->Update($reservation, $oauth->AccessToken());
    }

    /**
     * @param ExistingReservationSeries $reservation
     * @return null
     */
    public function Delete(ExistingReservationSeries $reservation)
    {
        $meetingId = empty($reservation->MeetingLink()) ? null : $reservation->MeetingLink()->Id();
        if (empty($meetingId)) {
            return null;
        }

        $oauth = $this->userRepository->GetOAuth($reservation->BookedBy()->UserId, $this->api->ProviderId());

        return $this->api->Delete($meetingId, $oauth->AccessToken());
    }
}