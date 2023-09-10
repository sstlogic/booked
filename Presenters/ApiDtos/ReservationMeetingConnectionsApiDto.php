<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

class ReservationMeetingConnectionApiDto
{
    public $linked = false;
}

class ReservationMeetingConnectionsApiDto
{
    /**
     * @var ReservationMeetingConnectionApiDto
     */
    public $meet;
    /**
     * @var ReservationMeetingConnectionApiDto
     */
    public $zoom;
    /**
     * @var ReservationMeetingConnectionApiDto
     */
    public $teams;

    public function __construct()
    {
        $this->meet = new ReservationMeetingConnectionApiDto();
        $this->zoom = new ReservationMeetingConnectionApiDto();
        $this->teams = new ReservationMeetingConnectionApiDto();
    }

    /**
     * @param UserOAuth[] $oauth
     * @return ReservationMeetingConnectionsApiDto
     */
    public static function FromList(array $oauth)
    {
        $dto = new ReservationMeetingConnectionsApiDto();

        foreach ($oauth as $o) {
            if ($o->ProviderId() === OAuthProviders::Zoom) {
                $dto->zoom->linked = true;
            }
            if ($o->ProviderId() === OAuthProviders::Microsoft) {
                $dto->teams->linked = true;
            }
        }

        return $dto;
    }
}