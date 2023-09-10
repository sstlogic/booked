<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'lib/Application/Reservation/Notification/IReservationNotificationService.php');

class AddReservationNotificationService extends ReservationNotificationService
{
	public function __construct(IUserRepository $userRepo, IResourceRepository $resourceRepo, IAttributeRepository $attributeRepo)
	{
		$notifications = array();
		$notifications[] = new OwnerEmailCreatedNotification($userRepo, $attributeRepo);
		$notifications[] = new OwnerSmsCreatedNotification($userRepo);
		$notifications[] = new AdminEmailCreatedNotification($userRepo, $userRepo, $attributeRepo);
		$notifications[] = new AdminEmailApprovalNotification($userRepo,  $userRepo, $attributeRepo);
		$notifications[] = new ParticipantAddedEmailNotification($userRepo, $attributeRepo);
		$notifications[] = new InviteeAddedEmailNotification($userRepo, $attributeRepo);
		$notifications[] = new CoOwnerAddedEmailNotification($userRepo, $attributeRepo);
		$notifications[] = new GuestAddedEmailNotification($userRepo, $attributeRepo);

		parent::__construct($notifications);
	}
}