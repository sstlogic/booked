<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

class ReservationAccessory
{
	/**
	 * @var Accessory
	 */
	public $Accessory;

	/**
	 * @var int
	 */
	public $AccessoryId;

	/**
	 * @var int
	 */
	public $QuantityReserved;

	/**
	 * @param Accessory $accessory
	 * @param int $quantityReserved
	 */
	public function __construct(Accessory $accessory, $quantityReserved)
	{
		$this->Accessory = $accessory;
		$this->AccessoryId = $accessory->GetId();
		$this->QuantityReserved = intval($quantityReserved);
        $this->Name = $accessory->GetName();
	}

    public $Name = "";

    /**
     * @return string
     */
    public function Name() {
        return $this->Accessory->GetName();
    }

	public function __toString()
	{
        return sprintf("ReservationAccessory id:%d quantity reserved:%d", $this->Accessory->GetId(), $this->QuantityReserved);
    }
}
