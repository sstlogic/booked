<?php

/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

class AccessoryResponse extends RestResponse
{
    public $id;
    public $name;
    public $quantityAvailable;
    public $associatedResources = array();
	public $creditCount;
    public $peakCreditCount;
    public $creditApplicability;

    public function __construct(IRestServer $server, Accessory $accessory)
    {
        $this->id = $accessory->GetId();
        $this->name = apidecode($accessory->GetName());
        $this->quantityAvailable = $accessory->GetQuantityAvailable();
        $this->associatedResources = $this->GetResources($server, $accessory->Resources());
        $this->creditCount = $accessory->GetCreditCount();
        $this->peakCreditCount = $accessory->GetPeakCreditCount();
        $this->creditApplicability = $accessory->GetCreditApplicability();
    }

    public static function Example()
    {
        return new ExampleAccessoryResponse();
    }

    /**
     * @param IRestServer $server
     * @param ResourceAccessory[] $resources
     * @return AssociatedResourceResponse[]
     */
    private function GetResources(IRestServer $server, $resources)
    {
        $items = array();
        foreach ($resources as $r) {
            $items[] = new AssociatedResourceResponse($server, $r);
        }

        return $items;
    }
}

class AssociatedResourceResponse extends RestResponse
{
    public $resourceId;
    public $minQuantity;
    public $maxQuantity;

    public function __construct(IRestServer $server, ResourceAccessory $resourceAccessory)
    {
        $this->resourceId = $resourceAccessory->ResourceId;
        $this->minQuantity = $resourceAccessory->MinQuantity;
        $this->maxQuantity = $resourceAccessory->MaxQuantity;
        $this->AddService($server, WebServices::GetResource, array(WebServiceParams::ResourceId => $resourceAccessory->ResourceId));
    }

    public static function Example()
    {
        return new ExampleAssociatedResourceResponse();
    }
}

class ExampleAccessoryResponse extends AccessoryResponse
{
    public function __construct()
    {
        $this->id = 1;
        $this->name = 'accessoryName';
        $this->quantityAvailable = 10;
        $this->associatedResources = array(AssociatedResourceResponse::Example());
        $this->creditCount = 1;
        $this->peakCreditCount = 2;
        $this->creditApplicability = CreditApplicability::SLOT;
    }
}

class ExampleAssociatedResourceResponse extends AssociatedResourceResponse
{
    public function __construct()
    {
        $this->resourceId = 1;
        $this->maxQuantity = 10;
        $this->minQuantity = 4;

    }
}