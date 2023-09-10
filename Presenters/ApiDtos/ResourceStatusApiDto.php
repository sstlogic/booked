<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ResourceStatusApiDto
{
    public $id;
    public $name;
    public $reasons = [];

    /**
     * @param ResourceStatusReason[] $statusReasons
     * @return ResourceStatusApiDto[]
     */
    public static function FromList(array $statusReasons): array
    {
        $resources = Resources::GetInstance();

        $available = new ResourceStatusApiDto();
        $available->id = ResourceStatus::AVAILABLE;
        $available->name = $resources->GetString('Available');
        self::AddReasons($available, $statusReasons);

        $unavailable = new ResourceStatusApiDto();
        $unavailable->id = ResourceStatus::UNAVAILABLE;
        $unavailable->name = $resources->GetString('Unavailable');
        self::AddReasons($unavailable, $statusReasons);

        $hidden = new ResourceStatusApiDto();
        $hidden->id = ResourceStatus::HIDDEN;
        $hidden->name = $resources->GetString('Hidden');
        self::AddReasons($hidden, $statusReasons);

        return [$available, $unavailable, $hidden];
    }

    /**
     * @param ResourceStatusApiDto $dto
     * @param ResourceStatusReason[] $statusReasons
     */
    private static function AddReasons(ResourceStatusApiDto $dto, array $statusReasons)
    {
        foreach ($statusReasons as $reason) {
            if ($reason->StatusId() == $dto->id) {
                $dto->reasons[] = ResourceStatusReasonApiDto::Create($reason->Id(), $reason->Description());
            }
        }
    }
}