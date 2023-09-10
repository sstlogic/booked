<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'Pages/Admin/ManageMapsPage.php');

class ManageMapsActions
{
    const SaveMap = 'saveMap';
    const UpdateMap = 'updateMap';
    const DeleteMap = 'deleteMap';
}

class ManageMapsPresenter extends ActionPresenter
{
    private IManageMapsPage $page;
    private IResourceMapsRepository $mapsRepository;
    private IResourceRepository $resourceRepository;

    public function __construct(IManageMapsPage $page, IResourceMapsRepository $mapsRepository, IResourceRepository $resourceRepository)
    {
        parent::__construct($page);
        $this->page = $page;
        $this->mapsRepository = $mapsRepository;
        $this->resourceRepository = $resourceRepository;

        $this->AddAction(ManageMapsActions::SaveMap, 'Add');
        $this->AddAction(ManageMapsActions::UpdateMap, 'Update');
        $this->AddAction(ManageMapsActions::DeleteMap, 'Delete');
    }

    public function PageLoad()
    {
        $maps = $this->mapsRepository->LoadAll();
        $this->page->BindMaps($maps);

        $resources = $this->resourceRepository->GetResourceList();
        $this->page->BindResources($resources);
    }

    public function Add()
    {
        $name = $this->page->GetMapName();
        $image = $this->page->GetMapImage();
        $isPublished = $this->page->GetIsPublished();
        $data = $this->page->GetMapData();

        if (!empty($name) && !empty($image) && !empty($data)) {

            $map = new ResourceMap();
            $map->SetData(MapData::CreateFromJson($data));
            $map->SetName($name);
            $map->SetIsPublished($isPublished);

            $this->mapsRepository->Add($map, $image);
        }
    }

    public function Update()
    {
        $id = $this->page->GetMapId();
        $name = $this->page->GetMapName();
        $isPublished = $this->page->GetIsPublished();
        $data = $this->page->GetMapData();

        if (!empty($id) && !empty($name) && !empty($data)) {

            $map = $this->mapsRepository->LoadById($id);
            $map->SetData(MapData::CreateFromJson($data));
            $map->SetName($name);
            $map->SetIsPublished($isPublished);

            $this->mapsRepository->Update($map);
        }
    }

    public function Delete() {
        $id = $this->page->GetMapId();
        Log::Debug("Deleting resource map.", ['id' => $id]);
        $this->mapsRepository->DeleteByPublicId($id);
    }

    public function LoadMap()
    {
        $id = $this->page->GetLoadMapId();

        $map = $this->mapsRepository->LoadById($id);

        $this->page->DisplayMapEdit($map);
    }
}
