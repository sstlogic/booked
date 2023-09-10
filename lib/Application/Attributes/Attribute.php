<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

namespace Booked;

class Attribute
{
    /**
     * @var \CustomAttribute
     */
    private $attributeDefinition;

    /**
     * @var mixed
     */
    private $value;

    public function __construct(\CustomAttribute $attributeDefinition, $value = null)
    {
        $this->attributeDefinition = $attributeDefinition;
        $this->value = $value;
    }

    /**
     * @return \CustomAttribute
     */
    public function Definition()
    {
        return $this->attributeDefinition;
    }

    /**
     * @return string
     */
    public function Label()
    {
        return $this->attributeDefinition->Label();
    }

    /**
     * @return \CustomAttributeCategory|int
     */
    public function Category()
    {
        return $this->attributeDefinition->Category();
    }

    /**
     * @return string
     */
    public function Regex()
    {
        return $this->attributeDefinition->Regex();
    }

    /**
     * @return int
     */
    public function Id()
    {
        return $this->attributeDefinition->Id();
    }

    /**
     * @return string|null
     */
    public function Value()
    {
        return $this->value;
    }

    /**
     * @return \CustomAttributeTypes|int
     */
    public function Type()
    {
        return $this->attributeDefinition->Type();
    }

    /**
     * @return array|string[]
     */
    public function PossibleValueList()
    {
        return $this->attributeDefinition->PossibleValueList();
    }

    /**
     * @return bool
     */
    public function Required()
    {
        return $this->attributeDefinition->Required();
    }

    /**
     * @return bool
     */
    public function AdminOnly()
    {
        return $this->attributeDefinition->AdminOnly();
    }

    /**
     * @return int
     */
    public function SortOrder()
    {
        return $this->attributeDefinition->SortOrder();
    }

    /**
     * @param $value mixed
     */
    public function SetValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return bool
     */
    public function UniquePerEntity()
    {
        return $this->attributeDefinition->UniquePerEntity();
    }

    /**
     * @return int[]
     */
    public function EntityIds()
    {
        return $this->attributeDefinition->EntityIds();
    }

    /**
     * @return bool
     */
    public function IsPrivate()
    {
        return $this->attributeDefinition->IsPrivate();
    }

    /**
     * @return \CustomAttributeCategory|int|null
     */
    public function SecondaryCategory()
    {
        return $this->attributeDefinition->SecondaryCategory();
    }

    /**
     * @return int[]|null
     */
    public function SecondaryEntityIds()
    {
        return $this->attributeDefinition->SecondaryEntityIds();
    }
}