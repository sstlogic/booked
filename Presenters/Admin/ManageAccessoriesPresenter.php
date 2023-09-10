<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'lib/Graphics/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Admin/ImageUploadDirectory.php');

class ManageAccessoriesActions
{
    const Add = 'addAccessory';
    const Change = 'changeAccessory';
    const Delete = 'deleteAccessory';
    const ChangeAccessoryResource = 'changeAccessoryResource';
    const PrintQR = 'printQrCode';
}

class ManageAccessoriesPresenter extends ActionPresenter
{
    /**
     * @var IManageAccessoriesPage
     */
    private $page;

    /**
     * @var IAccessoryRepository
     */
    private $accessoryRepository;

    /**
     * @var IResourceRepository
     */
    private $resourceRepository;

    /**
     * @param IManageAccessoriesPage $page
     * @param IResourceRepository $resourceRepository
     * @param IAccessoryRepository $accessoryRepository
     */
    public function __construct(IManageAccessoriesPage $page, IResourceRepository $resourceRepository, IAccessoryRepository $accessoryRepository)
    {
        parent::__construct($page);

        $this->page = $page;
        $this->resourceRepository = $resourceRepository;
        $this->accessoryRepository = $accessoryRepository;

        $this->AddAction(ManageAccessoriesActions::Add, 'AddAccessory');
        $this->AddAction(ManageAccessoriesActions::Change, 'ChangeAccessory');
        $this->AddAction(ManageAccessoriesActions::Delete, 'DeleteAccessory');
        $this->AddAction(ManageAccessoriesActions::ChangeAccessoryResource, 'ChangeAccessoryResources');
        $this->AddAction(ManageAccessoriesActions::PrintQR, 'PrintQR');
    }

    public function PageLoad()
    {
        $accessories = $this->resourceRepository->GetAccessoryList($this->page->GetSortField(), $this->page->GetSortDirection());
        $resources = $this->resourceRepository->GetResourceList();

        $this->page->BindAccessories($accessories);
        $this->page->BindResources($resources);
    }

    public function AddAccessory()
    {
        $name = $this->page->GetAccessoryName();
        $quantity = $this->page->GetQuantityAvailable();

        Log::Debug('Adding new accessory', ['name' => $name, 'quantity' => $quantity]);

        $this->accessoryRepository->Add(Accessory::Create($name, $quantity));
    }

    public function ChangeAccessory()
    {
        $id = $this->page->GetAccessoryId();
        $name = $this->page->GetAccessoryName();
        $quantity = $this->page->GetQuantityAvailable();
        $credits = $this->page->GetCreditCount();
        $peakCredits = $this->page->GetPeakCreditCount();
        $creditApplicability = $this->page->GetCreditApplicability();
        $creditsBlockedSlots = $this->page->GetCreditsForBlockedSlots();

        Log::Debug('Changing accessory', ['id' => $id, 'name' => $name, 'quantity' => $quantity]);

        $accessory = $this->accessoryRepository->LoadById($id);
        $accessory->SetName($name);
        $accessory->SetQuantityAvailable($quantity);
        $accessory->ChangeCredits($credits, $peakCredits, $creditApplicability, $creditsBlockedSlots);

        $this->accessoryRepository->Update($accessory);
    }

    public function DeleteAccessory()
    {
        $id = $this->page->GetAccessoryId();

        Log::Debug('Deleting accessory', ['id' => $id]);

        $this->accessoryRepository->Delete($id);
    }

    public function ProcessDataRequest($dataRequest)
    {
        $accessory = $this->accessoryRepository->LoadById($this->page->GetAccessoryId());
        $this->page->SetAccessoryResources($accessory->Resources());
    }

    public function ChangeAccessoryResources()
    {
        $accessoryResources = array();
        $resources = $this->page->GetAccessoryResources();
        $min = $this->page->GetAccessoryResourcesMinimums();
        $max = $this->page->GetAccessoryResourcesMaximums();

        foreach ($resources as $resourceId) {
            $accessoryResources[] = new ResourceAccessory($resourceId, $min[$resourceId], $max[$resourceId]);
        }

        $accessory = $this->accessoryRepository->LoadById($this->page->GetAccessoryId());
        $accessory->ChangeResources($accessoryResources);
        $this->accessoryRepository->Update($accessory);
    }

    public function PrintQr()
    {
        $qrGenerator = new QRGenerator();

        $accessoryId = $this->page->GetAccessoryId();
        $accessory = $this->accessoryRepository->LoadById($accessoryId);

        if (!$accessory->HasPublicId()) {
            $accessory->GeneratePublicId();
            $this->accessoryRepository->Update($accessory);
        }

        $imageUploadDir = new ImageUploadDirectory();
        $imageName = "/accessory-qr-{$accessory->GetPublicId()}.png";
        $url = $imageUploadDir->GetPath() . $imageName;
        $savePath = $imageUploadDir->GetDirectory() . $imageName;

        $qrPath = sprintf('%s/%s?%s=%s', Configuration::Instance()->GetScriptUrl(), Pages::ACCESSORY_QR_ROUTER, QueryStringKeys::PUBLIC_ID, $accessory->GetPublicId());
        $qrGenerator->SavePng($qrPath, $savePath);

        $this->page->ShowQRCode($url, $accessory->GetName());
    }
}