<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/UrlPaths.php');

class ReservationUrl
{
    public static function Create(string $referenceNumber): string
    {
        return sprintf('%s/%s?%s=%s', Configuration::Instance()->GetScriptUrl(), UrlPaths::RESERVATION, QueryStringKeys::REFERENCE_NUMBER, $referenceNumber);
    }
}