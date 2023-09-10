<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ResourceProperties
{
    /**
     * @var int
     */
    public $MaxConcurrentReservations;
	/**
	 * @var string
	 */
    public $SlotLabel;

    public static function FromResource(BookableResource $resource)
    {
        $obj = new ResourceProperties();
        $obj->MaxConcurrentReservations = $resource->GetMaxConcurrentReservations();
        $obj->SlotLabel = $resource->GetSlotLabel();

        return $obj;
    }

    public static function Deserialize($propertiesAsJson)
    {
        $obj = new ResourceProperties();
        if (empty($propertiesAsJson)) {
        	return $obj;
		}

        $decoded = json_decode($propertiesAsJson);
        if (isset($decoded->MaxConcurrentReservations)) {
            $obj->MaxConcurrentReservations = $decoded->MaxConcurrentReservations;
        }
        if (isset($decoded->SlotLabel)) {
            $obj->SlotLabel = $decoded->SlotLabel;
        }

        return $obj;
    }

    public function Serialize()
    {
        return json_encode($this);
    }
}
