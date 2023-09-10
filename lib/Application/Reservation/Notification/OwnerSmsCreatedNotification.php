<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

class OwnerSmsCreatedNotification extends OwnerSmsNotification
{
    protected function GetReservationEvent(): IDomainEvent
    {
        return new ReservationCreatedEvent();
    }

    protected function GetMessageKey(): string
    {
        return "SMSMessageReservationCreated";
    }
}