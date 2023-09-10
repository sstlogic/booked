<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

class ReservationMeetingLink
{
    /**
     * @var ReservationMeetingLinkType
     */
    private $type;
    /**
     * @var string
     */
    private $url;
    /**
     * @var string
     */
    private $id;
    /**
     * @var string[]
     */
    private $errors;
    /**
     * @var string
     */
    private $metadata;

    /**
     * @param $type ReservationMeetingLinkType
     * @param $url string
     */
    public function __construct($type, $url, $id = null)
    {
        $this->type = $type;
        $this->url = $url;
        $this->id = $id;
    }

    /**
     * @param ReservationMeetingLinkType|int $type
     * @param string $url
     * @param string $meetingId
     * @param string[] $errors
     * @param string $metadata
     * @return ReservationMeetingLink
     */
    public static function FromResponse($type, $url, $meetingId, $errors, $metadata)
    {
        $link = new ReservationMeetingLink($type, $url);
        $link->id = $meetingId;
        $link->errors = $errors;
        $link->metadata = $metadata;
        return $link;
    }

    /**
     * @return ReservationMeetingLinkType
     */
    public function Type()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function Url()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function Id()
    {
        return $this->id;
    }

    /**
     * @return ReservationMeetingLink
     */
    public function Clone()
    {
        $link = new ReservationMeetingLink($this->Type(), $this->Url(), $this->Id());
        $link->errors = $this->errors;
        $link->metadata = $this->metadata;
        return $link;
    }

    /**
     * @return string[]
     */
    public function Errors()
    {
        return $this->errors;
    }
}