<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ReservationAccessoryApiDto
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $quantityReserved;

    /**
     * @param ReservationAccessoryView[] $accessories
     * @return ReservationAccessoryApiDto[]
     */
    public static function FromList(array $accessories)
    {
        $dtos = [];

        foreach($accessories as $accessory) {
            $dto = new ReservationAccessoryApiDto();
            $dto->id = intval($accessory->AccessoryId);
            $dto->quantityReserved = intval($accessory->QuantityReserved);

            $dtos[] = $dto;
        }

        return $dtos;
    }

    /**
     * @param int $id
     * @param int $quantityReserved
     * @return ReservationAccessoryApiDto
     */
    public static function Create($id, $quantityReserved) {
        $dto = new ReservationAccessoryApiDto();
        $dto->id = intval($id);
        $dto->quantityReserved = intval($quantityReserved);
        return $dto;
    }
}