<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'lib/Application/Reservation/Notification/IReservationNotificationService.php');

class DeleteReservationNotificationService extends ReservationNotificationService
{
	public function __construct(IUserRepository $userRepo, IAttributeRepository $attributeRepo)
	{
		$notifications = array();

        $notifications[] = new OwnerEmailDeletedNotification($userRepo, $attributeRepo);
        $notifications[] = new OwnerSmsDeletedNotification($userRepo);
        $notifications[] = new ParticipantDeletedEmailNotification($userRepo, $attributeRepo);
        $notifications[] = new AdminEmailDeletedNotification($userRepo, $userRepo, $attributeRepo);
		$notifications[] = new GuestDeletedEmailNotification($userRepo, $attributeRepo);
		$notifications[] = new CoOwnerDeletedEmailNotification($userRepo, $attributeRepo);

		parent::__construct($notifications);
	}
}