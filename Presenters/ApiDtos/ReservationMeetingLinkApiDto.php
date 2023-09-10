<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

class ReservationMeetingLinkApiDto
{
    /**
     * @var ReservationMeetingLinkType
     */
    public $type;
    /**
     * @var string|null
     */
    public $url;

    /**
     * @param ReservationMeetingLinkView|null $meetingLink
     * @return ReservationMeetingLinkApiDto|null
     */
    public static function FromView($meetingLink)
    {
        if (empty($meetingLink)) {
            return null;
        }

        $dto = new ReservationMeetingLinkApiDto();
        $dto->url = apidecode($meetingLink->Url());
        $dto->type = intval($meetingLink->Type());
        return $dto;
    }

    /**
     * @param ReservationMeetingLinkType|int $type
     * @param string|null $url
     * @return ReservationMeetingLinkApiDto
     */
    public static function Create($type, $url)
    {
        $dto = new ReservationMeetingLinkApiDto();
        $dto->url = apiencode($url);
        $dto->type = intval($type);
        return $dto;
    }
}