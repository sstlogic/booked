<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/Admin/AdminPage.php');
require_once(ROOT_DIR . 'Pages/IPageable.php');
require_once(ROOT_DIR . 'Pages/Ajax/AutoCompletePage.php');
require_once(ROOT_DIR . 'Presenters/Admin/ManageSchedulesPresenter.php');
require_once(ROOT_DIR . 'Domain/Access/ScheduleRepository.php');
require_once(ROOT_DIR . 'lib/Application/Attributes/namespace.php');

interface IManageResourcesPage extends IActionPage
{
    /**
     * @return int
     */
    public function GetResourceId();

    /**
     * @return int
     */
    public function GetApiResourceId();

    /**
     * @return string[]
     */
    public function GetApiImageNames();

    /**
     * @return UploadedFile[]
     */
    public function GetApiUploadedImages();

    /**
     * @param string $qrCodeImageUrl
     * @param string $resourceName
     */
    public function ShowQRCode($qrCodeImageUrl, $resourceName);

}

class ManageResourcesPage extends ActionPage implements IManageResourcesPage, IPageWithId
{
    protected ManageResourcesPresenter $presenter;

    public function __construct()
    {
        parent::__construct('ManageResources', 2);
        $this->presenter = new ManageResourcesPresenter(
            $this,
            new ResourceRepository(),
            new ScheduleRepository(),
            new GroupRepository(),
            new AttributeService(new AttributeRepository()),
            new ReservationViewRepository(),
            new UserRepository(),
        );

        $this->Set('CreditsEnabled', Configuration::Instance()->GetSectionKey(ConfigSection::CREDITS, ConfigKeys::CREDITS_ENABLED, new BooleanConverter()));
        $page = basename($_SERVER['REQUEST_URI']);
        $this->Set('Endpoint', empty($page) || !BookedStringHelper::Contains($page, ".php") ? "manage_resources.php" : $page);

        $url = $this->server->GetUrl();
        $exportUrl = BookedStringHelper::Contains($url, '?') ? $url . '&dr=export' : $this->server->GetRequestUri() . '?dr=export';
        $this->Set('ExportUrl', $exportUrl);
        $this->Set('PageId', $this->GetPageId());
    }

    public function ProcessPageLoad()
    {
        $this->Display('Admin/Resources/manage-resources-spa.tpl');
    }

    public function ProcessApiCall($json)
    {
        $this->presenter->ProcessApi($json);
    }

    public function ProcessDataRequest($dataRequest)
    {
    }

    public function ProcessAction()
    {
        $this->presenter->ProcessAction();
    }

    public function GetResourceId()
    {
        $id = $this->GetQuerystring(QueryStringKeys::RESOURCE_ID);
        if (empty($id)) {
            $id = $this->GetForm(FormKeys::PK);
        }

        return $id;
    }

    public function ShowQRCode($qrCodeImageUrl, $resourceName)
    {
        $this->Set('QRImageUrl', $qrCodeImageUrl);
        $this->Set('ResourceName', $resourceName);
        $this->Set('HelpText', "Scan this code to quickly book this resource or check into an ongoing reservation.");

        $this->Display('Admin/Resources/show_resource_qr.tpl');
    }

    public function GetApiResourceId()
    {
        return $this->GetForm("id");
    }

    public function GetApiImageNames()
    {
        return $this->GetForm("images");
    }

    public function GetApiUploadedImages()
    {
        return $this->server->GetFiles("files");
    }

    public function GetPageId(): int
    {
        return AdminPageIds::Resources;
    }
}