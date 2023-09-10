<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Values/ReservationMeetingLinkType.php');

class ReservationMeetingLinkView
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
     * @param ReservationMeetingLinkType $type
     * @param string $url
     */
    public function __construct($type, $url)
    {
        $this->type = $type;
        $this->url = $url;
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

}