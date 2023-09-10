<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ResourceTypeApiDto
{
    public $id;
    public $name;
    public $description;
    /** @var AttributeValueApiDto[] */
    public $attributeValues = [];

    /**
     * @param $types ResourceType[]
     * @return ResourceTypeApiDto[];
     */
    public static function FromList(array $types): array
    {
        $dtos = [];
        foreach ($types as $type) {
            $dtos[] = self::FromType($type);
        }

        return $dtos;
    }

    public static function FromType(ResourceType $type)
    {
        $dto = new ResourceTypeApiDto();
        $dto->id = intval($type->Id());
        $dto->name = apidecode($type->Name());
        $dto->description = $type->Description();
        $dto->attributeValues = AttributeValueApiDto::FromList($type->GetAttributeValuesAll());
        return $dto;
    }
}