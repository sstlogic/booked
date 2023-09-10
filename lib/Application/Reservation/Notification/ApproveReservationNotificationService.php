<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'lib/Application/Reservation/Notification/IReservationNotificationService.php');

class ApproveReservationNotificationService extends ReservationNotificationService
{
	public function __construct(IUserRepository $userRepo, IResourceRepository $resourceRepo, IAttributeRepository $attributeRepo)
	{
		$notifications = array();
		$notifications[] = new OwnerEmailApprovedNotification($userRepo, $attributeRepo);
        $notifications[] = new OwnerSmsApprovedNotification($userRepo);

		parent::__construct($notifications);
	}
}
