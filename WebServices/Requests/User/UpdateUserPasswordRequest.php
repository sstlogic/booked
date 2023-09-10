<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/WebService/JsonRequest.php');

class UpdateUserPasswordRequest extends JsonRequest
{
    public $password;

    public static function Example()
    {
        $request = new UpdateUserPasswordRequest();
        $request->password = 'plaintext password';
        return $request;
    }
}