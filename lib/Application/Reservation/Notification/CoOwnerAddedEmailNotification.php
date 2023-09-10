<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'lib/Email/Messages/CoOwnerAddedEmail.php');

class CoOwnerAddedEmailNotification implements IReservationNotification
{
	/**
	 * @var IUserRepository
	 */
	protected $userRepository;

	/**
	 * @var IAttributeRepository
	 */
    protected $attributeRepository;

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

		foreach ($instance->AddedCoOwners() as $userId)
		{
			$coowner = $this->userRepository->LoadById($userId);

			$message = new CoOwnerAddedEmail($owner, $coowner, $reservationSeries, $this->attributeRepository, $this->userRepository);
			ServiceLocator::GetEmailService()->Send($message);
		}
	}
}