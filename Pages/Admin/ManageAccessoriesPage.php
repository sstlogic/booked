<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/Admin/AdminPage.php');
require_once(ROOT_DIR . 'Presenters/Admin/ManageAccessoriesPresenter.php');

interface IManageAccessoriesPage extends IActionPage, IPageWithId
{
    /**
     * @return int
     */
    public function GetAccessoryId();

    /**
     * @return string
     */
    public function GetAccessoryName();

    /**
     * @return int
     */
    public function GetQuantityAvailable();

    /**
     * @return int
     */
    public function GetCreditCount();

    /**
     * @return int
     */
    public function GetPeakCreditCount();

    /**
     * @return int
     */
    public function GetCreditApplicability();

    /**
     * @return bool
     */
    public function GetCreditsForBlockedSlots();

    /**
     * @param $accessories AccessoryDto[]
     */
    public function BindAccessories($accessories);

    /**
     * @param BookableResource[] $resources
     */
    public function BindResources($resources);

    /**
     * @param ResourceAccessory[] $resources
     */
    public function SetAccessoryResources($resources);

    /**
     * @return string[]
     */
    public function GetAccessoryResources();

    /**
     * @return string[]
     */
    public function GetAccessoryResourcesMinimums();

    /**
     * @return string[]
     */
    public function GetAccessoryResourcesMaximums();

    /**
     * @param string $qrCodeImageUrl
     * @param string $accessoryName
     */
    public function ShowQRCode($qrCodeImageUrl, $accessoryName);
}

class ManageAccessoriesPage extends ActionPage implements IManageAccessoriesPage
{
    private ManageAccessoriesPresenter $presenter;

    public function __construct()
    {
        parent::__construct('ManageAccessories', 1);

        $this->Set('PageId', $this->GetPageId());
        $this->presenter = new ManageAccessoriesPresenter($this, new ResourceRepository(), new AccessoryRepository());
    }

    public function ProcessPageLoad()
    {
        $this->presenter->PageLoad();

        $this->Set('CreditsEnabled', Configuration::Instance()->GetSectionKey(ConfigSection::CREDITS, ConfigKeys::CREDITS_ENABLED, new BooleanConverter()));
        $this->Display('Admin/manage_accessories.tpl');
    }

    public function BindAccessories($accessories)
    {
        $this->Set('accessories', $accessories);
    }

    public function ProcessAction()
    {
        $this->presenter->ProcessAction();
    }

    /**
     * @return int
     */
    public function GetAccessoryId()
    {
        return $this->GetQuerystring(QueryStringKeys::ACCESSORY_ID);
    }

    /**
     * @return string
     */
    public function GetAccessoryName()
    {
        return $this->GetForm(FormKeys::ACCESSORY_NAME);
    }

    /**
     * @return int
     */
    public function GetQuantityAvailable()
    {
        return $this->GetForm(FormKeys::ACCESSORY_QUANTITY_AVAILABLE);
    }

    public function ProcessDataRequest($dataRequest)
    {
        $this->presenter->ProcessDataRequest($dataRequest);
    }

    /**
     * @param BookableResource[] $resources
     */
    public function BindResources($resources)
    {
        $this->Set('resources', $resources);
    }

    /**
     * @param ResourceAccessory[] $resources
     */
    public function SetAccessoryResources($resources)
    {
        $this->SetJson($resources);
    }

    /**
     * @return string[]
     */
    public function GetAccessoryResources()
    {
        $r = $this->GetForm(FormKeys::ACCESSORY_RESOURCE);
        if (empty($r)) {
            return array();
        }

        return $r;
    }

    /**
     * @return string[]
     */
    public function GetAccessoryResourcesMinimums()
    {
        $r = $this->GetForm(FormKeys::ACCESSORY_MIN_QUANTITY);
        if (empty($r)) {
            return array();
        }

        return $r;
    }

    /**
     * @return string[]
     */
    public function GetAccessoryResourcesMaximums()
    {
        $r = $this->GetForm(FormKeys::ACCESSORY_MAX_QUANTITY);
        if (empty($r)) {
            return array();
        }

        return $r;
    }

    public function GetCreditCount()
    {
        return $this->GetForm(FormKeys::CREDITS);
    }

    public function GetPeakCreditCount()
    {
        return $this->GetForm(FormKeys::PEAK_CREDITS);
    }

    public function GetCreditApplicability()
    {
        return $this->GetForm(FormKeys::CREDITS_APPLICABILITY);
    }

    public function GetCreditsForBlockedSlots()
    {
        return $this->GetCheckbox(FormKeys::CREDITS_BLOCKED_SLOTS);
    }

    public function ShowQRCode($qrCodeImageUrl, $accessoryName)
    {
        $this->Set('QRImageUrl', $qrCodeImageUrl);
        $this->Set('Name', $accessoryName);
        $this->Set('HelpText', "Scan this code when making a reservation to quickly add this accessory");

        $this->Display('Admin/Resources/show_resource_qr.tpl');
    }

    public function GetPageId(): int
    {
        return AdminPageIds::Accessories;
    }
}