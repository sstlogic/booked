<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

class SignOutRequest
{
    /**
     * @var string
     */
    public $userId;
    /**
     * @var string
     */
    public $sessionToken;

    /**
     * @param string $userId
     * @param string $sessionToken
     */
    public function __construct($userId = null, $sessionToken = null)
    {
        $this->userId = $userId;
        $this->sessionToken = $sessionToken;
    }

    public static function Example()
    {
        $request = new SignOutRequest();
        $request->userId = 1;
        $request->sessionToken = "session token";
        return $request;
    }
}

