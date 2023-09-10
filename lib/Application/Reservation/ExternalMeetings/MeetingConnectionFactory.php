<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Access/UserRepository.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/ExternalMeetings/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/ExternalMeetings/ZoomApi.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/ExternalMeetings/MicrosoftTeamsApi.php');

class MeetingConnectionFactory implements IMeetingConnectionFactory {

    public function CreateApi($meetingLinkType)
    {
        if ($meetingLinkType == ReservationMeetingLinkType::Teams) {
            return new MicrosoftTeamsApi();
        }
        return new ZoomApi();
    }

    public function CreateMeetingConnection($meetingLinkType)
    {
        return new MeetingConnection(new UserRepository(), $this->CreateApi($meetingLinkType));
    }
}