<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ResourceTreeUpdateApiDto
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string|null
     */
    public $parentId;
    /**
     * @var int[]
     */
    public $resourceIds;
}