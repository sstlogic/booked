<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/external/random/random.php');

class BookedStringHelper
{
    /**
     * @static
     * @param $haystack string
     * @param $needle string
     * @return bool
     */
    public static function StartsWith($haystack, $needle)
    {
        $length = empty($needle) ? 0 : strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * @static
     * @param $haystack string
     * @param $needle string
     * @return bool
     */
    public static function EndsWith($haystack, $needle)
    {
        $length = empty($needle) ? 0 : strlen($needle . '');
        if ($length == 0) {
            return true;
        }

        $start = $length * -1;
        return (substr($haystack . '', $start) === $needle);
    }

    /**
     * @static
     * @param $haystack string
     * @param $needle string
     * @return bool
     */
    public static function Contains($haystack, $needle)
    {
        return strpos($haystack, $needle) !== false;
    }

    private static $_random = null;

    /**
     * @return string
     */
    public static function Random($length = 50)
    {
        if (!empty(self::$_random)) {
            return self::$_random;
        }

        try {
            $string = random_bytes(intval($length / 2));
            $string = bin2hex($string);
        } catch (Exception $ex) {
            $string = uniqid(rand(), true);
            Log::Error('Could not generate random using random_bytes.', ['exception' => $ex]);
        }
        return $string;
    }

    /**
     * TESTING ONLY
     * @param string $rand
     */
    public static function _SetRandom($rand)
    {
        self::$_random = $rand;
    }
}