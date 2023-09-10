<?php
/**
Copyright 2017-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'lib/Application/Reservation/Notification/InviteeAddedEmailNotification.php');

class InviteeUpdatedEmailNotification extends InviteeAddedEmailNotification
{
	/**
	 * @var IUserRepository
	 */
	private $userRepository;

	/**
	 * @var IAttributeRepository
	 */
	private $attributeRepository;

	public function __construct(IUserRepository $userRepository, IAttributeRepository $attributeRepository)
	{
		$this->userRepository = $userRepository;
		$this->attributeRepository = $attributeRepository;
        parent::__construct($userRepository, $attributeRepository);
	}

	/**
	 * @param ReservationSeries $reservationSeries
	 */
	function Notify($reservationSeries)
	{
        $owner = $this->userRepository->LoadById($reservationSeries->UserId());

		$instance = $reservationSeries->CurrentInstance();
		foreach ($instance->AddedInvitees() as $userId)
        {
			$invitee = $this->userRepository->LoadById($userId);

			$message = new InviteeAddedEmail($owner, $invitee, $reservationSeries, $this->attributeRepository, $this->userRepository);
			ServiceLocator::GetEmailService()->Send($message);
        }

        foreach ($instance->UnchangedInvitees() as $userId)
        {
			$invitee = $this->userRepository->LoadById($userId);

			$message = new InviteeUpdatedEmail($owner, $invitee, $reservationSeries, $this->attributeRepository, $this->userRepository);
			ServiceLocator::GetEmailService()->Send($message);
		}

		foreach ($instance->RemovedInvitees() as $userId)
		{
			$invitee = $this->userRepository->LoadById($userId);

			$message = new InviteeRemovedEmail($owner, $invitee, $reservationSeries, $this->attributeRepository, $this->userRepository);
			ServiceLocator::GetEmailService()->Send($message);
		}
	}
}