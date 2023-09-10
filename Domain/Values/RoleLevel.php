<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

class RoleLevel
{
	private function __construct()
	{}

	const NONE = null;
	const GROUP_ADMIN = 1;
	const APPLICATION_ADMIN = 2;
    const RESOURCE_ADMIN = 3;
	const SCHEDULE_ADMIN = 4;

    /**
     * @return RoleLevel[]
     */
    public static function All()
    {
        return [
            RoleLevel::GROUP_ADMIN,
            RoleLevel::APPLICATION_ADMIN,
            RoleLevel::RESOURCE_ADMIN,
            RoleLevel::SCHEDULE_ADMIN
        ];
    }
}