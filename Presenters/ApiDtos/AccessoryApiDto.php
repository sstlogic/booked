<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class AccessoryApiDto
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $name;
    /**
     * @var int|null
     */
    public $quantityAvailable;
    /**
     * @var ResourceAccessoryApiDto[]
     */
    public $resources;
    /**
     * @var string|null
     */
    public $publicId;

    /**
     * @param Accessory[] $accessories
     * @return AccessoryApiDto[]
     */
    public static function FromList($accessories)
    {
        $dtos = [];
        foreach ($accessories as $accessory) {
            $dto = new AccessoryApiDto();
            $dto->id = intval($accessory->GetId());
            $dto->name = apidecode($accessory->GetName());
            $dto->quantityAvailable = empty($accessory->GetQuantityAvailable()) ? null : intval($accessory->GetQuantityAvailable());
            $dto->publicId = $accessory->GetPublicId();

            $dto->resources = [];
            foreach ($accessory->Resources() as $resourceAccessory) {
                $raDto = new ResourceAccessoryApiDto();
                $raDto->resourceId = intval($resourceAccessory->ResourceId);
                $raDto->maximumQuantity = empty($resourceAccessory->MaxQuantity) ? null : intval($resourceAccessory->MaxQuantity);
                $raDto->minimumQuantity = empty($resourceAccessory->MinQuantity) ? null : intval($resourceAccessory->MinQuantity);
                $dto->resources[] = $raDto;
            }
            $dtos[] = $dto;
        }
        return $dtos;
    }
}

class ResourceAccessoryApiDto
{
    /**
     * @var int
     */
    public $resourceId;
    /**
     * @var int|null
     */
    public $minimumQuantity;
    /**
     * @var int|null
     */
    public $maximumQuantity;
}