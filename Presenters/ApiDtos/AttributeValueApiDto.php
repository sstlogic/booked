<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class AttributeValueApiDto
{
    public $id;
    public $value;

    /**
     * @param int $id
     * @param string|null $value
     * @return AttributeValueApiDto
     */
    public static function Create($id, ?string $value): AttributeValueApiDto
    {
        $dto = new AttributeValueApiDto();
        $dto->id = intval($id);
        $dto->value = $value;

        return $dto;
    }

    /**
     * @param AttributeValue[] $attributeValues
     * @return AttributeValueApiDto[]
     */
    public static function FromList(array $attributeValues): array
    {
        $dtos = [];
        foreach ($attributeValues as $v) {
            $dtos[] = AttributeValueApiDto::Create($v->AttributeId, apidecode($v->Value));
        }
        return $dtos;
    }
}
