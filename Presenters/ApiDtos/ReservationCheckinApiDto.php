<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ReservationCheckinApiDto
{
    /**
     * @var bool
     */
    public $checkinAvailable = false;
    /**
     * @var bool
     */
    public $checkoutAvailable = false;
    /**
     * @var null|int
     */
    public $autoReleaseMinutes = null;
    /**
     * @var bool
     */
    public $missedCheckin = false;
    /**
     * @var bool
     */
    public $missedCheckout = false;

    public static function FromView(ReservationView $reservation, $isAdmin, $canEdit): ReservationCheckinApiDto
    {
        $dto = new ReservationCheckinApiDto();
        if (empty($reservation->ReferenceNumber) || !$canEdit) {
            $dto->checkinAvailable = false;
            $dto->checkoutAvailable = false;
            $dto->autoReleaseMinutes = null;
            $dto->missedCheckin = false;
            $dto->missedCheckout = false;
            return $dto;
        }

        $dto->checkinAvailable = $reservation->IsCheckinAvailable($isAdmin);
        $dto->checkoutAvailable = $reservation->IsCheckoutAvailable($isAdmin);
        $dto->autoReleaseMinutes = $reservation->AutoReleaseMinutes();
        $dto->missedCheckin = $reservation->IsMissedCheckIn();
        $dto->missedCheckout = $reservation->IsMissedCheckOut();
        return $dto;
    }
}