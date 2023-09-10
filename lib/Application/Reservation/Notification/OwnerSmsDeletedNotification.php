<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

class OwnerSmsDeletedNotification extends OwnerSmsNotification
{
    protected function GetReservationEvent(): IDomainEvent
    {
        return new ReservationDeletedEvent();
    }

    protected function GetMessageKey(): string
    {
        return "SMSMessageReservationDeleted";
    }
}