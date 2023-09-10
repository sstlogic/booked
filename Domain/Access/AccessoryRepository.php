<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Accessory.php');

interface IAccessoryRepository
{
    /**
     * @param int $accessoryId
     * @return Accessory
     */
    public function LoadById($accessoryId);

    /**
     * @return Accessory[]
     */
    public function LoadAll();

    /**
     * @param Accessory $accessory
     * @return int
     */
    public function Add(Accessory $accessory);

    /**
     * @param Accessory $accessory
     * @return void
     */
    public function Update(Accessory $accessory);

    /**
     * @param int $accessoryId
     * @return void
     */
    public function Delete($accessoryId);
}

class AccessoryRepository implements IAccessoryRepository
{
    /**
     * @param int $accessoryId
     * @return Accessory
     */
    public function LoadById($accessoryId)
    {
        $reader = ServiceLocator::GetDatabase()->Query(new GetAccessoryByIdCommand($accessoryId));

        if ($row = $reader->GetRow()) {
            $accessory = new Accessory($row[ColumnNames::ACCESSORY_ID], $row[ColumnNames::ACCESSORY_NAME], $row[ColumnNames::ACCESSORY_QUANTITY]);
            $accessory->ChangeCredits($row[ColumnNames::CREDIT_COUNT], $row[ColumnNames::PEAK_CREDIT_COUNT], $row[ColumnNames::CREDIT_APPLICABILITY], $row[ColumnNames::CREDITS_CHARGED_ALL_SLOTS]);
            $accessory->WithPublicId($row[ColumnNames::PUBLIC_ID]);
            $arReader = ServiceLocator::GetDatabase()->Query(new GetAccessoryResources($accessoryId));

            while ($row = $arReader->GetRow()) {
                $accessory->AddResource($row[ColumnNames::RESOURCE_ID], $row[ColumnNames::ACCESSORY_MINIMUM_QUANTITY],
                    $row[ColumnNames::ACCESSORY_MAXIMUM_QUANTITY]);
            }

            $reader->Free();

            return $accessory;
        }

        return null;
    }

    /**
     * @param Accessory $accessory
     * @return int
     */
    public function Add(Accessory $accessory)
    {
        return ServiceLocator::GetDatabase()->ExecuteInsert(new AddAccessoryCommand($accessory->GetName(), $accessory->GetQuantityAvailable()));
    }

    /**
     * @param Accessory $accessory
     * @return void
     */
    public function Update(Accessory $accessory)
    {
        ServiceLocator::GetDatabase()->Execute(new UpdateAccessoryCommand($accessory->GetId(),
            $accessory->GetName(),
            $accessory->GetQuantityAvailable(),
            $accessory->GetCreditCount(),
            $accessory->GetPeakCreditCount(),
            $accessory->GetCreditApplicability(),
            $accessory->GetCreditsAlwaysCharged(),
            $accessory->GetPublicId()));
        ServiceLocator::GetDatabase()->Execute(new DeleteAcccessoryResourcesCommand($accessory->GetId()));
        foreach ($accessory->Resources() as $resource) {
            ServiceLocator::GetDatabase()->Execute(new AddAccessoryResourceCommand($accessory->GetId(), $resource->ResourceId, $resource->MinQuantity,
                $resource->MaxQuantity));
        }
    }

    /**
     * @param int $accessoryId
     * @return void
     */
    public function Delete($accessoryId)
    {
        ServiceLocator::GetDatabase()->Execute(new DeleteAccessoryCommand($accessoryId));
    }

    /**
     * @return Accessory[]
     */
    public function LoadAll()
    {
        $reader = ServiceLocator::GetDatabase()->Query(new GetAllAccessoriesCommand());
        $accessories = array();

        while ($row = $reader->GetRow()) {
            $accessory = new Accessory($row[ColumnNames::ACCESSORY_ID], $row[ColumnNames::ACCESSORY_NAME], $row[ColumnNames::ACCESSORY_QUANTITY]);
            $accessory->WithPublicId($row[ColumnNames::PUBLIC_ID]);

            $resourceList = $row[ColumnNames::RESOURCE_ACCESSORY_LIST];
            if (!empty($resourceList)) {
                $pairs = explode('!sep!', $resourceList);

                foreach ($pairs as $pair) {
                    $nv = explode(',', $pair);
                    $accessory->AddResource($nv[0], $nv[1], $nv[2]);
                }
            }

            $accessories[] = $accessory;
        }

        $reader->Free();
        return $accessories;
    }
}