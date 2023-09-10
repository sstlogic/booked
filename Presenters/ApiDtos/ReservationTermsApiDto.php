<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ReservationTermsApiDto {
    /**
     * @var string
     */
    public $url;

    public static function FromTerms(?TermsOfService $terms)
    {
        if (empty($terms) || !$terms->AppliesToReservation()) {
            return null;
        }

        $dto = new ReservationTermsApiDto();
        $dto->url = apidecode($terms->DisplayUrl());
        return $dto;
    }
}