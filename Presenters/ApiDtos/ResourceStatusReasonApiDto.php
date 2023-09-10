<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ResourceStatusReasonApiDto
{
    public $id;
    public $description;

    public static function Create(int $id, string $description): ResourceStatusReasonApiDto
    {
        $dto = new ResourceStatusReasonApiDto();
        $dto->id = intval($id);
        $dto->description = apidecode($description);
        return $dto;
    }
}