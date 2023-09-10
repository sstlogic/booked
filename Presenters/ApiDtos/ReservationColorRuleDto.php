<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

class ReservationColorRuleDto
{
    public int $id;
    public int $attributeId;
    public string $color;
    public string $value;
    public int $comparisonType;
    public ?int $priority;

    public static function FromRule(ReservationColorRule $rule): ReservationColorRuleDto
    {
        $dto = new ReservationColorRuleDto();
        $dto->id = intval($rule->Id);
        $dto->attributeId = intval($rule->AttributeId);
        $dto->color = $rule->Color;
        $dto->value = $rule->RequiredValue;
        $dto->comparisonType = intval($rule->ComparisonType);
        $dto->priority = intval($rule->Priority);

        return $dto;
    }

    /**
     * @param array|ReservationColorRule[] $rules
     * @return array ReservationColorRuleDto[]
     */
    public static function FromList(array $rules): array
    {
        return array_map("ReservationColorRuleDto::FromRule", $rules);
    }
}