<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

interface IDomainEvent
{
    /**
     * @return string
     */
    function EventType();

    /**
     * @return EventCategory|string
     */
    function EventCategory();
}