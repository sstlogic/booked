<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lang/AvailableLanguages.php');

interface IResourceLocalization
{
    /**
     * @param $key
     * @param array|string $args
     * @return void
     */
    public function GetString($key, $args = array());

    public function GetDateFormat($key);

    public function GetDays($key);

    public function GetMonths($key);

    function GeneralDateFormat();

    function GeneralDateTimeFormat();
}

class ResourceKeys
{
    const DATE_GENERAL = 'general_date';
    const DATETIME_GENERAL = 'general_datetime';
    const DATETIME_SHORT = 'short_datetime';
    const DATETIME_SYSTEM = 'system_datetime';
}

class Resources implements IResourceLocalization
{
    const DATE_MDY = 1;
    const DATE_DMY = 2;
    const TIME_AMPM = 1;
    const TIME_24H = 2;

    /**
     * @var string
     */
    public $CurrentLanguage;
    public $LanguageFile;
    public $CalendarLanguageFile;

    /**
     * @var array|AvailableLanguage[]
     */
    public $AvailableLanguages = array();

    /**
     * @var string
     */
    public $Charset;

    /**
     * @var string
     */
    public $HtmlLang;

    /**
     * @var string
     */
    public $TextDirection = 'ltr';

    protected $LanguageDirectory;

    private static $_instance;

    private $systemDateKeys = array();

    /**
     * @var Language
     */
    private $_lang;
    /**
     * @var int|null
     */
    private $dateFormat = null;
    /**
     * @var int|null
     */
    private $timeFormat = null;

    protected function __construct()
    {
        $this->LanguageDirectory = dirname(__FILE__) . '/../../lang/';

        $this->systemDateKeys['js_general_date'] = 'yy-mm-dd';
        $this->systemDateKeys['js_general_datetime'] = 'yy-mm-dd HH:mm';
        $this->systemDateKeys['js_general_time'] = 'HH:mm';
        $this->systemDateKeys['system_datetime'] = 'Y-m-d H:i:s';
        $this->systemDateKeys['url'] = 'Y-m-d';
        $this->systemDateKeys['url_full'] = 'Y-m-d H:i:s';
        $this->systemDateKeys['ical'] = 'Ymd\THis\Z';
        $this->systemDateKeys['system'] = 'Y-m-d';
        $this->systemDateKeys['fullcalendar'] = 'Y-m-d H:i';
        $this->systemDateKeys['google'] = 'Ymd\\THi00\\Z';
        $this->systemDateKeys['csv'] = 'Y-m-d H:i';

        $this->LoadAvailableLanguages();
    }

    private static function Create()
    {
        $resources = new Resources();
        $resources->SetCurrentLanguage($resources->GetLanguageCode());
        $resources->LoadOverrides();
        $session = ServiceLocator::GetServer()->GetUserSession();
        $resources->dateFormat = $session->DateFormat;
        $resources->timeFormat = $session->TimeFormat;
        return $resources;
    }

    /**
     * @return Resources
     */
    public static function &GetInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = Resources::Create();
        }

        setlocale(LC_ALL, self::$_instance->CurrentLanguage);
        return self::$_instance;
    }

    public static function SetInstance($instance)
    {
        self::$_instance = $instance;
    }

    /**
     * @param string $languageCode
     * @return bool
     */
    public function SetLanguage($languageCode)
    {
        return $this->SetCurrentLanguage($languageCode);
    }

    /**
     * @param string $languageCode
     * @return bool
     */
    public function IsLanguageSupported($languageCode)
    {
        return !empty($languageCode) &&
            (array_key_exists($languageCode, $this->AvailableLanguages) &&
                file_exists($this->LanguageDirectory . $this->AvailableLanguages[$languageCode]->LanguageFile));
    }

    public function GetString($key, $args = array())
    {
        if (!is_array($args)) {
            $args = array($args);
        }

        $strings = $this->_lang->Strings;

        if (empty($strings[$key])) {
            return '?';
        }

        if (empty($args)) {
            return $strings[$key];
        } else {
            return vsprintf($strings[$key], $args);
        }
    }

    public function GetDateFormat($key, $dateFormat = null, $timeFormat = null)
    {
        if (array_key_exists($key, $this->systemDateKeys)) {
            return $this->systemDateKeys[$key];
        }

        $dates = $this->_lang->Dates;

        if (empty($dates[$key])) {
            return '?';
        }

        if (empty($dateFormat)) {
            $dateFormat = $this->dateFormat;
        }

        if (empty($timeFormat)) {
            $timeFormat = $this->timeFormat;
        }

        if (empty($dateFormat) && empty($timeFormat)) {
            return $dates[$key];
        }

        if (empty($timeFormat) && !empty($dateFormat)) {
            $timeFormat = $dateFormat;
        }

        if (empty($dateFormat) && !empty($timeFormat)) {
            $dateFormat = $timeFormat;
        }


        $keys = [];
        $keys['general_date'][self::DATE_MDY][self::TIME_AMPM] = 'm/d/Y';
        $keys['general_date'][self::DATE_MDY][self::TIME_24H] = 'm/d/Y';
        $keys['general_date'][self::DATE_DMY][self::TIME_AMPM] = 'd/m/Y';
        $keys['general_date'][self::DATE_DMY][self::TIME_24H] = 'd/m/Y';

        $keys['short_date'][self::DATE_MDY][self::TIME_AMPM] = 'n/j/y';
        $keys['short_date'][self::DATE_MDY][self::TIME_24H] = 'n/j/y';
        $keys['short_date'][self::DATE_DMY][self::TIME_AMPM] = 'j/n/y';
        $keys['short_date'][self::DATE_DMY][self::TIME_24H] = 'j/n/y';

        $keys['general_datetime'][self::DATE_MDY][self::TIME_AMPM] = 'm/d/Y g:i:s A';
        $keys['general_datetime'][self::DATE_MDY][self::TIME_24H] = 'm/d/Y H:i:s';
        $keys['general_datetime'][self::DATE_DMY][self::TIME_AMPM] = 'd/m/Y g:i:s A';
        $keys['general_datetime'][self::DATE_DMY][self::TIME_24H] = 'd/m/Y H:i:s';

        $keys['short_datetime'][self::DATE_MDY][self::TIME_AMPM] = 'n/j/y g:i A';
        $keys['short_datetime'][self::DATE_MDY][self::TIME_24H] = 'n/j/y H:i';
        $keys['short_datetime'][self::DATE_DMY][self::TIME_AMPM] = 'j/n/y g:i A';
        $keys['short_datetime'][self::DATE_DMY][self::TIME_24H] = 'j/n/y H:i';

        $keys['schedule_daily'][self::DATE_MDY][self::TIME_AMPM] = 'l, n/j/y';
        $keys['schedule_daily'][self::DATE_MDY][self::TIME_24H] = 'l, n/j/y';
        $keys['schedule_daily'][self::DATE_DMY][self::TIME_AMPM] = 'l, j/m/y';
        $keys['schedule_daily'][self::DATE_DMY][self::TIME_24H] = 'l, j/m/y';

        $keys['reservation_email'][self::DATE_MDY][self::TIME_AMPM] = 'm/d/Y @ g:i A (e)';
        $keys['reservation_email'][self::DATE_MDY][self::TIME_24H] = 'm/d/Y @ H:i (e)';
        $keys['reservation_email'][self::DATE_DMY][self::TIME_AMPM] = 'd/m/Y @ g:i A (e)';
        $keys['reservation_email'][self::DATE_DMY][self::TIME_24H] = 'd/m/Y @ H:i (e)';

        $keys['res_popup'][self::DATE_MDY][self::TIME_AMPM] = 'D, n/d g:i A';
        $keys['res_popup'][self::DATE_MDY][self::TIME_24H] = 'D, n/d H:i';
        $keys['res_popup'][self::DATE_DMY][self::TIME_AMPM] = 'D, d/n g:i A';
        $keys['res_popup'][self::DATE_DMY][self::TIME_24H] = 'D, d/n H:i';

        $keys['res_popup_time'][self::DATE_MDY][self::TIME_AMPM] = 'g:i A';
        $keys['res_popup_time'][self::DATE_MDY][self::TIME_24H] = 'H:i';
        $keys['res_popup_time'][self::DATE_DMY][self::TIME_AMPM] = 'g:i A';
        $keys['res_popup_time'][self::DATE_DMY][self::TIME_24H] = 'H:i';

        $keys['short_reservation_date'][self::DATE_MDY][self::TIME_AMPM] = 'n/j/y g:i A';
        $keys['short_reservation_date'][self::DATE_MDY][self::TIME_24H] = 'n/j/y H:i';
        $keys['short_reservation_date'][self::DATE_DMY][self::TIME_AMPM] = 'j/n/y H:i';
        $keys['short_reservation_date'][self::DATE_DMY][self::TIME_24H] = 'j/n/y g:i A';

        $keys['dashboard'][self::DATE_MDY][self::TIME_AMPM] = 'D, n/d g:i A';
        $keys['dashboard'][self::DATE_MDY][self::TIME_24H] = 'D, n/d H:i';
        $keys['dashboard'][self::DATE_DMY][self::TIME_AMPM] = 'D, d/n g:i A';
        $keys['dashboard'][self::DATE_DMY][self::TIME_24H] = 'D, d/n H:i';

        $keys['period_time'][self::DATE_MDY][self::TIME_AMPM] = 'g:i A';
        $keys['period_time'][self::DATE_MDY][self::TIME_24H] = 'H:i';
        $keys['period_time'][self::DATE_DMY][self::TIME_AMPM] = 'g:i A';
        $keys['period_time'][self::DATE_DMY][self::TIME_24H] = 'H:i';

        $keys['timepicker'][self::DATE_MDY][self::TIME_AMPM] = 'g:i A';
        $keys['timepicker'][self::DATE_MDY][self::TIME_24H] = 'H:i';
        $keys['timepicker'][self::DATE_DMY][self::TIME_AMPM] = 'g:i A';
        $keys['timepicker'][self::DATE_DMY][self::TIME_24H] = 'H:i';

        $keys['mobile_reservation_date'][self::DATE_MDY][self::TIME_AMPM] = 'n/j g:i A';
        $keys['mobile_reservation_date'][self::DATE_MDY][self::TIME_24H] = 'n/j H:i';
        $keys['mobile_reservation_date'][self::DATE_DMY][self::TIME_AMPM] = 'j/n g:i A';
        $keys['mobile_reservation_date'][self::DATE_DMY][self::TIME_24H] = 'j/n H:i';

        $keys['general_date_js'][self::DATE_MDY][self::TIME_AMPM] = 'mm/dd/yy';
        $keys['general_date_js'][self::DATE_MDY][self::TIME_24H] = 'mm/dd/yy';
        $keys['general_date_js'][self::DATE_DMY][self::TIME_AMPM] = 'dd/mm/yy';
        $keys['general_date_js'][self::DATE_DMY][self::TIME_24H] = 'dd/mm/yy';

        $keys['general_time_js'][self::DATE_MDY][self::TIME_AMPM] = 'h:mm tt';
        $keys['general_time_js'][self::DATE_MDY][self::TIME_24H] = 'H:mm';
        $keys['general_time_js'][self::DATE_DMY][self::TIME_AMPM] = 'h:mm tt';
        $keys['general_time_js'][self::DATE_DMY][self::TIME_24H] = 'H:mm';

        $keys['timepicker_js'][self::DATE_MDY][self::TIME_AMPM] = 'h:i a';
        $keys['timepicker_js'][self::DATE_MDY][self::TIME_24H] = 'H:i';
        $keys['timepicker_js'][self::DATE_DMY][self::TIME_AMPM] = 'h:i a';
        $keys['timepicker_js'][self::DATE_DMY][self::TIME_24H] = 'H:i';

        $keys['momentjs_datetime'][self::DATE_MDY][self::TIME_AMPM] = 'M/D/YY h:mm A';
        $keys['momentjs_datetime'][self::DATE_MDY][self::TIME_24H] = 'M/D/YY H:mm';
        $keys['momentjs_datetime'][self::DATE_DMY][self::TIME_AMPM] = 'D/M/YY h:mm A';
        $keys['momentjs_datetime'][self::DATE_DMY][self::TIME_24H] = 'D/M/YY H:mm';

        $keys['calendar_time'][self::DATE_MDY][self::TIME_AMPM] = 'h:mma';
        $keys['calendar_time'][self::DATE_MDY][self::TIME_24H] = 'H:mm';
        $keys['calendar_time'][self::DATE_DMY][self::TIME_AMPM] = 'h:mma';
        $keys['calendar_time'][self::DATE_DMY][self::TIME_24H] = 'H:mm';

        $keys['calendar_dates'][self::DATE_MDY][self::TIME_AMPM] = 'M d';
        $keys['calendar_dates'][self::DATE_MDY][self::TIME_24H] = 'M d';
        $keys['calendar_dates'][self::DATE_DMY][self::TIME_AMPM] = 'd M';
        $keys['calendar_dates'][self::DATE_DMY][self::TIME_24H] = 'd M';

        $keys['embedded_date'][self::DATE_MDY][self::TIME_AMPM] = 'D d';
        $keys['embedded_date'][self::DATE_MDY][self::TIME_24H] = 'D d';
        $keys['embedded_date'][self::DATE_DMY][self::TIME_AMPM] = 'D d';
        $keys['embedded_date'][self::DATE_DMY][self::TIME_24H] = 'D d';

        $keys['embedded_time'][self::DATE_MDY][self::TIME_AMPM] = 'g:i A';
        $keys['embedded_time'][self::DATE_MDY][self::TIME_24H] = 'H:i';
        $keys['embedded_time'][self::DATE_DMY][self::TIME_AMPM] = 'g:i A';
        $keys['embedded_time'][self::DATE_DMY][self::TIME_24H] = 'H:i';

        $keys['embedded_datetime'][self::DATE_MDY][self::TIME_AMPM] = 'n/j g:i A';
        $keys['embedded_datetime'][self::DATE_MDY][self::TIME_24H] = 'n/j H:i';
        $keys['embedded_datetime'][self::DATE_DMY][self::TIME_AMPM] = 'j/n g:i A';
        $keys['embedded_datetime'][self::DATE_DMY][self::TIME_24H] = 'j/n H:i';

        $keys['report_date'][self::DATE_MDY][self::TIME_AMPM] = '%m/%d';
        $keys['report_date'][self::DATE_MDY][self::TIME_24H] = '%m/%d';
        $keys['report_date'][self::DATE_DMY][self::TIME_AMPM] = '%d/%m';
        $keys['report_date'][self::DATE_DMY][self::TIME_24H] = '%d/%m';

        $keys['react_date'][self::DATE_MDY][self::TIME_AMPM] = 'MM/dd/yyyy';
        $keys['react_date'][self::DATE_MDY][self::TIME_24H] = 'MM/dd/yyyy';
        $keys['react_date'][self::DATE_DMY][self::TIME_AMPM] = 'dd/MM/yyyy';
        $keys['react_date'][self::DATE_DMY][self::TIME_24H] = 'dd/MM/yyyy';

        $keys['react_time'][self::DATE_MDY][self::TIME_AMPM] = 'hh:mm aaa';
        $keys['react_time'][self::DATE_MDY][self::TIME_24H] = 'H:mm';
        $keys['react_time'][self::DATE_DMY][self::TIME_AMPM] = 'hh:mm aaa';
        $keys['react_time'][self::DATE_DMY][self::TIME_24H] = 'H:mm';

        $keys['react_datetime'][self::DATE_MDY][self::TIME_AMPM] = 'MM/dd/yyyy h:mm aaa';
        $keys['react_datetime'][self::DATE_MDY][self::TIME_24H] = 'MM/dd/yyyyH:mm';
        $keys['react_datetime'][self::DATE_DMY][self::TIME_AMPM] = 'dd/MM/yyyy h:mm aaa';
        $keys['react_datetime'][self::DATE_DMY][self::TIME_24H] = 'dd/MM/yyyy H:mm';

        $keys['monitor_date'][self::DATE_MDY][self::TIME_AMPM] = 'n/j/y';
        $keys['monitor_date'][self::DATE_MDY][self::TIME_24H] = 'n/j/y';
        $keys['monitor_date'][self::DATE_DMY][self::TIME_AMPM] = 'j/n/y';
        $keys['monitor_date'][self::DATE_DMY][self::TIME_24H] = 'j/n/y';

        $keys['monitor_time'][self::DATE_MDY][self::TIME_AMPM] = 'g:i A';
        $keys['monitor_time'][self::DATE_MDY][self::TIME_24H] = 'H:i';
        $keys['monitor_time'][self::DATE_DMY][self::TIME_AMPM] = 'g:i A';
        $keys['monitor_time'][self::DATE_DMY][self::TIME_24H] = 'H:i';

        $keys['monitor_event_date'][self::DATE_MDY][self::TIME_AMPM] = 'n/j';
        $keys['monitor_event_date'][self::DATE_MDY][self::TIME_24H] = 'n/j';
        $keys['monitor_event_date'][self::DATE_DMY][self::TIME_AMPM] = 'j/n';
        $keys['monitor_event_date'][self::DATE_DMY][self::TIME_24H] = 'j/n';

        $keys['monitor_event_time'][self::DATE_MDY][self::TIME_AMPM] = 'g:ia';
        $keys['monitor_event_time'][self::DATE_MDY][self::TIME_24H] = 'H:i';
        $keys['monitor_event_time'][self::DATE_DMY][self::TIME_AMPM] = 'g:ia';
        $keys['monitor_event_time'][self::DATE_DMY][self::TIME_24H] = 'H:i';

        $keys['sms_datetime'][self::DATE_MDY][self::TIME_AMPM] = 'n/j g:i a';
        $keys['sms_datetime'][self::DATE_MDY][self::TIME_24H] = 'n/j H:i';
        $keys['sms_datetime'][self::DATE_DMY][self::TIME_AMPM] = 'j/n g:i a';
        $keys['sms_datetime'][self::DATE_DMY][self::TIME_24H] = 'j/n H:i';

        return $keys[$key][$dateFormat][$timeFormat];
    }

    public function GeneralDateFormat($dateFormat = null, $timeFormat = null)
    {
        return $this->GetDateFormat(ResourceKeys::DATE_GENERAL, $dateFormat, $timeFormat);
    }

    public function GeneralDateTimeFormat($dateFormat = null, $timeFormat = null)
    {
        return $this->GetDateFormat(ResourceKeys::DATETIME_GENERAL, $dateFormat, $timeFormat);
    }

    public function ShortDateTimeFormat($dateFormat = null, $timeFormat = null)
    {
        return $this->GetDateFormat(ResourceKeys::DATETIME_SHORT, $dateFormat, $timeFormat);
    }

    public function SystemDateTimeFormat()
    {
        return $this->GetDateFormat(ResourceKeys::DATETIME_SYSTEM);
    }

    public function GetDays($key)
    {
        $days = $this->_lang->Days;

        if (!isset($days[$key]) || empty($days[$key])) {
            return '?';
        }

        return $days[$key];
    }

    public function GetMonths($key)
    {
        $months = $this->_lang->Months;

        if (!isset($months[$key]) || empty($months[$key])) {
            return '?';
        }

        return $months[$key];
    }

    /**
     * @param $languageCode
     * @return bool
     */
    private function SetCurrentLanguage($languageCode)
    {
        $languageCode = strtolower($languageCode . '');

        if ($languageCode == $this->CurrentLanguage) {
            return true;
        }

        if ($this->IsLanguageSupported($languageCode)) {
            $languageSettings = $this->AvailableLanguages[$languageCode];
            $this->LanguageFile = $languageSettings->LanguageFile;

            require_once($this->LanguageDirectory . $this->LanguageFile);

            $class = $languageSettings->LanguageClass;
            $this->_lang = new $class;
            $this->CurrentLanguage = $languageCode;
            $this->Charset = $this->_lang->Charset;
            $this->HtmlLang = $this->_lang->HtmlLang;
            $this->TextDirection = $this->_lang->TextDirection;

            setlocale(LC_ALL, $this->CurrentLanguage);

            return true;
        }

        return false;
    }

    private function GetLanguageCode()
    {
        $cookie = ServiceLocator::GetServer()->GetCookie(CookieKeys::LANGUAGE);
        if ($cookie != null) {
            return $cookie;
        } else {
            return Configuration::Instance()->GetKey(ConfigKeys::LANGUAGE);
        }
    }

    private function LoadAvailableLanguages()
    {
        $this->AvailableLanguages = AvailableLanguages::GetAvailableLanguages();
    }

    private function LoadOverrides()
    {
        $overrideFile = ROOT_DIR . 'config/lang-overrides.php';
        if (file_exists($overrideFile)) {
            global $langOverrides;
            include_once($overrideFile);
            $this->_lang->Strings = array_merge($this->_lang->Strings, $langOverrides);
        }
    }

    /**
     * @return string
     */
    public function CurrentLanguageJs()
    {
        if (str_contains($this->CurrentLanguage . '', "_")) {
            $parts = explode("_", $this->CurrentLanguage);
            return $parts[0] . "-" . strtoupper($parts[1]);
        }

        return $this->CurrentLanguage;
    }
}