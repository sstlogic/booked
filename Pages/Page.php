<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/IPage.php');
require_once(ROOT_DIR . 'Pages/Pages.php');
require_once(ROOT_DIR . 'Pages/CustomFileCache.php');
require_once(ROOT_DIR . 'Pages/UrlPaths.php');
require_once(ROOT_DIR . 'lib/Common/namespace.php');
require_once(ROOT_DIR . 'lib/Server/namespace.php');
require_once(ROOT_DIR . 'lib/Config/namespace.php');
require_once(ROOT_DIR . 'lib/external/MobileDetect/Mobile_Detect.php');

abstract class Page implements IPage
{
    /**
     * @var SmartyPage
     */
    protected $smarty = null;

    /**
     * @var Server
     */
    protected $server = null;
    protected $path;

    protected $IsMobile = false;
    protected $IsTablet = false;
    protected $IsDesktop = true;

    protected function __construct($titleKey = '', $pageDepth = 0)
    {
        ExceptionHandler::SetExceptionHandler(new WebExceptionHandler(array($this, 'RedirectToError')));

        $this->SetSecurityHeaders();

        $this->path = str_repeat('../', $pageDepth);
        $this->server = ServiceLocator::GetServer();
        $resources = Resources::GetInstance();
        $configuration = Configuration::Instance();

        $this->smarty = new SmartyPage($resources, $this->path);

        $userSession = ServiceLocator::GetServer()->GetUserSession();
        $this->smarty->assign('Timezone', $userSession->Timezone);
        $this->smarty->assign('Charset', $resources->Charset);
        $this->smarty->assign('CurrentLanguage', $resources->CurrentLanguage);
        $this->smarty->assign('CurrentLanguageJs', $resources->CurrentLanguageJs());
        $this->smarty->assign('HtmlLang', $resources->HtmlLang);
        $this->smarty->assign('HtmlTextDirection', $resources->TextDirection);
        $appTitle = $configuration->GetKey(ConfigKeys::APP_TITLE);
        $pageTile = $resources->GetString($titleKey);
        $this->smarty->assign('Title', (empty($appTitle) ? 'Booked' : $appTitle) . (empty($pageTile) ? '' : ' - ' . $pageTile));
        $this->smarty->assign('AppTitle', (empty($appTitle) ? 'Booked' : $appTitle));
        $this->smarty->assign('CalendarJSFile', $resources->CalendarLanguageFile);

        $this->smarty->assign('LoggedIn', $userSession->IsLoggedIn());
        $this->smarty->assign('Version', Configuration::VERSION);
        $this->smarty->assign('Path', $this->path);
        $this->smarty->assign('ScriptUrl', $configuration->GetScriptUrl());
        $this->smarty->assign('UploadsUrl', $configuration->GetScriptUrl() . '/' . $configuration->GetKey(ConfigKeys::IMAGE_UPLOAD_URL));
        $this->smarty->assign('UserName', !is_null($userSession) ? $userSession->FirstName : '');
        $this->smarty->assign('DisplayWelcome', $this->DisplayWelcome());
        $this->smarty->assign('UserId', $userSession->UserId);
        $this->smarty->assign('CanViewAdmin', $userSession->IsAdmin);
        $this->smarty->assign('CanViewGroupAdmin', $userSession->IsGroupAdmin);
        $this->smarty->assign('CanViewResourceAdmin', $userSession->IsResourceAdmin);
        $this->smarty->assign('CanViewScheduleAdmin', $userSession->IsScheduleAdmin);
        $this->smarty->assign('CanViewResponsibilities', !$userSession->IsAdmin && ($userSession->IsGroupAdmin || $userSession->IsResourceAdmin || $userSession->IsScheduleAdmin));
        $allowAllUsersToReports = $configuration->GetSectionKey(ConfigSection::REPORTS, ConfigKeys::REPORTS_ALLOW_ALL, new BooleanConverter());
        $restrictReportsToAdmins = $configuration->GetSectionKey(ConfigSection::REPORTS, ConfigKeys::REPORTS_RESTRICT_TO_ADMINS, new BooleanConverter());
        $this->smarty->assign('CanViewReports', ($allowAllUsersToReports || $userSession->IsAdmin || (!$restrictReportsToAdmins && ($userSession->IsGroupAdmin || $userSession->IsResourceAdmin || $userSession->IsScheduleAdmin))));
        $timeout = $configuration->GetKey(ConfigKeys::INACTIVITY_TIMEOUT);
        if (!empty($timeout)) {
            $this->smarty->assign('SessionTimeoutSeconds', max($timeout, 1) * 60);
        }
        $this->smarty->assign('ShouldLogout', $this->GetShouldAutoLogout());
        $this->smarty->assign('UseLocalJquery', $configuration->GetKey(ConfigKeys::USE_LOCAL_JS, new BooleanConverter()));
        $this->smarty->assign('EnableConfigurationPage', $configuration->GetSectionKey(ConfigSection::PAGES, ConfigKeys::PAGES_ENABLE_CONFIGURATION, new BooleanConverter()));
        $this->smarty->assign('ShowParticipation', !$configuration->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_PREVENT_PARTICIPATION, new BooleanConverter()));
        $this->smarty->assign('CreditsEnabled', $configuration->GetSectionKey(ConfigSection::CREDITS, ConfigKeys::CREDITS_ENABLED, new BooleanConverter()));
        $this->smarty->assign('PaymentsEnabled', $configuration->GetSectionKey(ConfigSection::CREDITS, ConfigKeys::CREDITS_ALLOW_PURCHASE, new BooleanConverter()));
        $this->smarty->assign('EmailEnabled', $configuration->GetKey(ConfigKeys::ENABLE_EMAIL, new BooleanConverter()));
        $this->smarty->assign('WaitlistEnabled', $configuration->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_ALLOW_WAITLIST, new BooleanConverter()));
        $this->smarty->assign('MapsEnabled', $configuration->GetSectionKey(ConfigSection::MAPS, ConfigKeys::MAPS_ENABLED, new BooleanConverter()));
        $this->smarty->assign('EnableOAuth', $configuration->GetSectionKey(ConfigSection::AUTHENTICATION, ConfigKeys::AUTHENTICATION_ALLOW_OAUTH, new BooleanConverter()));
        $this->smarty->assign('FirstWeekday', $configuration->GetKey(ConfigKeys::FIRST_DAY_OF_WEEK, new IntConverter()));

        $this->smarty->assign('LogoUrl', 'booked.png');
        $version = Configuration::VERSION;
        if (file_exists($this->path . 'img/custom-logo.png')) {
            $cache = $this->GetCustomFileCache();
            $this->smarty->assign('LogoUrl', "custom-logo.png?q={$cache->logo}&v={$version}");
        }
        if (file_exists($this->path . 'img/custom-logo.gif')) {
            $cache = $this->GetCustomFileCache();
            $this->smarty->assign('LogoUrl', "custom-logo.gif?q={$cache->logo}&v={$version}");
        }
        if (file_exists($this->path . 'img/custom-logo.jpg')) {
            $cache = $this->GetCustomFileCache();
            $this->smarty->assign('LogoUrl', "custom-logo.jpg?q={$cache->logo}&v={$version}");
        }

        $this->smarty->assign('CssUrl', '');
        if (file_exists($this->path . 'css/custom-style.css')) {
            $cache = $this->GetCustomFileCache();
            $this->smarty->assign('CssUrl', "custom-style.css?q={$cache->css}");
        }

        $this->smarty->assign('FaviconUrl', 'favicon.png');
        if (file_exists($this->path . 'custom-favicon.png')) {
            $cache = $this->GetCustomFileCache();
            $this->smarty->assign('FaviconUrl', "custom-favicon.png?q={$cache->favicon}");
        }
        if (file_exists($this->path . 'custom-favicon.gif')) {
            $cache = $this->GetCustomFileCache();
            $this->smarty->assign('FaviconUrl', "custom-favicon.gif?q={$cache->favicon}");
        }
        if (file_exists($this->path . 'custom-favicon.jpg')) {
            $cache = $this->GetCustomFileCache();
            $this->smarty->assign('FaviconUrl', "custom-favicon.jpg?q={$cache->favicon}");
        }
        if (file_exists($this->path . 'custom-favicon.ico')) {
            $cache = $this->GetCustomFileCache();
            $this->smarty->assign('FaviconUrl', "custom-favicon.ico?q={$cache->favicon}");
        }

        $logoUrl = $configuration->GetKey(ConfigKeys::HOME_URL);
        if (empty($logoUrl)) {
            $logoUrl = $this->path . Pages::UrlFromId($userSession->HomepageId);
        }
        $this->smarty->assign('HomeUrl', $logoUrl);

        $detect = new Mobile_Detect();
        $this->IsMobile = $detect->isMobile();
        $this->IsTablet = $detect->isTablet();
        $this->IsDesktop = !$this->IsMobile && !$this->IsTablet;
        $this->Set('IsMobile', $this->IsMobile);
        $this->Set('IsTablet', $this->IsTablet);
        $this->Set('IsDesktop', $this->IsDesktop);
        $this->Set('GoogleAnalyticsTrackingId', $configuration->GetSectionKey(ConfigSection::GOOGLE_ANALYTICS, ConfigKeys::GOOGLE_ANALYTICS_TRACKING_ID));
        $this->Set('ShowNewVersion', $this->ShouldShowNewVersion());
        $this->Set('CSRFToken', $userSession->CSRFToken);
        $this->Set('ShowInvalidScriptUrl', !BookedStringHelper::Contains($this->server->GetHostAndUri(), preg_replace("/(^\w+:|^)\/\//", '', $configuration->GetScriptUrl())));
        $helpUrl = $configuration->GetKey(ConfigKeys::HELP_URL);
        $this->Set('NavHelpUrl', empty($helpUrl) ? 'https://www.bookedscheduler.com/help' : $helpUrl);
        $this->Set('FirstDayOfWeek', Configuration::Instance()->GetKey(ConfigKeys::FIRST_DAY_OF_WEEK, new IntConverter()));

        $fullCalendarLocales = ['af','ar-dz','ar-kw','ar-ly','ar-ma','ar-sa','ar-tn','ar','az','bg','bn','bs','ca','cs','cy','da','de-at','de','el','en-au','en-gb','en-nz','eo','es-us','es','et','eu','fa','fi','fr-ca','fr-ch','fr','gl','he','hi','hr','hu','hy-am','id','is','it','ja','ka','kk','km','ko','ku','lb','lt','lv','mk','ms','nb','ne','nl','nn','pl','pt-br','pt','ro','ru','si-lk','sk','sl','sm','sq','sr-cyrl','sr','sv','ta-in','th','tr','ug','uk','uz','vi','zh-cn','zh-tw'];
        $htmlLang = strtolower($resources->HtmlLang . '');
        if (in_array($htmlLang, $fullCalendarLocales)) {
            $this->Set('FullCalendarLocale', $htmlLang);
        }
        else if (in_array(substr($htmlLang, 0, 2), $fullCalendarLocales)) {
            $this->Set('FullCalendarLocale', substr($htmlLang, 0, 2));
        }
        else {
            $this->Set('FullCalendarLocale','en');
        }
        // header defaults
        $this->Set('UsingReact', false);
        $this->Set('ForceJquery', false);
        $this->Set('Qtip', false);
        $this->Set('Select2', false);
        $this->Set('Fullcalendar', false);
        $this->Set('Owl', false);
        $this->Set('HideNavBar', false);
        $this->Set('cssFiles', "");
        $this->Set('printCssFiles', "");
        $this->Set('HideLogo', false);
        $this->Set('Autocomplete', false);
        $this->Set('Moment', false);
        $this->Set('ShowScheduleLink', false);
        $this->Set('TitleKey', '');
        $this->Set('TitleArgs', '');
        $this->Set('NoGutter', false);
    }

    protected function SetTitle($title)
    {
        $this->smarty->assign('Title', $title);
    }

    public function Redirect($url)
    {
        if (!BookedStringHelper::StartsWith($url, $this->path) && !BookedStringHelper::StartsWith($url, "/")) {
            $url = $this->path . $url;
        }
        $url = str_replace('&amp;', '&', $url);
        $url = str_replace('(', '', $url);
        $url = str_replace(')', '', $url);
        $url = str_replace(';', '', $url);
        $url = str_replace('%28', '', $url);
        $url = str_replace('%29', '', $url);
        $url = str_replace('%3B', '', $url);
        $url = self::CleanRedirect($url);
        header("Location: $url");
        die();
    }

    public function RedirectUnsafe($url)
    {
        header("Location: $url");
        die();
    }

    public function RedirectResume($url)
    {
        if (!BookedStringHelper::StartsWith($url, $this->path)) {
            $url = $this->path . $url;
        }
        $url = self::CleanRedirect($url);

        header("Location: $url");
        die();
    }

    /**
     * @param string $redirect
     * @return string
     */
    public static function CleanRedirect($redirect)
    {
        if (empty($redirect)) {
            return "";
        }

        $result = preg_match("/^(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})$/", $redirect, $matches);

        if ($result === false || count($matches) === 0) {
            return $redirect;
        }

        $result = preg_match("/^(https?)|(www\.)/", $matches[0], $containsProtocol);
        if ($result === false || count($containsProtocol) === 0) {
            return $redirect;
        }

        if (!BookedStringHelper::StartsWith($matches[0], Configuration::Instance()->GetScriptUrl())) {
            return "";
        }

        return $matches[0];

    }

    public function RedirectToError($errorMessageId = ErrorMessages::UNKNOWN_ERROR, $lastPage = '')
    {
        $errorMessageKey = ErrorMessages::Instance()->GetResourceKey($errorMessageId);
        $this->Set('ErrorMessage', $errorMessageKey);
        $this->Set('TitleKey', 'Error');
        $this->Display('error.tpl');
        die();
    }

    public function GetLastPage($defaultPage = '')
    {
        $referer = getenv("HTTP_REFERER");
        $scriptUrl = Configuration::Instance()->GetScriptUrl();
        if (empty($referer) || !BookedStringHelper::Contains($referer, $scriptUrl)) {
            return empty($defaultPage) ? "$scriptUrl/" . Pages::LOGIN : "$scriptUrl/" . $defaultPage;
        }

        return $referer;

//        $page = str_ireplace($scriptUrl, '', $referer);
//        return ltrim($page, '/');
    }

    public function DisplayWelcome()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function IsAuthenticated()
    {
        return !is_null($this->server->GetUserSession()) && $this->server->GetUserSession()->IsLoggedIn();
    }

    /**
     * @return bool
     */
    public function IsPostBack()
    {
        return !empty($_POST);
    }

    /**
     * @param int|string $validatorId
     * @param IValidator $validator
     */
    public function RegisterValidator($validatorId, $validator)
    {
        $this->smarty->Validators->Register($validatorId, $validator);
    }

    /**
     * @return bool
     */
    public function IsValid()
    {
        return $this->smarty->IsValid();
    }

    /**
     * @param string $var
     * @param string|object|array $value
     * @return void
     */
    public function Set($var, $value)
    {
        $this->smarty->assign($var, $value);
    }

    public function EnforceCSRFCheck()
    {
        $session = $this->server->GetUserSession();
        if (!$session->IsLoggedIn()) {
            return;
        }
        $token = $this->GetForm(FormKeys::CSRF_TOKEN);
        $session = $this->server->GetUserSession();
        if ($this->IsPost() && (empty($token) || $token != $session->CSRFToken)) {
            Log::Error('Possible CSRF attack', ['submittedToken' => $token, 'expectedToken' => $session->CSRFToken]);
            http_response_code(500);
            die('Insecure request');
        }
    }

    public function GetSortField()
    {
        return $this->GetQuerystring(QueryStringKeys::SORT_FIELD);
    }

    public function GetSortDirection()
    {
        return $this->GetQuerystring(QueryStringKeys::SORT_DIRECTION);
    }

    /**
     * @param string $var
     * @return string
     */
    protected function GetVar($var)
    {
        return $this->smarty->getTemplateVars($var);
    }

    /**
     * @param string $var
     * @return null|string|array
     */
    protected function GetForm($var, $forceArray = false)
    {
        $val = $this->server->GetForm($var);
        if (!$forceArray) {
            return $val;
        }

        if (empty($val)) {
            return [];
        }
        if (!is_array($val)) {
            return [$val];
        }

        return $val;
    }

    /**
     * @param string $var
     * @return bool
     */
    protected function GetCheckbox($var)
    {
        $val = $this->server->GetForm($var);
        return !empty($val);
    }

    /**
     * @param string $var
     * @return null
     */
    protected function GetRawForm($var)
    {
        return $this->server->GetRawForm($var);
    }

    /**
     * @param string $key
     * @param bool $forceArray
     * @return null|string|array
     */
    protected function GetQuerystring($key, $forceArray = false)
    {
        $val = $this->server->GetQuerystring($key);
        if (!$forceArray) {
            return $val;
        }

        if (empty($val)) {
            return [];
        }
        if (!is_array($val)) {
            return [$val];
        }

        return $val;
    }

    /**
     * @param string $key
     * @return null|UploadedFile
     */
    protected function GetFile($key)
    {
        return $this->server->GetFile($key);
    }

    /**
     * @param mixed $objectToSerialize
     * @param string|null $error
     * @param int|null $httpResponseCode
     * @return void
     */
    protected function SetJson($objectToSerialize, $error = null, $httpResponseCode = 200)
    {
        header('Content-type: application/json');
        http_response_code(empty($httpResponseCode) ? 200 : $httpResponseCode);

        if (empty($error)) {
            $this->Set('data', json_encode($objectToSerialize));
        } else {
            $this->Set('error', json_encode(array('response' => $objectToSerialize, 'errors' => $error)));
        }
        $this->smarty->display('json_data.tpl');
    }

    /**
     * @param string $objectToSerialize
     * @return void
     */
    protected function SetJsonError($objectToSerialize)
    {
        header('Content-type: application/json');
        header('HTTP/1.1 500 Internal Server Error');

        $this->Set('data', json_encode($objectToSerialize));

        $this->smarty->display('json_data.tpl');
    }

    /**
     * A template file to be displayed
     * @param string $templateName
     */
    protected function Display($templateName)
    {
        if ($this->InMaintenanceMode()) {
            $this->smarty->display('maintenance.tpl');
        } elseif ($this->ForcePasswordUpdate()) {
            $this->RedirectUnsafe(Configuration::Instance()->GetScriptUrl() . "/password.php?force=true");
        } elseif ($this->ForceMfA()) {
            $this->RedirectUnsafe(Configuration::Instance()->GetScriptUrl() . sprintf("/auth/confirm-account.php?%s=%s", QueryStringKeys::REDIRECT, urlencode($this->server->GetUrl())));
        } else {
            $this->smarty->display($templateName);

        }
    }

    protected function DisplayCsv($templateName, $fileName)
    {
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=$fileName;");
        header("Content-Transfer-Encoding: binary");
        echo chr(239) . chr(187) . chr(191);

        $this->Display($templateName);
    }

    /**
     * @param string $templateName
     * @param null $languageCode uses current language is nothing is passed in
     */
    protected function DisplayLocalized($templateName, $languageCode = null)
    {
        if (empty($languageCode)) {
            $languageCode = $this->GetVar('CurrentLanguage');
        }
        $localizedPath = ROOT_DIR . 'lang/' . $languageCode;
        $defaultPath = ROOT_DIR . 'lang/en_us/';

        if (file_exists($localizedPath . '/' . $templateName)) {
            $this->smarty->AddTemplateDirectory($localizedPath);
        } else {
            $this->smarty->AddTemplateDirectory($defaultPath);
        }

        $this->Display($templateName);
    }

    protected function GetShouldAutoLogout()
    {
        $timeout = $this->GetVar('SessionTimeoutSeconds');

        return !empty($timeout);
    }

    private function InMaintenanceMode()
    {
        return is_file(ROOT_DIR . 'maint.txt');
    }

    private function ForcePasswordUpdate()
    {
        $currentUrl = $_SERVER["PHP_SELF"];
        $session = ServiceLocator::GetServer()->GetUserSession();
        return $session->IsLoggedIn() && $session->ForcePasswordReset && !BookedStringHelper::EndsWith($currentUrl, "password.php");
    }

    private function ForceMfA()
    {
        $currentUrl = $_SERVER["PHP_SELF"];
        $session = ServiceLocator::GetServer()->GetUserSession();
        return $session->IsLoggedIn() && $session->IsAwaitingMultiFactorAuth && !BookedStringHelper::EndsWith($currentUrl, "confirm-account.php");
    }

    private function SetSecurityHeaders()
    {
        $config = Configuration::Instance();
        if ($config->GetSectionKey(ConfigSection::SECURITY, ConfigKeys::SECURITY_HEADERS, new BooleanConverter())) {
            header('Strict-Transport-Security: ' . $config->GetSectionKey(ConfigSection::SECURITY, ConfigKeys::SECURITY_STRICT_TRANSPORT));
            header('X-Frame-Options: ' . $config->GetSectionKey(ConfigSection::SECURITY, ConfigKeys::SECURITY_X_FRAME));
            header('X-XSS-Protection: ' . $config->GetSectionKey(ConfigSection::SECURITY, ConfigKeys::SECURITY_X_XSS));
            header('X-Content-Type-Options: ' . $config->GetSectionKey(ConfigSection::SECURITY, ConfigKeys::SECURITY_X_CONTENT_TYPE));
            header('Content-Security-Policy: ' . $config->GetSectionKey(ConfigSection::SECURITY, ConfigKeys::SECURITY_CONTENT_SECURITY_POLICY));
        }
    }

    protected function IsPost()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    private function ShouldShowNewVersion()
    {
        if (!$this->server->GetUserSession()->IsAdmin) {
            return false;
        }

        $newVersion = $this->server->GetCookie('new_version');
        if (empty($newVersion)) {
            $cookie = sprintf('v=%s,fs=%s', Configuration::VERSION, Date::Now()->Timestamp());
            $this->server->SetCookie(new Cookie('new_version', $cookie));
            return true;
        }

        $parts = explode(',', $newVersion);
        $versionParts = explode('=', $parts[0]);
        $firstShownParts = explode('=', $parts[1]);

        if ($versionParts[1] != Configuration::VERSION) {
            $cookie = sprintf('v=%s,fs=%s', Configuration::VERSION, Date::Now()->Timestamp());
            $this->server->SetCookie(new Cookie('new_version', $cookie));
            return true;
        }

        if (Date::Now()->AddDays(-3)->Timestamp() > $firstShownParts[1]) {
            return false;
        }

        return true;
    }

    /**
     * @return CustomFileCache
     */
    private function GetCustomFileCache()
    {
        return CustomFileCache::Load($this->path);
    }
}