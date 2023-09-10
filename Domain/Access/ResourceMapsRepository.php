<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

interface IResourceMapsRepository
{
    /**
     * @return ResourceMap[]
     */
    public function LoadAll(): array;

    public function Add(ResourceMap $map, UploadedFile $image): int;

    public function LoadById(string $mapPublicId): ?ResourceMap;

    public function Update(ResourceMap $map): void;

    public function LoadPublished(): array;

    public function DeleteByPublicId(string $mapPublicId);
}

class ResourceMapsRepository implements IResourceMapsRepository
{
    public function LoadAll(?int $statusId = null): array
    {
        $maps = [];
        $reader = ServiceLocator::GetDatabase()->Query(new GetAllResourceMapsCommand($statusId));
        while ($row = $reader->GetRow()) {
            $maps[] = $this->MapFromRow($row);
        }
        return $maps;
    }

    public function LoadPublished(): array
    {
        return $this->LoadAll(ResourceMapStatus::Active);
    }

    public function Add(ResourceMap $map, UploadedFile $image): int
    {
        $publicId = BookedStringHelper::Random(20);

        $fileSystem = ServiceLocator::GetFileSystem();
        $extension = $image->Extension();
        $fileSystem->Save($fileSystem->GetResourceMapPath(), "$publicId.$extension", $image->Contents());

        $db = ServiceLocator::GetDatabase();
        $addMapCommand = new AddResourceMapCommand($map->GetName(), $publicId, $map->GetStatus(), $image->Extension(), $image->MimeType(), $image->Size());
        $mapId = $db->ExecuteInsert($addMapCommand);

        foreach ($map->GetData()->layers as $layer) {
            $addMapLayer = new AddResourceMapResourceCommand($mapId, $layer->layerId, $layer->resourceId, $layer->GetCoordinates());
            $db->ExecuteInsert($addMapLayer);
        }

        return $mapId;
    }

    public function Update(ResourceMap $map): void
    {
        $db = ServiceLocator::GetDatabase();
        $db->Execute(new UpdateResourceMapCommand($map->GetId(), $map->GetName(), $map->GetStatus()));
        $db->Execute(new DeleteResourceMapResourcesCommand($map->GetId()));

        foreach ($map->GetData()->layers as $layer) {
            $addMapLayer = new AddResourceMapResourceCommand($map->GetId(), $layer->layerId, $layer->resourceId, $layer->GetCoordinates());
            $db->ExecuteInsert($addMapLayer);
        }
    }

    public function GetImage($mapPublicId): ?ResourceMapImage
    {
        $command = new GetResourceMapCommand($mapPublicId);
        $reader = ServiceLocator::GetDatabase()->Query($command);
        if ($row = $reader->GetRow()) {
            return ResourceMapImage::FromRow($row);
        }

        return null;
    }

    public function LoadById(string $mapPublicId): ?ResourceMap
    {
        $command = new GetResourceMapCommand($mapPublicId);
        $reader = ServiceLocator::GetDatabase()->Query($command);
        if ($row = $reader->GetRow()) {
            $map = $this->MapFromRow($row);

            $resources = new GetResourceMapResourcesCommand($row[ColumnNames::RESOURCE_MAP_ID]);
            $resourcesReader = ServiceLocator::GetDatabase()->Query($resources);
            $data = new MapData();

            while ($resourcesRow = $resourcesReader->GetRow()) {
                $data->layers[] = MapDataLocationLayer::Create($resourcesRow[ColumnNames::PUBLIC_ID], $resourcesRow[ColumnNames::RESOURCE_ID], json_decode($resourcesRow[ColumnNames::RESOURCE_MAP_COORDINATES]));
            }
            $map->SetData($data);
            return $map;
        }

        return null;
    }

    private function MapFromRow(array $row): ResourceMap
    {
        $map = new ResourceMap();
        $map->SetId(intval($row[ColumnNames::RESOURCE_MAP_ID]));
        $map->SetPublicId($row[ColumnNames::PUBLIC_ID]);
        $map->SetName($row[ColumnNames::RESOURCE_MAP_NAME]);
        $map->SetStatus(intval($row[ColumnNames::RESOURCE_MAP_STATUS]));
        return $map;
    }

    public function DeleteByPublicId(string $mapPublicId)
    {
        ServiceLocator::GetDatabase()->Execute(new DeleteResourceMapCommand($mapPublicId));
    }
}