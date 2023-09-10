<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

class OwnerSmsUpdatedNotification extends OwnerSmsNotification
{
    protected function GetReservationEvent(): IDomainEvent
    {
        return new ReservationUpdatedEvent();
    }

    protected function GetMessageKey(): string
    {
        return "SMSMessageReservationUpdated";
    }
}