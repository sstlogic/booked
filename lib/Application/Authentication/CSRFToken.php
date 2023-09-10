<?php

/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

class CSRFToken
{
    /**
     * @var string
     */
    public static $_Token;

    /**
     * @return string
     */
    public static function Create()
    {
        if (!empty(self::$_Token)) {
            return self::$_Token;
        }

        return base64_encode(md5(BookedStringHelper::Random()));
    }
}
