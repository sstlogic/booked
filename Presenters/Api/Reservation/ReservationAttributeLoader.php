<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */
class ReservationAttributeLoader
{
    /**
     * @var IAttributeService
     */
    private $attributeService;

    public function __construct(IAttributeService $attributeService)
    {
        $this->attributeService = $attributeService;
    }

    /**
     * @param UserSession $currentUser
     * @param int $ownerId
     * @param int[] $resourceIds
     * @return CustomAttribute[]
     */
    public function Load(UserSession $currentUser, int $ownerId, array $resourceIds): array
    {
        /**
         * @var $definitions CustomAttribute[]
         */
        $definitions = array();

        $attributes = $this->attributeService->GetReservationAttributes($currentUser, new ReservationView(), $ownerId, $resourceIds);
        foreach ($attributes as $a) {
            $definitions[] = $a->Definition();
        }

        return $definitions;
    }
}