<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/


interface IReservationListingFactory
{
	/**
	 * @param string $targetTimezone
     * @param DateRange|null $acceptableDateRange
     * @return IMutableReservationListing
	 */
	function CreateReservationListing($targetTimezone, $acceptableDateRange = null);
}

class ReservationListingFactory implements IReservationListingFactory
{
	public function CreateReservationListing($targetTimezone, $acceptableDateRange = null)
	{
		return new ReservationListing($targetTimezone, $acceptableDateRange);
	}
}
