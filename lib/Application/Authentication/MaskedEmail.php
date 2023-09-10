<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class MaskedEmail
{
    /**
     * @param string $email
     * @return string
     */
    public static function Mask($email)
    {
        $parts = explode('@', $email);
        $email = $parts[0];
        $domain = $parts[1];

        $length = strlen($email);
        if ($length <= 5) {
            $toReveal = substr($email, -1, 1);
            return str_repeat('*', $length - 1) . $toReveal . '@'. $domain;
        }

       if ($length <= 6) {
           $toReveal = substr($email, -2, 2);
           return str_repeat('*', $length - 2) . $toReveal . '@'. $domain;
       }

        $toReveal = substr($email, -3, 3);
        return str_repeat('*', $length - 3) . $toReveal . '@'. $domain;
    }

}