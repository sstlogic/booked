<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class GroupsApiDto
{
    public $id;
    public $name;
    public $isDefault = false;
    public $roles = [];

    /**
     * @param GroupItemView[] $groups
     * @return GroupsApiDto[]
     */
    public static function FromList(array $groups): array
    {
        $limitedView = false;
        foreach ($groups as $group) {
            if ($group->LimitedOnReservation()) {
                $limitedView = true;
                break;
            }
        }
        $dtos = [];
        foreach ($groups as $group) {
            if (!$limitedView || $group->LimitedOnReservation()) {
                $dtos[] = self::FromGroup($group);
            }
        }
        return $dtos;
    }

    private static function FromGroup(GroupItemView $group): GroupsApiDto
    {
        $dto = new GroupsApiDto();
        $dto->id = intval($group->Id());
        $dto->name = apidecode($group->Name());
        $dto->isDefault = intval($group->IsDefault()) == 1;
        $dto->roles = $group->RoleIds();
        return $dto;
    }
}