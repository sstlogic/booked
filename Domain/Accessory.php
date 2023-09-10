<?php

/**
 * Copyright 2011-2014 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Values/CreditApplicability.php');

class Accessory
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $quantityAvailable;

    /**
     * @var ResourceAccessory[]
     */
    private $resources = array();

    /**
     * @var int|null
     */
    private $peakCredits;
    /**
     * @var int|null
     */
    private $offPeakCredits;
    /**
     * @var int|CreditApplicability|null
     */
    private $creditApplicability;
    /**
     * @var bool
     */
    private $creditsAlwaysCharged = false;
    /**
     * @var string|null
     */
    private $publicId;

    /**
     * @param int $id
     * @param string $name
     * @param int $quantityAvailable
     */
    public function __construct($id, $name, $quantityAvailable)
    {
        $this->id = $id;
        $this->SetName($name);
        $this->SetQuantityAvailable($quantityAvailable);
        $this->creditApplicability = CreditApplicability::SLOT;
        $this->peakCredits = 0;
        $this->offPeakCredits = 0;
    }

    /**
     * @return int
     */
    public function GetId()
    {
        return intval($this->id);
    }

    /**
     * @return string
     */
    public function GetName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function SetName($name)
    {
        $this->name = $name;
    }

    /**
     * @param int $quantity
     */
    public function SetQuantityAvailable($quantity)
    {
        $q = intval($quantity);
        $this->quantityAvailable = empty($q) ? null : $q;
    }

    /**
     * @return int
     */
    public function GetQuantityAvailable()
    {
        return $this->quantityAvailable;
    }

    /**
     * @return ResourceAccessory[]
     */
    public function Resources()
    {
        return $this->resources;
    }

    /**
     * @return int[]
     */
    public function ResourceIds()
    {
        $ids = array();
        foreach ($this->resources as $resource) {
            $ids[] = $resource->ResourceId;
        }

        return $ids;
    }

    /**
     * @static
     * @param string $name
     * @param int $quantity
     * @return Accessory
     */
    public static function Create($name, $quantity)
    {
        return new Accessory(null, $name, $quantity);
    }

    /**
     * @return bool
     */
    public function HasUnlimitedQuantity()
    {
        return empty($this->quantityAvailable);
    }

    public function AddResource($resourceId, $minQuantity, $maxQuantity)
    {
        $this->resources[] = new ResourceAccessory($resourceId, $minQuantity, $maxQuantity);
    }

    /**
     * @param ResourceAccessory[] $resources
     */
    public function ChangeResources($resources)
    {
        $this->resources = $resources;
    }

    /**
     * @return bool
     */
    public function IsTiedToResource()
    {
        return count($this->resources) > 0;
    }

    /**
     * @param int $resourceId
     * @return ResourceAccessory
     */
    public function GetResource($resourceId)
    {
        foreach ($this->resources as $resource) {
            if ($resource->ResourceId == $resourceId) {
                return $resource;
            }
        }

        return null;
    }

    /**
     * @param int|null $offPeakCredits
     * @param int|null $peakCredits
     * @param int|CreditApplicability $applicability
     * @param bool $alwaysCharged
     */
    public function ChangeCredits($offPeakCredits, $peakCredits, $applicability, $alwaysCharged)
    {
        $this->offPeakCredits = empty($offPeakCredits) ? 0 : $offPeakCredits;
        $this->peakCredits = empty($peakCredits) ? 0 : $peakCredits;
        $this->creditApplicability = CreditApplicability::Create($applicability);
        $this->creditsAlwaysCharged = (bool)intval($alwaysCharged);
    }

    /**
     * @return int|null
     */
    public function GetCreditCount()
    {
        return $this->offPeakCredits;
    }

    /**
     * @return int|null
     */
    public function GetPeakCreditCount()
    {
        return $this->peakCredits;
    }

    /**
     * @return CreditApplicability|int|null
     */
    public function GetCreditApplicability()
    {
        return $this->creditApplicability;
    }

    /**
     * @return bool
     */
    public function GetCreditsAlwaysCharged()
    {
        return $this->creditsAlwaysCharged;
    }

    /**
     * @return bool
     */
    public function HasPublicId()
    {
        return !empty($this->publicId);
    }

    /**
     * @param string $publicId
     */
    public function WithPublicId($publicId)
    {
        $this->publicId = $publicId;
    }

    /**
     * @return string|null
     */
    public function GetPublicId()
    {
        return $this->publicId;
    }

    public function GeneratePublicId()
    {
        $this->publicId = BookedStringHelper::Random(20);
    }
}

class ResourceAccessory
{
    public $ResourceId;
    public $MinQuantity;
    public $MaxQuantity;

    public function __construct($resourceId, $minQuantity, $maxQuantity)
    {
        $this->ResourceId = $resourceId;
        $this->MinQuantity = empty($minQuantity) ? null : (int)$minQuantity;
        $this->MaxQuantity = empty($maxQuantity) ? null : (int)$maxQuantity;;
    }
}
