<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/ActionPage.php');
require_once(ROOT_DIR . 'Pages/SecurePage.php');
require_once(ROOT_DIR . 'Presenters/DashboardPresenter.php');

interface IDashboardPage extends IActionPage
{
    public function AddItem(DashboardItem $item);

    /**
     * @return int
     */
    public function GetResourceId();
}

class DashboardPage extends ActionPage implements IDashboardPage
{
    private $items = array();
    /**
     * @var DashboardPresenter
     */
    private $_presenter;

    public function __construct()
    {
        parent::__construct('MyDashboard');
        $this->_presenter = new DashboardPresenter($this);
    }

    public function AddItem(DashboardItem $item)
    {
        $this->items[] = $item;
    }

    public function ProcessAction()
    {
        $this->_presenter->ProcessAction();
    }

    public function ProcessDataRequest($dataRequest)
    {
        // TODO: Implement ProcessDataRequest() method.
    }

    public function ProcessPageLoad()
    {
        $this->_presenter->Initialize();

        $this->Set('items', $this->items);
        $this->Display('dashboard.tpl');
    }

    public function GetResourceId()
    {
        return $this->GetForm(FormKeys::RESOURCE_ID);
    }
}

