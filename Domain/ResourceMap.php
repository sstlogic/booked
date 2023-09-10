<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

class ResourceMap
{
    private string $name;
    private bool $isPublished;
    private MapData $mapData;
    private string $publicId;
    private int $id = 0;

    public function SetPublicId($publicId): void
    {
        $this->publicId = $publicId;
    }

    public function GetPublicId(): string
    {
        return $this->publicId;
    }

    public function SetId(int $id): void
    {
        $this->id = $id;
    }

    public function GetId(): int
    {
        return $this->id;
    }

    public function SetName(string $name): void
    {
        $this->name = $name;
    }

    public function GetName(): string
    {
        return $this->name;
    }

    public function SetIsPublished(bool $isPublished): void
    {
        $this->isPublished = $isPublished;
    }

    public function GetIsPublished(): bool
    {
        return $this->isPublished;
    }

    public function SetStatus(int $status): void
    {
        $this->isPublished = $status == ResourceMapStatus::Active;
    }

    public function GetStatus(): int
    {
        return $this->isPublished ? ResourceMapStatus::Active : ResourceMapStatus::Draft;
    }

    public function SetData(MapData $mapData): void
    {
        $this->mapData = $mapData;
    }

    public function GetData(): MapData
    {
        return $this->mapData;
    }

    public function GetImageUrl(): string
    {
        return Configuration::Instance()->GetScriptUrl() . '/maps/image.php?pid=' . $this->publicId;
    }
}

class ResourceMapStatus
{
    public const Active = 1;
    public const Draft = 2;
}

class MapData
{
    /**
     * @var MapDataLocationLayer[]
     */
    public $layers = [];

    public static function CreateFromJson(string $data): MapData
    {
        $json = json_decode($data);
        $data = new MapData();
        $data->layers = array_map('MapDataLocationLayer::CreateFromJson', $json->layers);
        return $data;
    }
}

class MapDataCoordinates
{
    /**
     * @var array|MapDataLatLong[]|null
     */
    public array $latLngs = [];
    public ?string $radius = null;

    public static function Create(array $latLngs, ?string $radius): MapDataCoordinates
    {
        $dto = new MapDataCoordinates();
        $dto->radius = $radius;
        $dto->latLngs = $latLngs;
        return $dto;
    }
}

class MapDataLocationLayer
{
    public string $layerId;
    public string $resourceId;
    /**
     * @var MapDataCoordinates
     */
    public $coordinates;

    public static function Create(string $layerId, string $resourceId, $coordinates): MapDataLocationLayer
    {
        $dto = new MapDataLocationLayer();
        $dto->layerId = $layerId;
        $dto->resourceId = $resourceId;
        $dto->coordinates = $coordinates;
        return $dto;
    }

    public static function CreateFromJson($json): MapDataLocationLayer
    {
        $coordinates = new MapDataCoordinates();
        if (isset($json->latLngs) && !empty($json->latLngs)) {
            $coordinates->latLngs = array_map('MapDataLatLong::CreateFromJson', $json->latLngs);
        }
        if (isset($json->radius) && !empty($json->radius)) {
            $coordinates->radius = $json->radius . '';
        }
        return self::Create($json->layerId, $json->resourceId, $coordinates);
    }

    public function GetCoordinates()
    {
        return $this->coordinates;
    }
}

class MapDataLatLong
{
    public float $lat;
    public float $lng;

    public static function Create($lat, $lng): MapDataLatLong
    {
        $dto = new MapDataLatLong();
        $dto->lat = $lat;
        $dto->lng = $lng;
        return $dto;
    }

    public static function CreateFromJson($json): ?MapDataLatLong
    {
        if (empty($json)) {
            return null;
        }
        return self::Create($json->lat, $json->lng);
    }
}

class ResourceMapImage
{
    private string $name;
    private string $contents;
    private string $type;
    private string $extension;
    private int $size = 0;

    public static function FromRow($row): ResourceMapImage
    {
        $fileId = $row[ColumnNames::PUBLIC_ID];
        $extension = $row[ColumnNames::FILE_EXTENSION];
        $fileName = "$fileId.$extension";
        $fileSystem = ServiceLocator::GetFileSystem();
        $contents = $fileSystem->GetFileContents($fileSystem->GetResourceMapPath() . $fileName);
        $image = new ResourceMapImage();
        $image->SetFileName($fileName);
        $image->SetFileType($row[ColumnNames::FILE_TYPE]);
        $image->SetFileSize($row[ColumnNames::FILE_SIZE]);
        $image->SetContents($contents);
        $image->SetFileExtension($row[ColumnNames::FILE_EXTENSION]);

        return $image;
    }

    public function FileContents(): string
    {
        return $this->contents;
    }

    public function FileType(): string
    {
        return $this->type;
    }

    public function FileName(): string
    {
        return $this->name;
    }

    public function Size()
    {
        return $this->size;
    }

    public function Extension(): string
    {
        return $this->extension;
    }

    private function SetFileName(string $name)
    {
        $this->name = $name;
    }

    private function SetFileType(string $type)
    {
        $this->type = $type;
    }

    private function SetContents(?string $contents)
    {
        $this->contents = $contents . '';
    }

    private function SetFileExtension(string $extension)
    {
        $this->extension = $extension;
    }

    private function SetFileSize($size)
    {
        $this->size = empty($size) ? 0 : intval($size);
    }
}