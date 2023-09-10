<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class PasswordResetRequest
{
    public const EXPIRATION_MINUTES = 30;

    private $userId;
    private $token;
    private $dateCreated;

    /**
     * @param int $userId
     * @param string $token
     * @param Date $dateCreated
     */
    public function __construct($userId, $token, $dateCreated)
    {

        $this->userId = $userId;
        $this->token = $token;
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return int
     */
    public function UserId()
    {
        return $this->userId;
    }

    /**
     * @return bool
     */
    public function IsExpired()
    {
        return $this->dateCreated->AddMinutes(self::EXPIRATION_MINUTES)->LessThan(Date::Now());
    }
}