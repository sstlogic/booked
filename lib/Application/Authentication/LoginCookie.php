<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Common/namespace.php');

class LoginCookie extends Cookie
{
    public $UserID;
    public $Token;

    public function __construct($userId, $rememberMeToken)
    {
        $this->UserID = $userId;
        $this->Token = $rememberMeToken;

        parent::__construct(CookieKeys::PERSIST_LOGIN, "{$userId}|{$rememberMeToken}", null, null, true);
    }

    /**
     * @param string $cookieValue
     * @return LoginCookie|null
     */
    public static function FromValue($cookieValue)
    {
        if (empty($cookieValue)) {
            return null;
        }
        $parts = explode("|", $cookieValue);
        if (count($parts) != 2) {
            return null;
        }
        return new LoginCookie($parts[0], $parts[1]);
    }
}
