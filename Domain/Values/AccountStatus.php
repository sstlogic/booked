<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

class AccountStatus
{
	private function __construct()
	{}

	const ALL = 0;
	const ACTIVE = 1;
	const AWAITING_ACTIVATION = 2;
	const INACTIVE = 3;
}