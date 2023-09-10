<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ResourceAddDto
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var AttributeValueApiDto[]
     */
    public $attributeValues;
    /**
     * @var int|null
     */
    public $typeId;
    /**
     * @var string|null
     */
    public $newType;
    /**
     * @var int
     */
    public $scheduleId;
    /**
     * @var int|null
     */
    public $adminGroupId;
    /**
     * @var bool
     */
    public $requiresApproval;
    /**
     * @var bool
     */
    public $autoPermission;
}