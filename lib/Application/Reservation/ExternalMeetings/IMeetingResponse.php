<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

interface IMeetingResponse
{
    /**
     * @return string
     */
    public function MeetingId();

    /**
     * @return string
     */
    public function MeetingUrl();

    /**
     * @return string[]
     */
    public function Errors();

    /**
     * @return boolean
     */
    public function IsSaved();

    /**
     * @return string
     */
    public function Metadata();
}
