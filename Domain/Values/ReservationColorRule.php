<?php
/**
 * Copyright 2014-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Values/ComparisonType.php');

class ReservationColorRule
{
    public $Id;
    public $AttributeId;
    public $AttributeType;
    public $AttributeName = '';
    public $RequiredValue = '';
    public $ComparisonType;
    public $Color = '';
    public $Priority = 0;

    public function SetAttributeId($attributeId)
    {
        $this->AttributeId = intval($attributeId);
    }

    public function SetRequiredValue($requiredValue, $comparisonType)
    {
        $this->RequiredValue = $comparisonType == ComparisonType::Empty ? "" : $requiredValue;
    }

    public function SetColor($color)
    {
        $this->Color = BookedStringHelper::StartsWith($color, "#") ? $color : "#$color";
    }

    public function SetComparisonType($comparisonType, $requiredValue)
    {
        $this->ComparisonType = $requiredValue == "" ? ComparisonType::Empty : intval($comparisonType);
    }

    public function SetPrioity($priority)
    {
        if (empty($priority)) {
            $this->Priority = 0;
        } else {
            $this->Priority = intval($priority);
        }
    }

    /**
     * @param int $attributeId
     * @param string $requiredValue
     * @param string $color
     * @param int|ComparisonType $comparisonType
     * @param int $priority
     * @return ReservationColorRule
     */
    public static function Create($attributeId, $requiredValue, $color, $comparisonType, $priority = 0)
    {
        $rule = new ReservationColorRule();
        $rule->SetAttributeId($attributeId);
        $rule->SetRequiredValue($requiredValue, $comparisonType);
        $rule->SetColor($color);
        $rule->SetComparisonType($comparisonType, $requiredValue);
        $rule->SetPrioity($priority);
        return $rule;
    }

    /**
     * @param array $row
     * @return ReservationColorRule
     */
    public static function FromRow($row)
    {
        $rule = self::Create(
            $row[ColumnNames::ATTRIBUTE_ID],
            $row[ColumnNames::REQUIRED_VALUE],
            $row[ColumnNames::RESERVATION_COLOR],
            $row[ColumnNames::COMPARISON_TYPE],
        );
        $rule->AttributeName = $row[ColumnNames::ATTRIBUTE_LABEL];
        $rule->AttributeType = intval($row[ColumnNames::ATTRIBUTE_TYPE]);
        $rule->Id = intval($row[ColumnNames::RESERVATION_COLOR_RULE_ID]);
        $rule->SetPrioity($row[ColumnNames::RESERVATION_COLOR_RULE_PRIORITY]);

        return $rule;
    }

    public function IsSatisfiedBy(ReservationItemView $reservation)
    {
        $value = $reservation->Attributes->Get($this->AttributeId);

        if ($value == null || $value == "") {
            return $this->ComparisonType == ComparisonType::Empty;
        }

        if ($this->ComparisonType == ComparisonType::Equals) {
            return strtolower($value) == strtolower($this->RequiredValue);
        }

        if ($this->ComparisonType == ComparisonType::Contains) {
            return str_contains($this->RequiredValue, $value);
        }

        if ($this->AttributeType == CustomAttributeTypes::DATETIME) {
            try {
                $dateValue = Date::Parse($value);
                $requiredValue = Date::Parse($this->RequiredValue);

                if ($this->ComparisonType == ComparisonType::LessThan) {
                    return $dateValue->LessThan($requiredValue);
                }
                if ($this->ComparisonType == ComparisonType::GreaterThan) {
                    return $dateValue->GreaterThan($requiredValue);
                }
            } catch (Throwable $ex) {
                return false;
            }
        }

        return false;
    }

    public function IsHigherPriority($priority)
    {
        if (is_null($priority) || $priority === 0)
        {
            return true;
        }

        if (is_null($this->Priority) && $priority > 0) {
            return false;
        }

        return intval($this->Priority) < intval($priority);
    }
}