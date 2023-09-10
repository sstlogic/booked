<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

class ErrorMessages
{
	const UNKNOWN_ERROR = 0;
	const INSUFFICIENT_PERMISSIONS = 1;
	const MISSING_RESOURCE = 2;
	const MISSING_SCHEDULE = 3;
	const RESERVATION_NOT_FOUND = 4;
	const RESERVATION_NOT_AVAILABLE = 5;

	private $_resourceKeys = array();
	private static $_instance;

	private function __construct()
	{
		$this->SetKey(ErrorMessages::INSUFFICIENT_PERMISSIONS, 'InsufficientPermissionsError');
		$this->SetKey(ErrorMessages::MISSING_RESOURCE, 'MissingReservationResourceError');
		$this->SetKey(ErrorMessages::MISSING_SCHEDULE, 'MissingReservationScheduleError');
		$this->SetKey(ErrorMessages::RESERVATION_NOT_FOUND, 'ReservationNotFoundError');
		$this->SetKey(ErrorMessages::RESERVATION_NOT_AVAILABLE, 'ReservationNotAvailable');
	}

    /**
     * @static
     * @return ErrorMessages
     */
	public static function Instance()
	{
		if (self::$_instance == null)
		{
			self::$_instance = new ErrorMessages();
		}

		return self::$_instance;
	}

	private function SetKey($errorMessageId, $resourceKey)
	{
		$this->_resourceKeys[$errorMessageId] = $resourceKey;
	}

	public function GetResourceKey($errorMessageId)
	{
		if (!isset($this->_resourceKeys[$errorMessageId]))
		{
			return 'UnknownError';
		}

		return $this->_resourceKeys[$errorMessageId];
	}
}