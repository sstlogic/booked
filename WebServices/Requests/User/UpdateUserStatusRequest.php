<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/WebService/JsonRequest.php');

class UpdateUserStatusRequest extends JsonRequest
{
    public $statusId;

    public static function Example()
    {
        $request = new UpdateUserStatusRequest();
        $request->statusId = AccountStatus::ACTIVE;
        return $request;
    }
}