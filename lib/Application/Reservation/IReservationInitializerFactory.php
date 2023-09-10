<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

interface IReservationInitializerFactory
{
	/**
	 * @param INewReservationPage $page
	 * @return IReservationInitializer
	 */
	public function GetNewInitializer(INewReservationPage $page);

	/**
	 * @param IExistingReservationPage $page
	 * @param ReservationView $reservationView
	 * @return IReservationInitializer
	 */
	public function GetExistingInitializer(IExistingReservationPage $page, ReservationView $reservationView);
}