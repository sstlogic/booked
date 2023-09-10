<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/


interface IBlackoutValidationResult
{
	/**
	 * @return bool
	 */
	public function WasSuccessful();

	/**
	 * @abstract
	 * @return string
	 */
	public function Message();

	/**
	 * @abstract
	 * @return array|ReservationItemView[]
	 */
	public function ConflictingReservations();

	/**
	 * @abstract
	 * @return array|BlackoutItemView[]
	 */
	public function ConflictingBlackouts();
}
