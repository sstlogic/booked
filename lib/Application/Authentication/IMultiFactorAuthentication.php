<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

interface IMultiFactorAuthentication
{
    /**
     * @param User $user
     * @param string|null $token
     * @return bool
     */
    public function Enforce(User $user, $token);

    /**
     * @param User $user
     */
    public function GenerateAndSendOtp(User $user);
}