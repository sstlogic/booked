<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Application/Reservation/ExternalMeetings/IMeetingConnectionApi.php');

interface IMeetingConnectionFactory
{
    /**
     * @param ReservationMeetingLinkType|int $meetingLinkType
     * @return IMeetingConnectionApi
     */
    public function CreateApi($meetingLinkType);

    /**
     * @param ReservationMeetingLinkType|int $meetingLinkType
     * @return MeetingConnection
     */
    public function CreateMeetingConnection($meetingLinkType);
}