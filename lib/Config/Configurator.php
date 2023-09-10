<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/external/pear/Config.php');

abstract class ConfigurationSetting
{
    public $Key;
    public $Value;
    public $Label;
    public $Section;

    public function Name()
    {
        return "setting[{$this->Section}|{$this->Key}]";
    }

    public abstract function Type();

    public function __construct($key, $value, $label, $section)
    {
        $this->Key = trim($key);
        $this->Value = trim($value . '');
        $this->Label = $key == $label ? $label : "{$key} - <i>{$label}</i>";
        $this->Section = $section . '';
    }

    public static function Create(string $key, string $value, ?string $section = null)
    {
        $textKeys = [
            ConfigKeys::APP_TITLE => "Application title",
            ConfigKeys::ADMIN_EMAIL => "Email address list for the core application administrators (comma separated)",
            ConfigKeys::ADMIN_EMAIL_NAME => "Name of administrator (used in emails)",
            ConfigKeys::NAME_FORMAT => "Format for user names. Tokens are {first} and {last}",
            ConfigKeys::HOME_URL => "URL to be taken to when clicking the logo",
            ConfigKeys::LOGOUT_URL => "URL to be taken to after logging out",
            ConfigKeys::HELP_URL => "Optional URL to use for alternative application help (https://www.bookedscheduler.com/help is used by default)",
            ConfigKeys::SCHEDULE_RESERVATION_LABEL => "The format of the label to use on the schedule view (see Help for available options)",
            ConfigKeys::ICS_SUBSCRIPTION_KEY => "A random key to allow iCalendar subscriptions",
            ConfigKeys::RESERVATION_START_REMINDER => "The default start reminder for reservations (ex 10 minutes, 2 hours, 6 days)",
            ConfigKeys::RESERVATION_END_REMINDER => "The default end reminder for reservations (ex 10 minutes, 2 hours, 6 days)",
            ConfigKeys::UPLOAD_RESERVATION_EXTENSIONS => "Allowed file extensions for reservation attachments (comma separated)",
            ConfigKeys::RECAPTCHA_PUBLIC_KEY => "reCAPTCHA public key or site key",
            ConfigKeys::RECAPTCHA_PRIVATE_KEY => "reCAPTCHA private key or secret key",
            ConfigKeys::DEFAULT_FROM_ADDRESS => "Default \"from\" email address for emails",
            ConfigKeys::DEFAULT_FROM_NAME => "Default \"from\" name for emails",
            ConfigKeys::RESERVATION_LABELS_MY_ICS_SUMMARY => "The format of the summary for owned iCalendar events (see Help for available options). Default is title.",
            ConfigKeys::RESERVATION_LABELS_ICS_SUMMARY => "The format of the summary for general iCalendar events (see Help for available options). Default is title.",
            ConfigKeys::RESERVATION_LABELS_MY_ICS_DESCRIPTION => "The format of the description for owned iCalendar events (see Help for available options). Default is description.",
            ConfigKeys::RESERVATION_LABELS_ICS_DESCRIPTION => "The format of the description for general iCalendar events (see Help for available options). Default is description.",
            ConfigKeys::RESERVATION_LABELS_MY_CALENDAR => "The format of the label reservations on My Calendar (see Help for available options)",
            ConfigKeys::RESERVATION_LABELS_RESOURCE_CALENDAR => "The format of the label reservations on Resource Calendar (see Help for available options)",
            ConfigKeys::RESERVATION_LABELS_RSS_DESCRIPTION => "The format of the description for RSS events (see Help for available options)",
            ConfigKeys::RESERVATION_LABELS_RESERVATION_POPUP => "The format of the reservation popup (see Help for available options)",
            ConfigKeys::GOOGLE_ANALYTICS_TRACKING_ID => "Google Analytics Tracking ID",
            ConfigKeys::AUTHENTICATION_REQUIRED_EMAIL_DOMAINS => "Restrict registration and login to a list of email domains (comma separated)",
            ConfigKeys::SLACK_TOKEN => "Slack token to enable Slack integration",
        ];

        $boolKeys = [
            ConfigKeys::ALLOW_REGISTRATION => "Allow users to register",
            ConfigKeys::ENABLE_EMAIL => "Enable application emails",
            ConfigKeys::REGISTRATION_ENABLE_CAPTCHA => "Use captcha on registration",
            ConfigKeys::REGISTRATION_REQUIRE_ACTIVATION => "Require new users to activate their account",
            ConfigKeys::REGISTRATION_AUTO_SUBSCRIBE_EMAIL => "Automatically enable reservation email notifications",
            ConfigKeys::REGISTRATION_NOTIFY => "Notify administrators of new user registrations",
            ConfigKeys::DISABLE_PASSWORD_RESET => "Disable the ability for users to reset their password",
            ConfigKeys::SCHEDULE_PER_USER_COLORS => "Enable the ability to set colors for each user",
            ConfigKeys::SCHEDULE_SHOW_INACCESSIBLE_RESOURCES => "Show resources that the user does not have permission for",
            ConfigKeys::SCHEDULE_HIDE_BLOCKED_PERIODS => "Hide blocked schedule slots",
            ConfigKeys::SCHEDULE_SHOW_WEEK_NUMBERS => "Show week numbers for the schedule and calendar views",
            ConfigKeys::SCHEDULE_SHOW_CHECKIN => "Show icons indicating the reservation check in/out status",
            ConfigKeys::PRIVACY_VIEW_SCHEDULES => "Allow unauthenticated people to view the schedule",
            ConfigKeys::PRIVACY_VIEW_RESERVATIONS => "Allow unauthenticated people to view reservation details",
            ConfigKeys::PRIVACY_HIDE_USER_DETAILS => "Hide user details from non-administrative users",
            ConfigKeys::PRIVACY_ALWAYS_SHOW_USER_NAME => "If hiding user details, force the user first and last name to be shown",
            ConfigKeys::PRIVACY_HIDE_RESERVATION_DETAILS => "Hide reservation details from authenticated, non-administrative users",
            ConfigKeys::PRIVACY_ALLOW_GUEST_BOOKING => "Allow unauthenticated guests to book reservations",
            ConfigKeys::RESERVATION_UPDATES_REQUIRE_APPROVAL => "Updates to reservations require approval (if resource requires approval)",
            ConfigKeys::RESERVATION_PREVENT_PARTICIPATION => "Prevent reservation participation",
            ConfigKeys::RESERVATION_PREVENT_RECURRENCE => "Prevent reservation recurrence",
            ConfigKeys::RESERVATION_REMINDERS_ENABLED => "Allow reservation reminders",
            ConfigKeys::RESERVATION_ALLOW_GUESTS => "Allow guests to be invited to reservations",
            ConfigKeys::RESERVATION_ALLOW_WAITLIST => "Allow users to add themselves to a reservation availability waitlist",
            ConfigKeys::RESERVATION_TITLE_REQUIRED => "Require reservation title",
            ConfigKeys::RESERVATION_DESCRIPTION_REQUIRED => "Require reservation description",
            ConfigKeys::RESERVATION_DELETE_REASON_REQUIRED => "Require reason for reservation cancellation",
            ConfigKeys::RESERVATION_ALLOW_MEETING_LINKS => "Allow a meeting link to be added to reservations",
            ConfigKeys::RESERVATION_DEFAULT_ALLOW_PARTICIPATION_JOIN => "Default value for allowing participants to join reservations",
            ConfigKeys::NOTIFY_APPROVAL_APPLICATION_ADMINS => "Notify application administrators when reservations require approval",
            ConfigKeys::NOTIFY_APPROVAL_GROUP_ADMINS => "Notify group administrators when their group reservations require approval",
            ConfigKeys::NOTIFY_APPROVAL_RESOURCE_ADMINS => "Notify resource and schedule administrators when their resource reservations require approval",
            ConfigKeys::NOTIFY_CREATE_APPLICATION_ADMINS => "Notify application administrators when reservations are created",
            ConfigKeys::NOTIFY_CREATE_GROUP_ADMINS => "Notify group administrators when reservations are created by anyone in their group",
            ConfigKeys::NOTIFY_CREATE_RESOURCE_ADMINS => "Notify resource and schedule administrators when reservations are created on their resources",
            ConfigKeys::NOTIFY_DELETE_APPLICATION_ADMINS => "Notify application administrators when reservations are deleted",
            ConfigKeys::NOTIFY_DELETE_GROUP_ADMINS => "Notify group administrators when reservations are deleted by anyone in their group",
            ConfigKeys::NOTIFY_DELETE_RESOURCE_ADMINS => "Notify resource and schedule administrators their resource reservations are deleted",
            ConfigKeys::NOTIFY_UPDATE_APPLICATION_ADMINS => "Notify application administrators when reservations are updated",
            ConfigKeys::NOTIFY_UPDATE_GROUP_ADMINS => "Notify group administrators when reservations are updated by anyone in their group",
            ConfigKeys::NOTIFY_UPDATE_RESOURCE_ADMINS => "Notify resource and schedule administrators their resource reservations are updated",
            ConfigKeys::NOTIFY_MISSED_CHECKIN_APPLICATION_ADMINS => "Notify application administrators if a reservation missed the check in time",
            ConfigKeys::NOTIFY_MISSED_CHECKIN_GROUP_ADMINS=> "Notify group administrators if a reservation missed the check in time",
            ConfigKeys::NOTIFY_MISSED_CHECKIN_RESOURCE_ADMINS=> "Notify resource and schedule administrators if a reservation missed the check in time",
            ConfigKeys::NOTIFY_MISSED_CHECKOUT_APPLICATION_ADMINS=> "Notify resource and schedule administrators if a reservation missed the check out time",
            ConfigKeys::NOTIFY_MISSED_CHECKOUT_RESOURCE_ADMINS=> "Notify group administrators if a reservation missed the check out time",
            ConfigKeys::NOTIFY_MISSED_CHECKOUT_GROUP_ADMINS=> "Notify resource and schedule administrators if a reservation missed the check out time",
            ConfigKeys::UPLOAD_ENABLE_RESERVATION_ATTACHMENTS => "Allow file attachments for reservations",
            ConfigKeys::REPORTS_ALLOW_ALL => "Allow all users to access reports",
            ConfigKeys::REPORTS_RESTRICT_TO_ADMINS => "Allow all levels of administrators to access reports",
            ConfigKeys::PASSWORD_UPPER_AND_LOWER => "Require passwords to contain both uppercase and lowercase characters",
            ConfigKeys::AUTHENTICATION_ALLOW_FACEBOOK => "Allow users to login using their Facebook account",
            ConfigKeys::AUTHENTICATION_ALLOW_GOOGLE => "Allow users to login using their Google account",
            ConfigKeys::AUTHENTICATION_HIDE_BOOKED_LOGIN_PROMPT => "Hide the username and password prompt on the login page",
            ConfigKeys::AUTHENTICATION_CAPTCHA_ON_LOGIN => "Show captcha on the login page",
            ConfigKeys::CREDITS_ALLOW_PURCHASE => "Allow purchasing of reservation credits",
            ConfigKeys::TABLET_VIEW_AUTOCOMPLETE => "Automatically suggest emails from registered users",
            ConfigKeys::REGISTRATION_REQUIRE_ORGANIZATION => "Require organization on registration",
            ConfigKeys::REGISTRATION_REQUIRE_PHONE => "Require phone number on registration",
            ConfigKeys::REGISTRATION_REQUIRE_POSITION => "Require position number on registration",
            ConfigKeys::REGISTRATION_LOCK_TIMEZONE => "Lock the timezone to the default configured timezone",
            ConfigKeys::REGISTRATION_DISABLE_PROFILE_UPDATES => "Prevent users from updating their own profile",
            ConfigKeys::RESERVATION_SHOW_DETAILED_SAVE_RESPONSE => "Whether or not to show resources and dates on the reservation save notice",
            ConfigKeys::RESERVATION_HIDE_EMAIL => "Whether or not hide user email addresses on the reservation page",
            ConfigKeys::RESERVATION_LIMIT_INVITEES_TO_MAX_PARTICIPANTS => "Whether or not to limit the number of invitees to the maximum capacity of a reservation",
            ConfigKeys::MAPS_ENABLED => "Use resource maps or floor plans to view resource availability",
            ConfigKeys::REGISTRATION_BYPASS_FIRST_LOGIN_SCREEN => "Skip the first login welcome screen for new users",
            ConfigKeys::AUTHENTICATION_ALLOW_OAUTH => "If custom OAuth providers are allowed for authentication",
        ];

        $numberKeys = [
            ConfigKeys::SCHEDULE_UPDATE_HIGHLIGHT_MINUTES => "Number of minutes past a reservation being added or updated to show the new/updated label (0 will not show the label)",
            ConfigKeys::ICS_FUTURE_DAYS => "Number of days to return for iCalendar subscriptions (max 90, default 30)",
            ConfigKeys::ICS_PAST_DAYS => "Number of days to return for iCalendar subscriptions (max 30, default 0)",
            ConfigKeys::RESERVATION_CHECKIN_MINUTES => "If using reservation check in, the number of minutes prior to a reservation starting to allow check in",
            ConfigKeys::RESERVATION_TOTAL_USER_CONCURRENT_LIMIT => "The total number of concurrent reservations allowed per user",
            ConfigKeys::PASSWORD_LETTERS => "Minimum number of letters for user passwords",
            ConfigKeys::PASSWORD_NUMBERS => "Minimum number of numbers for user passwords",
            ConfigKeys::RESERVATION_MAXIMUM_RESOURCE_CHECKLIST => "The maximum number of resources to show as a checklist when making a reservation. More resources will change to a filterable dropdown. The default is 30.",
            ConfigKeys::INACTIVITY_TIMEOUT => "Number of minutes before an inactive user is logged out. By default, the standard server timeout is used.",
        ];

        if (in_array($key, array_keys($textKeys))) {
            return new ConfigurationSettingText($key, $value, $textKeys[$key], $section);
        }

        if (in_array($key, array_keys($boolKeys))) {
            return new ConfigurationSettingBool($key, $value, $boolKeys[$key], $section);
        }

        if (in_array($key, array_keys($numberKeys))) {
            return new ConfigurationSettingNumber($key, $value, $numberKeys[$key], $section);
        }

        if ($key == ConfigKeys::DEFAULT_TIMEZONE) {
            $options = [];
            foreach ($GLOBALS['APP_TIMEZONES'] as $tz) {
                $options[] = ['value' => $tz, 'text' => $tz];
            }

            return new ConfigurationSettingSelect($key, $value, $options, 'Default timezone for new users', $section);
        }

        if ($key == ConfigKeys::LANGUAGE) {
            $options = [];
            foreach (Resources::GetInstance()->AvailableLanguages as $lang) {
                $options[] = ['value' => $lang->GetLanguageCode(), 'text' => $lang->GetDisplayName()];
            }

            return new ConfigurationSettingSelect($key, $value, $options, 'Default language for new users', $section);
        }

        if ($key == ConfigKeys::DEFAULT_HOMEPAGE) {
            $options = [];
            $pages = Pages::GetAvailablePages();
            foreach ($pages as $pageid => $page) {
                $options[] = ['value' => $pageid, 'text' => Resources::GetInstance()->GetString($page['name'])];
            }
            return new ConfigurationSettingSelect($key, $value, $options, 'Default homepage for new users', $section);
        }

        if ($key == ConfigKeys::RESERVATION_START_TIME_CONSTRAINT) {
            $options = [
                ['value' => 'future', 'text' => 'Only allow future times to be booked and future reservations to be modified'],
                ['value' => 'current', 'text' => 'Allow current slots to be booked and ongoing reservations to be modified'],
                ['value' => 'none', 'text' => 'Allow past, present, and future slots and reservations to be created and modified'],
            ];
            return new ConfigurationSettingSelect($key, $value, $options, 'Limit reservation activity (create, update, delete)', $section);
        }

        if ($key == ConfigKeys::API_ENABLED && $section == ConfigSection::API) {
            return new ConfigurationSettingBool($key, $value, "Enable Booked API", $section);
        }

        if ($key == ConfigKeys::RECAPTCHA_ENABLED && $section == ConfigSection::RECAPTCHA) {
            return new ConfigurationSettingBool($key, $value, "Enable reCaptcha", $section);
        }

        if ($key == ConfigKeys::CREDITS_ENABLED && $section == ConfigSection::CREDITS) {
            return new ConfigurationSettingBool($key, $value, "Enable reservation credits", $section);
        }

        if ($key == ConfigKeys::MFA_TYPE && $section == ConfigSection::MFA) {
            $options = [
                ['value' => '', 'text' => 'Disabled'],
                ['value' => 'email', 'text' => 'Email (One Time Passcode)'],
            ];
            return new ConfigurationSettingSelect($key, $value, $options, 'Enable Multi-Factor Authentication', $section);
        }

        if ($key == ConfigKeys::RECAPTCHA_VERSION) {
            $options = [
                ['value' => '2', 'text' => 'v2'],
                ['value' => '3', 'text' => 'v3'],
            ];
            return new ConfigurationSettingSelect($key, $value, $options, 'Version of reCAPTCHA to use. Default is v2', $section);
        }

        if ($key == ConfigKeys::FIRST_DAY_OF_WEEK) {
            $options = [
                ['value' => '0', 'text' => 'Sunday'],
                ['value' => '1', 'text' => 'Monday'],
            ];
            return new ConfigurationSettingSelect($key, $value, $options, 'First day of the week', $section);
        }

        if ($key == ConfigKeys::LOGGING_LEVEL) {
            $options = [
                ['value' => 'debug', 'text' => 'Debug (Diagnostics, informational and troubleshooting)'],
                ['value' => 'error', 'text' => 'Error (Errors and problems)'],
            ];

            return new ConfigurationSettingSelect($key, $value, $options, 'Logging level (defaults to Error)', $section);
        }

        return null;
    }
}

class ConfigurationSettingText extends ConfigurationSetting
{
    public function Type()
    {
        return "text";
    }
}

class ConfigurationSettingBool extends ConfigurationSetting
{
    public function Type()
    {
        return "bool";
    }
}

class ConfigurationSettingNumber extends ConfigurationSetting
{
    public function Type()
    {
        return "number";
    }
}

class ConfigurationSettingSelect extends ConfigurationSetting
{
    public function Type()
    {
        return "select";
    }

    public $Options;

    public function __construct($key, $value, $options, $label, $section)
    {
        parent::__construct($key, $value, $label, $section);
        $this->Options = $options;
    }
}

interface IConfigurationSettings
{
    /**
     * @param string $file
     * @return array
     */
    public function GetSettings($file);

    /**
     * @param array $currentSettings
     * @param array $newSettings
     * @param bool $removeMissingKeys
     * @return array
     */
    public function BuildConfig($currentSettings, $newSettings, $removeMissingKeys = false);

    /**
     * @param string $configFilePath
     * @param array $mergedSettings
     */
    public function WriteSettings($configFilePath, $mergedSettings);

    /**
     * @param string $configFilePath
     * @return bool
     */
    public function CanOverwriteFile($configFilePath);
}

class Configurator implements IConfigurationSettings
{
    /**
     * @param string $configPhp
     * @param string $distPhp
     */
    public function Merge($configPhp, $distPhp)
    {
        if ($this->IsConfigOutOfDate($configPhp, $distPhp)) {
            $mergedSettings = $this->GetMerged($configPhp, $distPhp);

            $this->WriteSettings($configPhp, $mergedSettings);
        }
    }

    public function WriteSettings($configFilePath, $mergedSettings)
    {
        $this->CreateBackup($configFilePath);
        if (!array_key_exists(Configuration::SETTINGS, $mergedSettings)) {
            $mergedSettings = array(Configuration::SETTINGS => $mergedSettings);
        }
        $config = new Config();
        $config->parseConfig($mergedSettings, 'PHPArray');
        $config->writeConfig($configFilePath, 'PHPArray', $mergedSettings);

//        $this->AddErrorReporting($configFilePath);
    }

    /**
     * @param string $configPhp
     * @param string $distPhp
     * @return string[]
     */
    private function GetMerged($configPhp, $distPhp)
    {
        $currentSettings = $this->GetSettings($configPhp);
        $newSettings = $this->GetSettings($distPhp);

        $settings = $this->BuildConfig($currentSettings, $newSettings, true);
        return array(Configuration::SETTINGS => $settings);
    }

    public function GetMergedString($configPhp, $distPhp)
    {
        $settings = $this->GetMerged($configPhp, $distPhp);
        $config = new Config();
        $parsed = $config->parseConfig($settings, 'PHPArray');

        return $parsed->toString('PHPArray');
    }

    public function GetSettings($file)
    {
        $config = new Config();
        /** @var $current Config_Container */
        $current = $config->parseConfig($file, 'PHPArray');

        $currentValues = $current->getItem("section", Configuration::SETTINGS)->toArray();

        return $currentValues[Configuration::SETTINGS];
    }

    public function BuildConfig($currentSettings, $newSettings, $keepMissingKeys = false)
    {
        foreach ($currentSettings as $key => $value) {
            if (array_key_exists($key, $newSettings)) {
                if (is_array($value)) {
                    $newSettings[$key] = array_merge($newSettings[$key], $value);
                } else {
                    $newSettings[$key] = $value;
                }
            } else {
                Log::Debug("$key not found");
                if ($keepMissingKeys) {
                    $newSettings[$key] = $value;
                }
            }
        }

        return $newSettings;
    }

    private function AddErrorReporting($file)
    {
        $pathinfo = pathinfo($file);
        if ($pathinfo['dirname'] != ROOT_DIR . 'config') {
            return;
        }
        $contents = file_get_contents($file);
        $new = str_replace("<?php", "<?php\r\nerror_reporting( E_ALL & ~E_DEPRECATED & ~E_STRICT);\r\nini_set('display_errors', 0);\r\nini_set('display_startup_errors', 0);\r\n", $contents);

        file_put_contents($file, $new);
    }

    public function CanOverwriteFile($configFile)
    {
        if (!is_writable($configFile)) {
            return chmod($configFile, 0770);
        }

        return true;
    }

    private function CreateBackup($configFilePath)
    {
        $backupPath = str_replace('.php', time() . '.php', $configFilePath);
        copy($configFilePath, $backupPath);
    }

    private function IsConfigOutOfDate($configPhp, $distPhp)
    {
        $currentSettings = $this->GetSettings($configPhp);
        $newSettings = $this->GetSettings($distPhp);

        if ($this->AreKeysTheSame($currentSettings, $newSettings)) {
            Log::Debug('Config file is already up to date. Skipping config merge.');
            return false;
        }

        Log::Debug('Config file is out of date. Merging new config options in.');
        return true;
    }

    private function AreKeysTheSame($current, $new)
    {
        foreach ($new as $key => $val) {
            if (!array_key_exists($key, $current) || (is_array($new[$key]) && is_array($current[$key]) && !$this->AreKeysTheSame($current[$key], $new[$key]))) {
                Log::Debug('Could not find key in config file', ['key' => $key]);
                return false;
            }
        }

        return true;
    }
}
