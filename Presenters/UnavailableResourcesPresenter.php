<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'Presenters/UnavailableResourcesPresenter.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/namespace.php');

class UnavailableResourcesPresenter
{
	/**
	 * @var IAvailableResourcesPage
	 */
	private $page;
	/**
	 * @var IReservationConflictIdentifier
	 */
	private $reservationConflictIdentifier;
	/**
	 * @var UserSession
	 */
	private $userSession;
	/**
	 * @var IResourceRepository
	 */
	private $resourceRepository;
	/**
	 * @var IReservationRepository
	 */
	private $reservationRepository;
	/**
	 * @var IResourceAvailabilityStrategy
	 */
	private $resourceAvailability;

	public function __construct(IAvailableResourcesPage $page,
								IReservationConflictIdentifier $reservationConflictIdentifier,
								UserSession $userSession,
								IResourceRepository $resourceRepository,
								IReservationRepository $reservationRepository,
								IResourceAvailabilityStrategy $resourceAvailability)
	{
		$this->page = $page;
		$this->reservationConflictIdentifier = $reservationConflictIdentifier;
		$this->userSession = $userSession;
		$this->resourceRepository = $resourceRepository;
		$this->reservationRepository = $reservationRepository;
		$this->resourceAvailability = $resourceAvailability;
	}

	public function PageLoad()
	{
		$duration = DateRange::Create($this->page->GetStartDate() . ' ' . $this->page->GetStartTime(),
									  $this->page->GetEndDate() . ' ' . $this->page->GetEndTime(), $this->userSession->Timezone);

		$resources = $this->resourceRepository->GetScheduleResources($this->page->GetScheduleId());

		$allowConcurrent = false;
		foreach ($resources as $resource)
		{
			if ($resource->GetAllowConcurrentReservations())
			{
				$allowConcurrent = true;
				break;
			}
		}

		if ($allowConcurrent)
		{
			if (count($resources) > 100) {
				$this->page->BindUnavailable([], true);
				return;
			}

			$unavailable = array();
			$referenceNumber = $this->page->GetReferenceNumber();
			$series = null;
			$existingSeries = false;
			if (!empty($referenceNumber))
			{
				$series = $this->reservationRepository->LoadByReferenceNumber($referenceNumber);
				$series->UpdateDuration($duration);
				$existingSeries = true;
			}

			foreach ($resources as $resource)
			{
				if (!$existingSeries)
				{
					$series = ReservationSeries::Create($this->userSession->UserId, $resource, "", "", $duration, new RepeatNone(), $this->userSession);
				}
				$conflict = $this->reservationConflictIdentifier->GetConflicts($series);

				if (!$conflict->AllowReservation())
				{
					$unavailable[] = $resource->GetId();
				}
			}

			$this->page->BindUnavailable(array_unique($unavailable));
		}
		else
		{
			$unavailable = array();
			$reserved = $this->resourceAvailability->GetItemsBetween($duration->GetBegin(), $duration->GetEnd(), null);
			foreach ($reserved as $reservation)
			{
				if ($reservation->GetReferenceNumber() == $this->page->GetReferenceNumber())
				{
					continue;
				}

				if ($reservation->BufferedTimes()->Overlaps($duration))
				{
					$unavailable[] = $reservation->GetResourceId();
				}
			}

			$this->page->BindUnavailable(array_unique($unavailable));
		}
	}
}