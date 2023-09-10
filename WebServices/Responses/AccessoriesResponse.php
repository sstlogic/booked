<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

class AccessoriesResponse extends RestResponse
{
    /**
     * @var AccessoryItemResponse
     */
    public $accessories;

    /**
     * @param IRestServer $server
     * @param AccessoryDto[] $accessories
     */
    public function __construct(IRestServer $server, $accessories)
    {
        /** @var $accessory AccessoryDto */
        foreach ($accessories as $accessory) {
            $this->accessories[] = new AccessoryItemResponse($server, $accessory);
        }
    }

    public static function Example()
    {
        return new ExampleAccessoriesResponse();
    }
}

class ExampleAccessoriesResponse extends AccessoriesResponse
{
    public function __construct()
    {
        $this->accessories = array(AccessoryItemResponse::Example());
    }
}

class AccessoryItemResponse extends RestResponse
{
    public $id;
    public $name;
    public $quantityAvailable;
    public $associatedResourceCount;
    public $creditCount;
    public $peakCreditCount;
    public $creditApplicability;
    public $creditsChargedAllSlots;

    public function __construct(IRestServer $server, AccessoryDto $accessory)
    {
        $this->id = intval($accessory->Id);
        $this->name = apidecode($accessory->Name);
        $this->quantityAvailable = intval($accessory->QuantityAvailable);
        $this->associatedResourceCount = $accessory->AssociatedResources;
        $this->creditCount = $accessory->CreditCount;
        $this->peakCreditCount = $accessory->PeakCreditCount;
        $this->creditApplicability = $accessory->CreditApplicability;
        $this->creditsChargedAllSlots = $accessory->CreditsChargedAllSlots;
        $this->AddService($server, WebServices::GetAccessory, array(WebServiceParams::AccessoryId => $this->id));
    }

    public static function Example()
    {
        return new ExampleAccessoryItemResponse();
    }
}

class ExampleAccessoryItemResponse extends AccessoryItemResponse
{
    public function __construct()
    {
        $this->id = 1;
        $this->name = 'accessoryName';
        $this->quantityAvailable = 3;
        $this->associatedResourceCount = 10;
        $this->creditCount = 1;
        $this->peakCreditCount = 2;
        $this->creditApplicability = 1;
    }
}