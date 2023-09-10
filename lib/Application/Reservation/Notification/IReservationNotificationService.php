<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

interface IReservationNotificationService
{
	/**
	 * @param $reservationSeries ReservationSeries|ExistingReservationSeries
	 * @return void
	 */
	function Notify($reservationSeries);
}

abstract class ReservationNotificationService implements IReservationNotificationService
{
	/**
	 * @var IReservationNotification[]
	 */
	protected $notifications;

	/**
	 * @param IReservationNotification[] $notifications
	 */
	public function __construct($notifications)
	{
		$this->notifications = $notifications;
	}

	/**
	 * @param $reservationSeries ReservationSeries|ExistingReservationSeries
	 * @return void
	 */
	public function Notify($reservationSeries)
	{
		$referenceNumber = $reservationSeries->CurrentInstance()->ReferenceNumber();

		foreach ($this->notifications as $notification)
		{
			try
			{
				Log::Debug('Calling notify for reservation', ['notificationType' =>  get_class($notification), 'referenceNumber' => $referenceNumber]);

				$notification->Notify($reservationSeries);
			}
			catch(Exception $ex)
			{
				Log::Error('Error sending notification for reservation', ['notificationType' =>  get_class($notification), 'referenceNumber' => $referenceNumber, 'exception' => $ex]);
			}
		}
	}
}