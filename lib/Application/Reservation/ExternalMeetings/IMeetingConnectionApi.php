<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Application/Reservation/ExternalMeetings/namespace.php');

interface IMeetingConnectionApi
{
    /**
     * @return OAuthProviders|int
     */
    public function ProviderId();

    /**
     * @param string $refreshToken
     * @return OAuthRefreshResponse
     * @throws Exception
     */
    public function Refresh(string $refreshToken);

    /**
     * @param ReservationSeries $series
     * @param string $authToken
     * @return ReservationMeetingLink
     */
    public function Create(ReservationSeries $series, string $authToken);

    /**
     * @param ExistingReservationSeries $series
     * @param string $authToken
     * @return ReservationMeetingLink
     */
    public function Update(ExistingReservationSeries $series, string $authToken);

    /**
     * @param string $meetingId
     * @param string $authToken
     * @return ReservationMeetingLink|null
     */
    public function Delete($meetingId, string $authToken);

    /**
     * @return string
     */
    public function Name();

}

