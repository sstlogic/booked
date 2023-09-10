<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once ROOT_DIR . 'Presenters/ApiDtos/ApiHelperFunctions.php';

class ResourceGroupApiDto
{
    public $id;
    public $name;
    public $parentId;

    /**
     * @param ResourceGroup[] $groups
     * @return ResourceGroupApiDto[]
     */
    public static function FromList(array $groups): array {
        $dtos = [];
        foreach($groups as $group) {
            $dto = new ResourceGroupApiDto();
            $dto->id = intval($group->id);
            $dto->parentId = empty($group->parent_id) ? null : intval($group->parent_id);
            $dto->name = apidecode($group->name);
            $dtos[] = $dto;
        }

        return $dtos;
    }
}