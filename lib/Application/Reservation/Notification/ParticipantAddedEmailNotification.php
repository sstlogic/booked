<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'lib/Email/Messages/ParticipantAddedEmail.php');

class ParticipantAddedEmailNotification implements IReservationNotification
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
	}

	/**
	 * @param ReservationSeries $reservationSeries
	 */
	function Notify($reservationSeries)
	{
		$instance = $reservationSeries->CurrentInstance();
        $owner = $this->userRepository->LoadById($reservationSeries->UserId());

		foreach ($instance->AddedParticipants() as $userId)
		{
			$participant = $this->userRepository->LoadById($userId);

			$message = new ParticipantAddedEmail($owner, $participant, $reservationSeries, $this->attributeRepository, $this->userRepository);
			ServiceLocator::GetEmailService()->Send($message);
		}
	}
}