<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/Admin/AdminPage.php');
require_once(ROOT_DIR . 'Presenters/Admin/ManageQuotasPresenter.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');

interface IManageQuotasPage extends IActionPage
{
}

class ManageQuotasPage extends ActionPage implements IManageQuotasPage, IPageWithId
{
    private ManageQuotasPresenter $presenter;

    public function __construct()
    {
        parent::__construct('ManageQuotas', 2);
        $this->presenter = new ManageQuotasPresenter(
            $this,
            new ResourceRepository(),
            new GroupRepository(),
            new ScheduleRepository(),
            new QuotaRepository());

        $this->Set('PageId', $this->GetPageId());
    }

    public function ProcessPageLoad()
    {
        $this->Display('Admin/Quotas/manage-quotas-spa.tpl');
    }

    public function ProcessApiCall($json)
    {
        $this->presenter->ProcessApi($json);
    }

    public function ProcessAction()
    {
        // no-op
    }

    public function ProcessDataRequest($dataRequest)
    {
        // no-op
    }

    public function GetPageId(): int
    {
        return AdminPageIds::Quotas;
    }
}