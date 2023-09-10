<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class UserMultiFactorAuthenticationSettings
{
    /**
     * @var int
     */
    private $userId;
    /**
     * @var string
     */
    private $otp;
    /**
     * @var Date
     */
    private $created;

    /**
     * @param int $userId
     * @param string $otp
     * @param Date $created
     */
    public function __construct($userId, $otp, $created)
    {
        $this->userId = $userId;
        $this->otp = $otp;
        $this->created = $created;
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
    public function Otp()
    {
        return $this->otp;
    }

    /**
     * @return Date
     */
    public function CreatedDate()
    {
        return $this->created->IsNull() ? Date::Now() : $this->created;
    }

    /**
     * @return Date
     */
    public function ExpirationDate()
    {
        return $this->CreatedDate()->AddMinutes(10);
    }

    /**
     * @return bool
     */
    public function IsExpired()
    {
        return $this->ExpirationDate()->LessThan(Date::Now());
    }
}