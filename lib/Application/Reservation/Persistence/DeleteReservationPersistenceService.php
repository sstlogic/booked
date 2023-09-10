<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'lib/Application/Reservation/Persistence/IReservationPersistenceService.php');

interface IDeleteReservationPersistenceService extends IReservationPersistenceService
{
	/**
	 * @param string $referenceNumber
	 * @return ExistingReservationSeries
	 */
	public function LoadByReferenceNumber($referenceNumber);
}

class DeleteReservationPersistenceService implements IDeleteReservationPersistenceService
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

	public function LoadByReferenceNumber($referenceNumber)
	{
		return $this->repository->LoadByReferenceNumber($referenceNumber);
	}

	public function Persist($existingReservationSeries)
	{
		$this->repository->Delete($existingReservationSeries);
        $this->meetingService->Delete($existingReservationSeries, $this->repository);
	}
}
