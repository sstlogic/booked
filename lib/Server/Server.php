<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Server/UserSession.php');

class Server
{
    public function __construct()
    {
    }

    public function SetCookie(Cookie $cookie)
    {
        setcookie($cookie->Name, $cookie->Value . '', $cookie->Expiration, $cookie->Path, "", $this->GetIsHttps(), $cookie->HttpOnly);
    }

    public function DeleteCookie(Cookie $cookie)
    {
        setcookie($cookie->Name, "", time() - 3600, $cookie->Path);
    }

    public function GetCookie($name)
    {
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        }
        return null;
    }

    const sessionId = 'booked';

    protected function InitSession()
    {
        if (!$this->IsSessionStarted()) {
            $parts = parse_url(Configuration::Instance()->GetScriptUrl());
            $path = isset($parts['path']) ? $parts['path'] : '';
            $seconds = Configuration::Instance()->GetKey(ConfigKeys::INACTIVITY_TIMEOUT, new IntConverter()) * 60;
            if (!empty($seconds)) {
                $defaultSessionSavePath = session_save_path();
                @ini_set('session.gc_maxlifetime', $seconds);
                @ini_set('session.gc_probability', 1);
                @ini_set('session.gc_divisor', 100);
                $sessionPath = Configuration::Instance()->GetKey(ConfigKeys::SESSION_SAVE_PATH);
                if (empty($sessionPath)) {
                    $sessionPath = ROOT_DIR . '../tmp/session';
                } else {
                    if (!BookedStringHelper::StartsWith($sessionPath, "/") && preg_match("/^[a-zA-Z]:\\\\/", $sessionPath) === 0) {
                        $sessionPath = ROOT_DIR . $sessionPath;
                    }

                }
                if (!is_dir($sessionPath)) {
                    $created = @mkdir($sessionPath, 755, true);
                    if (!$created) {
                        Log::Error('Could not create session save directory. Check your session.save.path configuration setting. Falling back to default session save path.', ['session.save.path' => $sessionPath, 'defaultSessionPath' => $defaultSessionSavePath]);
                        $sessionPath = $defaultSessionSavePath;
                    }
                }
                $setSessionSavePath = @session_save_path($sessionPath);
                if ($setSessionSavePath === false) {
                    Log::Error('Could not initialize session save path, attempting to use default. Check your session.save.path configuration setting.', ['sessionPath' => $sessionPath, 'defaultSessionPath' => $defaultSessionSavePath]);
                    @session_save_path($defaultSessionSavePath);
                }
            }
            ini_set('session.cookie_httponly', 1);
            @session_set_cookie_params(0, $path, null, $this->GetIsHttps(), true);
            @session_unset();
            @session_destroy();
            @session_start();
        }
    }

    public function SetSession($name, $value)
    {
        $this->InitSession();
        $_SESSION[self::sessionId][$name] = $value;
    }

    public function GetSession($name)
    {
        $this->InitSession();
        if (isset($_SESSION[self::sessionId][$name])) {
            return $_SESSION[self::sessionId][$name];
        }
        return null;
    }

    public function EndSession($name)
    {
        $this->SetSession($name, null);
        @session_unset();
        @session_destroy();
    }

    /**
     * @return bool
     */
    public function IsSessionStarted()
    {
        if (php_sapi_name() !== 'cli') {
            if (version_compare(phpversion(), '5.4.0', '>=')) {
                return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
            } else {
                return session_id() === '' ? FALSE : TRUE;
            }
        }
        return FALSE;
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function GetQuerystring($name)
    {
        if (isset($_GET[$name])) {
            $value = $_GET[$name];

            if ($value != '' && $value != null && !is_array($value)) {
                return htmlspecialchars(trim($value));
            } else {
                if (is_array($value)) {
                    array_walk($value, array($this, 'specialchars'));
                    return $value;
                }
            }

            return '';
        }
        return null;
    }

    private static function specialchars(&$val)
    {
        $val = htmlspecialchars(trim($val));
    }

    /**
     * @param string $name
     * @return string|null|array
     */
    public function GetForm($name)
    {
        $value = $this->GetRawForm($name);
        if (!empty($value) && !is_array($value)) {
            return htmlspecialchars(trim($value));
        }

        if (is_array($value)) {
            array_walk($value, array($this, 'specialchars'));
            return $value;
        }

        return $value;
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function GetRawForm($name)
    {
        if (isset($_POST[$name])) {
            if (is_array($_POST[$name])) {
                return $_POST[$name];
            }

            return trim($_POST[$name]);
        }
        return null;
    }

    /**
     * @param string $name
     * @return null|UploadedFile
     */
    public function GetFile($name)
    {
        if (isset($_FILES[$name])) {
            return new UploadedFile($_FILES[$name]);
        }
        return null;
    }

    /**
     * @param string $name
     * @return array|UploadedFile[]
     */
    public function GetFiles($name)
    {
        $uploadedFiles = array();

        if (isset($_FILES[$name])) {
            $files = $_FILES[$name];
            if (is_array($files['name'])) {
                // convert the files from the weird PHP multi-file array to a normal array of objects
                // taken from PHP.net
                $file_ary = array();
                $file_count = count($files['name']);
                $file_keys = array_keys($files);

                for ($i = 0; $i < $file_count; $i++) {
                    foreach ($file_keys as $key) {
                        $file_ary[$i][$key] = $files[$key][$i];
                    }

                    $uploadedFiles[] = new UploadedFile($file_ary[$i]);
                }
            } else {
                $uploadedFiles[] = new UploadedFile($_FILES[$name]);
            }
        }
        return $uploadedFiles;
    }

    public function GetUrl()
    {
        $url = str_replace("index.php", "", $_SERVER['SCRIPT_NAME']);

        if (isset($_SERVER['QUERY_STRING'])) {
            $qs = http_build_query($_GET);
            $url .= '?' . $qs;
        }

        return $url;
    }

    public function GetHost()
    {
        return $_SERVER['HTTP_HOST'];
    }

    public function GetHostAndUri()
    {
        return $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '';
    }

    /**
     * @return UserSession
     */
    public function GetUserSession()
    {
        $userSession = $this->GetSession(SessionKeys::USER_SESSION);

        if (!empty($userSession)) {
            return $userSession;
        }

        return new NullUserSession();
    }

    /**
     * @param $userSession UserSession
     * @return void
     */
    public function SetUserSession($userSession)
    {
        $this->SetSession(SessionKeys::USER_SESSION, $userSession);
    }

    /**
     * @return string
     */
    public function GetRequestMethod()
    {
        return $this->GetHeader('REQUEST_METHOD');
    }

    /**
     * @return string
     */
    public function GetLanguage()
    {
        $lang = $this->GetHeader('HTTP_ACCEPT_LANGUAGE');
        if (strlen($lang) > 4) {
            return substr(str_replace('-', '_', $lang), 0, 5);
        }
        return null;
    }

    /**
     * @param string $headerCode
     * @return string
     */
    public function GetHeader($headerCode)
    {
        if (isset($_SERVER[$headerCode])) {
            return $_SERVER[$headerCode];
        }
        return '';
    }

    /**
     * @return string
     */
    public function GetRemoteAddress()
    {
        return $this->GetHeader('REMOTE_ADDR');
    }

    /**
     * @return bool
     */
    public function GetIsHttps()
    {
        $isHttps = $this->GetHeader('HTTPS');
        return $isHttps == 'on';
    }

    /**
     * @return string
     */
    public function GetRequestUri()
    {
        return $this->GetUrl();
        //return $this->GetHeader('REQUEST_URI');
    }
}
