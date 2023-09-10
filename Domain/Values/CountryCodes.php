<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

class CountryCodes
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $code;
    /**
     * @var string
     */
    public $phone;

    public function __construct($name, $code, $phone)
    {
        $this->name = $name;
        $this->code = $code;
        $this->phone = $phone;
    }

    public function __toString()
    {
       return $this->code;
    }

    private static function GetDefault(): CountryCodes {
       return new CountryCodes("Other/Not Listed", "", "");
    }

    /**
     * @return CountryCodes[]
     */
    public static function All(): array
    {
        return [
            new CountryCodes("United States", "US", "1"),
            new CountryCodes("United Kingdom", "GB", "44"),
            new CountryCodes("Austria", "AT", "43"),
            new CountryCodes("Belgium", "BE", "32"),
            new CountryCodes("Brazil", "BR", "55"),
            new CountryCodes("Canada", "CA", "1"),
            new CountryCodes("Chile", "CL", "56"),
            new CountryCodes("China", "CN", "86"),
            new CountryCodes("Denmark", "DK", "45"),
            new CountryCodes("Finland", "FI", "358"),
            new CountryCodes("France", "FR", "33"),
            new CountryCodes("Germany", "DE", "49"),
            new CountryCodes("Greece", "GR", "30"),
            new CountryCodes("Hong Kong", "HK", "852"),
            new CountryCodes("Iceland", "IS", "354"),
            new CountryCodes("India", "IN", "91"),
            new CountryCodes("Indonesia", "ID", "62"),
            new CountryCodes("Ireland", "IE", "353"),
            new CountryCodes("Italy", "IT", "39"),
            new CountryCodes("Israel", "IL", "972"),
            new CountryCodes("Japan", "JP", "81"),
            new CountryCodes("Netherlands", "NL", "31"),
            new CountryCodes("Norway", "NO", "47"),
            new CountryCodes("Mexico", "MX", "52"),
            new CountryCodes("Pakistan", "PK", "92"),
            new CountryCodes("Philippines", "PH", "63"),
            new CountryCodes("Poland", "PL", "48"),
            new CountryCodes("Portugal", "PT", "351"),
            new CountryCodes("Puerto Rico", "PR", "1787"),
            new CountryCodes("Romania", "RO", "40"),
            new CountryCodes("Singapore", "SG", "65"),
            new CountryCodes("Sweden", "SE", "46"),
            new CountryCodes("Switzerland", "CH", "41"),
            new CountryCodes("Thailand", "TH", "66"),
            new CountryCodes("Ukraine", "UA", "380"),
            self::GetDefault(),
        ];
    }

    public static function Guess(?string $language): CountryCodes
    {
        if (empty($language)) {
            return self::GetDefault();
        }

        $code = strtoupper($language);
        if (BookedStringHelper::Contains($language, "_")) {
            $code = strtoupper(explode('_', $language)[1]);
        }

        foreach (CountryCodes::All() as $c) {
            if ($c->code == $code) {
                return $c;
            }
        }
        return self::GetDefault();
    }

    public static function Get($phoneCountryCode, $phone, $language): CountryCodes
    {
        if (empty($phoneCountryCode) || empty($phone)) {
            return self::GetDefault();
        }

        if (empty($phoneCountryCode)) {
            return self::Guess($language);
        }

        foreach (CountryCodes::All() as $c) {
            if ($c->code == $phoneCountryCode) {
                return $c;
            }
        }
        return self::GetDefault();
    }
}