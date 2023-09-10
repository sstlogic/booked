<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */
class ResourceLoader
{
    /**
     * @var IResourceService
     */
    private $resourceService;
    /**
     * @var IAttributeService
     */
    private $attributeService;

    public function __construct(IResourceService $resourceService, IAttributeService $attributeService)
    {
        $this->resourceService = $resourceService;
        $this->attributeService = $attributeService;
    }

    /**
     * @param int $scheduleId
     * @param UserSession $user
     * @return array|BookableResource[]
     */
    public function Load(int $scheduleId, UserSession $user)
    {
        return $this->resourceService->GetScheduleBookableResources($scheduleId, false, $user);
    }

    /**
     * @param UserSession $user
     * @return array|CustomAttribute[]
     */
    public function LoadAttributes(UserSession $user): array
    {
        $attributes = $this->attributeService->GetByCategory(CustomAttributeCategory::RESOURCE);
        if ($user->IsAdmin) {
            return $attributes;
        }
        $validAttributes = [];
        foreach ($attributes as $a) {
            if (!$a->AdminOnly()) {
                $validAttributes[] = $a;
            }
        }

        return $validAttributes;
    }
}