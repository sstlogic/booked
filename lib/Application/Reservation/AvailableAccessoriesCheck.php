<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/AccessoryAggregation.php');

class AvailableAccessoriesCheck
{
	/**
	 * @var IAccessoryRepository
	 */
	private $accessoryRepository;
	/**
	 * @var IReservationViewRepository
	 */
	private $reservationViewRepository;

	public function __construct(IAccessoryRepository $accessoryRepository, IReservationViewRepository $reservationViewRepository)
	{
		$this->accessoryRepository = $accessoryRepository;
		$this->reservationViewRepository = $reservationViewRepository;
	}

    /**
     * @param Date $start
     * @param Date $end
     * @param string $referenceNumber
     * @return AccessoryAvailability[]
     */
	public function Check(Date $start, Date $end, $referenceNumber)
	{
		$accessories = $this->accessoryRepository->LoadAll();

		$duration = new DateRange($start, $end);
		$accessoryReservations = $this->reservationViewRepository->GetAccessoriesWithin($duration);

		$aggregation = new AccessoryAggregation($accessories, $duration);

		foreach ($accessoryReservations as $accessoryReservation)
		{
			if ($referenceNumber != $accessoryReservation->GetReferenceNumber())
			{
				$aggregation->Add($accessoryReservation);
			}
		}

		$realAvailability = array();

		foreach ($accessories as $accessory)
		{
			$id = $accessory->GetId();

			$available = $accessory->GetQuantityAvailable();
			if ($available != null)
			{
				$reserved = $aggregation->GetQuantity($id);
				$realAvailability[] = new AccessoryAvailability($id, max(0,$available - $reserved));
			}
			else
			{
				$realAvailability[] = new AccessoryAvailability($id, null);
			}
		}

		return $realAvailability;
	}
}

class AccessoryAvailability
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var int|null
     */
    public $quantity;

    public function __construct($id, $quantity)
    {
        $this->id = intval($id);
        $this->quantity = $quantity === null ? null : intval($quantity);
    }
}