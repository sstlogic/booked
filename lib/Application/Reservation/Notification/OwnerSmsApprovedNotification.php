<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

class OwnerSmsApprovedNotification extends OwnerSmsNotification
{
    protected function GetReservationEvent(): IDomainEvent
    {
        return new ReservationApprovedEvent();
    }

    protected function GetMessageKey(): string
    {
        return "SMSMessageReservationApproved";
    }
}