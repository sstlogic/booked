<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

class OAuthRefreshResponse
{
    /**
     * @var string
     */
    private $accessToken;
    /**
     * @var string
     */
    private $refreshToken;
    /**
     * @var Date
     */
    private $expiresAt;

    public function __construct(string $accessToken, string $refreshToken, Date $expiresAt)
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return string
     */
    public function AccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return string
     */
    public function RefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @return Date
     */
    public function ExpiresAt()
    {
        return $this->expiresAt;
    }
}
