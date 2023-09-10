<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/ExternalMeetings/namespace.php');

class ReservationPersistenceFactory implements IReservationPersistenceFactory
{
	private $services = array();
	private $creationStrategies = array();

	public function __construct()
	{
		$this->creationStrategies[ReservationAction::Approve] = 'CreateUpdateService';
		$this->creationStrategies[ReservationAction::Create] = 'CreateAddService';
		$this->creationStrategies[ReservationAction::Delete] = 'CreateDeleteService';
		$this->creationStrategies[ReservationAction::Update] = 'CreateUpdateService';
		$this->creationStrategies[ReservationAction::Checkin] = 'CreateUpdateService';
		$this->creationStrategies[ReservationAction::Checkout] = 'CreateUpdateService';
		$this->creationStrategies[ReservationAction::WaitList] = 'CreateUpdateService';
	}

	/**
	 * @param string $reservationAction
	 * @return IReservationPersistenceService
	 */
	public function Create($reservationAction)
	{
		if (!array_key_exists($reservationAction, $this->services))
		{
			$this->AddCachedService($reservationAction);
		}

		return $this->services[$reservationAction];
	}

	private function AddCachedService($reservationAction)
	{
		$createMethod = $this->creationStrategies[$reservationAction];
		$this->services[$reservationAction] = $this->$createMethod();
	}

	private function CreateAddService()
	{
		return new AddReservationPersistenceService(new ReservationRepository(), $this->GetMeetingLinkService());
	}

	private function CreateDeleteService()
	{
		return new DeleteReservationPersistenceService(new ReservationRepository(), $this->GetMeetingLinkService());
	}

	private function CreateUpdateService()
	{
		return new UpdateReservationPersistenceService(new ReservationRepository(), $this->GetMeetingLinkService());
	}

    private function GetMeetingLinkService()
    {
        return new ReservationMeetingLinkService(new MeetingConnectionFactory());
    }
}