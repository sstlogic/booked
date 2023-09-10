<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ReservationDtoAsAttributesPage implements IReservationAttributesPage
{
    /**
     * @var ReservationApiDto
     */
    private $request;
    /**
     * @var Booked\Attribute[]
     */
    private $attributes;

    /**
     * @param ReservationApiDto $request
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    public function GetRequestedUserId()
    {
        return intval($this->request->ownerId);
    }

    public function GetRequestedReferenceNumber()
    {
        return $this->request->referenceNumber;
    }

    public function SetAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    public function GetRequestedResourceIds()
    {
        return array_map('intval', $this->request->resourceIds);
    }

    public function GetData()
    {
        $definitions = [];
        $values = [];
        foreach ($this->attributes as $attribute) {
            $definitions[] = $attribute->Definition();
            $values[] = new AttributeValue($attribute->Id(), $attribute->Value());
        }

        return ['attributes' => AttributeApiDto::FromList($definitions), 'attributeValues' => AttributeValueApiDto::FromList($values)];
    }
}