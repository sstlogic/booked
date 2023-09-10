<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

class OAuthProviders
{
    public const Zoom = 1;
    public const Microsoft = 2;
}

class UserOAuth
{
    /**
     * @var int
     */
    protected $userId;
    /**
     * @var string
     */
    protected $accessToken;
    /**
     * @var string
     */
    protected $refreshToken;
    /**
     * @var Date
     */
    protected $expiresAt;
    /**
     * @var int
     */
    protected $providerId;

    public static function FromRow(array $row)
    {
        $u = new UserOAuth();
        $u->userId = intval($row[ColumnNames::USER_ID]);
        $u->accessToken = $row[ColumnNames::ACCESS_TOKEN];
        $u->refreshToken = $row[ColumnNames::REFRESH_TOKEN];
        $u->expiresAt = Date::FromDatabase($row[ColumnNames::EXPIRES_AT]);
        $u->providerId = intval($row[ColumnNames::PROVIDER_ID]);

        return $u;
    }

    public static function Create(int $userId, string $accessToken, string $refreshToken, Date $expiresAt, int $providerId)
    {
        $u = new UserOAuth();
        $u->userId = $userId;
        $u->accessToken = $accessToken;
        $u->refreshToken = $refreshToken;
        $u->expiresAt = $expiresAt;
        $u->providerId = $providerId;
        return $u;
    }

    /**
     * @return int
     */
    public function UserId()
    {
        return $this->userId;
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

    /**
     * @return int
     */
    public function ProviderId()
    {
        return $this->providerId;
    }

    /**
     * @return bool
     */
    public function RequiresRefresh()
    {
        return Date::Now()->GreaterThanOrEqual($this->expiresAt);
    }
    
}