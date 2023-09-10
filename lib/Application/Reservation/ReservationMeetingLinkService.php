<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Application/Reservation/ExternalMeetings/MeetingConnection.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/ExternalMeetings/ZoomApi.php');

interface IReservationMeetingLinkService
{
    public function Add(ReservationSeries $reservation, IReservationRepository $repository);

    public function Update(ExistingReservationSeries $reservation, IReservationRepository $repository);

    public function Delete(ExistingReservationSeries $reservation, IReservationRepository $repository);
}

class ReservationMeetingLinkService implements IReservationMeetingLinkService
{
    /**
     * @var IMeetingConnectionFactory
     */
    private $meetingConnectionFactory;

    public function __construct(IMeetingConnectionFactory $meetingConnectionFactory)
    {
        $this->meetingConnectionFactory = $meetingConnectionFactory;
    }

    public function Add(ReservationSeries $reservation, IReservationRepository $repository)
    {
        if (empty($reservation->MeetingLink())) {
            return;
        }
        // todo - move the existing meeting link stuff out of the events?
        $meetingLinkType = $reservation->MeetingLink()->Type();
        if (!$reservation->CanSaveMeetingLink() || $meetingLinkType == ReservationMeetingLinkType::Link) {
            return;
        }

        try {
            $meetingConnection = $this->meetingConnectionFactory->CreateMeetingConnection($meetingLinkType);
            $meeting = $meetingConnection->Add($reservation);
            $repository->UpdateMeeting($reservation, $meeting);
        } catch (Throwable $ex) {
            Log::Error("Error adding meeting connection.", ['type' => $meetingLinkType, 'exception' => $ex]);
        }
    }

    public function Update(ExistingReservationSeries $reservation, IReservationRepository $repository)
    {
        $currentMeeting = $reservation->MeetingLink();
        $previousMeeting = $reservation->PreviousMeetingLink();

        $meetingLinkType = empty($currentMeeting) ? null : $currentMeeting->Type();
        if ((empty($currentMeeting) || $currentMeeting->Type() == ReservationMeetingLinkType::Link) && !empty($previousMeeting)) {
            $meetingLinkType = $previousMeeting->Type();
        }

        if (!$reservation->CanSaveMeetingLink() || empty($meetingLinkType) || $meetingLinkType == ReservationMeetingLinkType::Link) {
            return;
        }

        try {
            $meetingConnection = $this->meetingConnectionFactory->CreateMeetingConnection($meetingLinkType);
            $meeting = $meetingConnection->Update($reservation);
            if (!empty($meeting)) {
                $repository->UpdateMeeting($reservation, $meeting);
            }
        } catch (Throwable $ex) {
            Log::Error("Error updating meeting connection.", ['type' => $meetingLinkType, 'exception' => $ex]);
        }
    }

    public function Delete(ExistingReservationSeries $reservation, IReservationRepository $repository)
    {
        if (empty($reservation->MeetingLink())) {
            return;
        }
        $meetingLinkType = $reservation->MeetingLink()->Type();
        if (!$reservation->CanSaveMeetingLink() || $meetingLinkType == ReservationMeetingLinkType::Link) {
            return;
        }

        try {
            $meetingConnection = $this->meetingConnectionFactory->CreateMeetingConnection($meetingLinkType);
            $meeting = $meetingConnection->Delete($reservation);
            $repository->UpdateMeeting($reservation, $meeting);
        } catch (Throwable $ex) {
            Log::Error("Error deleting meeting connection.", ['type' => $meetingLinkType, 'exception' => $ex]);
        }

    }
}