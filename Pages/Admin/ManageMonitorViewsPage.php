<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/Admin/AdminPage.php');
require_once(ROOT_DIR . 'Presenters/Admin/ManageGroupsPresenter.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Attributes/namespace.php');

interface IManageMonitorViewsPage extends IActionPage
{
}

class ManageMonitorViewsPage extends ActionPage implements IManageMonitorViewsPage
{
    /**
     * @var ManageMonitorViewsPresenter
     */
    private $presenter;

    public function __construct($pageDepth = 2)
    {
        parent::__construct("ManageMonitorViews", $pageDepth);
        $this->presenter = new ManageMonitorViewsPresenter($this, new ScheduleRepository(), new ResourceRepository(), new MonitorViewRepository(), new AttributeService(new AttributeRepository()));
    }

    public function ProcessApiCall($json)
    {
        $this->presenter->ProcessApi($json);
    }

    public function ProcessAction()
    {
    }

    public function ProcessDataRequest($dataRequest)
    {
    }

    public function ProcessPageLoad()
    {
        $this->Display("Admin/MonitorViews/manage-monitor-views-spa.tpl");
    }
}
