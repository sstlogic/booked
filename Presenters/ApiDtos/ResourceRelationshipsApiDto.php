<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ResourceRelationshipsApiDto
{
    public $required = [];
    public $requiredOneWay = [];
    public $excluded = [];
    public $excludedAtTime = [];

    /**
     * @param $required int[]
     * @param $excluded int[]
     * @param $excludedAtTime int[]
     * @return ResourceRelationshipsApiDto
     */
    public static function Create(array $required, array $excluded, array $excludedAtTime, array $requiredOneWay): ResourceRelationshipsApiDto
    {
        $dto = new ResourceRelationshipsApiDto();
        $dto->required = array_map("intval", $required);
        $dto->requiredOneWay = array_map("intval", $requiredOneWay);
        $dto->excluded = array_map("intval", $excluded);
        $dto->excludedAtTime = array_map("intval", $excludedAtTime);

        return $dto;
    }
}