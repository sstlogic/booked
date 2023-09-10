<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

class ReservationUserLevel
{
	public function __construct()
	{
	}

	const ALL = 0;
	const OWNER = 1;
	const PARTICIPANT = 2;
	const INVITEE = 3;
	const CO_OWNER = 4;
}