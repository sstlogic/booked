<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class OtpGenerator implements IOtpGenerator
{
    public function Generate()
    {
        return random_int(100000, 999999);
    }
}