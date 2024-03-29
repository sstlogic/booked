<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'lib/Application/Reservation/Notification/IReservationNotificationService.php');

class UpdateReservationNotificationService extends ReservationNotificationService
{
	public function __construct(IUserRepository $userRepo, IResourceRepository $resourceRepo, IAttributeRepository $attributeRepo)
	{
		$notifications = array();
		$notifications[] = new OwnerEmailUpdatedNotification($userRepo, $attributeRepo);
		$notifications[] = new OwnerSmsUpdatedNotification($userRepo);
		$notifications[] = new AdminEmailUpdatedNotification($userRepo, $userRepo, $attributeRepo);
		$notifications[] = new AdminEmailApprovalNotification($userRepo,  $userRepo, $attributeRepo);
		$notifications[] = new ParticipantAddedEmailNotification($userRepo, $attributeRepo);
		$notifications[] = new InviteeAddedEmailNotification($userRepo, $attributeRepo);
		$notifications[] = new CoOwnerAddedEmailNotification($userRepo, $attributeRepo);
		$notifications[] = new ParticipantUpdatedEmailNotification($userRepo, $attributeRepo);
		$notifications[] = new InviteeUpdatedEmailNotification($userRepo, $attributeRepo);
		$notifications[] = new CoOwnerUpdatedEmailNotification($userRepo, $attributeRepo);
		$notifications[] = new GuestAddedEmailNotification($userRepo, $attributeRepo);
		$notifications[] = new GuestUpdatedEmailNotification($userRepo, $attributeRepo);

		parent::__construct($notifications);
	}
}