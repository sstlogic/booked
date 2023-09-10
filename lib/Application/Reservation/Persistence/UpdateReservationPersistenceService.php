<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'lib/Application/Reservation/Persistence/IReservationPersistenceService.php');

interface IUpdateReservationPersistenceService extends IReservationPersistenceService
{
	/**
	 * @param int $reservationInstanceId
	 * @return ExistingReservationSeries
	 */
	public function LoadByInstanceId($reservationInstanceId);

	/**
	 * @param string $referenceNumber
	 * @return ExistingReservationSeries
	 */
	public function LoadByReferenceNumber($referenceNumber);
}

class UpdateReservationPersistenceService implements IUpdateReservationPersistenceService
{
	/**
	 * @var IReservationRepository
	 */
	private $repository;
    /**
     * @var IReservationMeetingLinkService
     */
    private $meetingService;

    public function __construct(IReservationRepository $repository, IReservationMeetingLinkService $meetingService)
	{
		$this->repository = $repository;
        $this->meetingService = $meetingService;
    }

	public function LoadByInstanceId($reservationInstanceId)
	{
		return $this->repository->LoadById($reservationInstanceId);
	}

	public function Persist($existingReservationSeries)
	{
		$this->repository->Update($existingReservationSeries);
        $this->meetingService->Update($existingReservationSeries, $this->repository);
	}

	public function LoadByReferenceNumber($referenceNumber)
	{
		return $this->repository->LoadByReferenceNumber($referenceNumber);
	}
}