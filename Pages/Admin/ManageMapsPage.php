<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/Admin/AdminPage.php');
require_once(ROOT_DIR . 'Presenters/Admin/ManageMapsPresenter.php');

interface IManageMapsPage extends IActionPage
{
    /**
     * @param array|ResourceMap[] $maps
     */
    public function BindMaps(array $maps);

    /**
     * @param array|BookableResource[] $resources
     */
    public function BindResources(array $resources);

    public function GetMapName(): ?string;

    public function GetMapImage(): ?UploadedFile;

    public function GetIsPublished(): bool;

    public function GetMapData(): ?string;

    public function GetLoadMapId(): ?string;

    public function DisplayMapEdit(?ResourceMap $map);

    public function GetMapId(): ?string;
}

class ManageMapsPage extends ActionPage implements IManageMapsPage, IPageWithId
{
    private ManageMapsPresenter $presenter;

    public function __construct()
    {
        parent::__construct('ManageMaps', 2);
        $this->presenter = new ManageMapsPresenter($this, new ResourceMapsRepository(), new ResourceRepository());
        $this->Set('PageId', $this->GetPageId());
    }

    public function ProcessAction()
    {
        $this->presenter->ProcessAction();
    }

    public function ProcessDataRequest($dataRequest)
    {
        if ($dataRequest == "edit") {
            $this->presenter->LoadMap();
        } else {
            $this->Display('Admin/Maps/add-map.tpl');
        }
    }

    public function ProcessPageLoad()
    {
        $this->Set('resourceMaps', []);
        $this->presenter->PageLoad();
        $this->Display('Admin/Maps/manage-maps.tpl');
    }

    public function DisplayMapEdit(?ResourceMap $map)
    {
        if (!empty($map)) {
            $this->Set('MapName', $map->GetName());
            $this->Set('MapData', json_encode($map->GetData()));
            $this->Set('MapId', $map->GetPublicId());
            $this->Set('IsPublished', $map->GetIsPublished());
            $this->Set('MapImageUrl', $map->GetImageUrl());
            $this->Display('Admin/Maps/edit-map.tpl');
        }

    }

    public function BindMaps(array $maps)
    {
        $this->Set('resourceMaps', $maps);
    }

    public function BindResources(array $resources)
    {
        $this->Set('resources', $resources);
    }

    public function GetMapName(): ?string
    {
        return $this->GetForm(FormKeys::MAP_NAME);
    }

    public function GetMapId(): ?string
    {
        return $this->GetForm(FormKeys::MAP_ID);
    }

    public function GetMapImage(): ?UploadedFile
    {
        return $this->GetFile(FormKeys::MAP_IMAGE);
    }

    public function GetIsPublished(): bool
    {
        return $this->GetCheckbox(FormKeys::MAP_IS_PUBLISHED);
    }

    public function GetMapData(): ?string
    {
        return $this->GetRawForm(FormKeys::MAP_DATA);
    }

    public function GetLoadMapId(): ?string
    {
        return $this->GetQuerystring("id");
    }

    public function GetPageId(): int
    {
        return AdminPageIds::ResourceMaps;
    }
}