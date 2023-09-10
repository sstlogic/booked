<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/ApiDtos/AttributeApiDto.php');
require_once(ROOT_DIR . 'Presenters/ApiDtos/AttributeValueApiDto.php');

class UserDetailsApiDto
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $firstName;
    /**
     * @var string
     */
    public $lastName;
    /**
     * @var string
     */
    public $fullName;
    /**
     * @var string
     */
    public $email;
    /**
     * @var string
     */
    public $organization;
    /**
     * @var string
     */
    public $position;
    /**
     * @var string
     */
    public $phone;
    /**
     * @var string
     */
    public $reservationColor;
    /**
     * @var AttributeApiDto[]
     */
    public $userAttributes = [];
    /**
     * @var AttributeValueApiDto[]
     */
    public $attributeValues = [];

    public static function FromUser(User $user, IEntityAttributeList $attributeList): UserDetailsApiDto
    {
        $dto = new UserDetailsApiDto();
        $dto->id = intval($user->Id());
        $dto->firstName = apidecode($user->FirstName());
        $dto->lastName = apidecode($user->LastName());
        $dto->fullName = apidecode($user->FullName());
        $dto->email = apidecode($user->EmailAddress());
        $dto->phone = apidecode($user->GetAttribute(UserAttribute::Phone));
        $dto->organization = apidecode($user->GetAttribute(UserAttribute::Organization));
        $dto->position = apidecode($user->GetAttribute(UserAttribute::Position));
        $dto->reservationColor = apidecode($user->GetPreference(UserPreferences::RESERVATION_COLOR));

        $attributes = $attributeList->GetAttributes($user->Id());
        foreach ($attributes as $a) {
            $dto->attributeValues[] = AttributeValueApiDto::Create($a->Id(), $a->Value());
            $dto->userAttributes[] = AttributeApiDto::FromAttribute($a->Definition());
        }

        return $dto;
    }
}
