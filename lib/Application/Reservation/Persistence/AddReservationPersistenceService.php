<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

class AddReservationPersistenceService implements IReservationPersistenceService
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

	public function Persist($reservation)
	{
		$this->repository->Add($reservation);
        $this->meetingService->Add($reservation, $this->repository);
	}
}