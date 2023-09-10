<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/SecurePage.php');
require_once(ROOT_DIR . 'Pages/ActionPage.php');
require_once(ROOT_DIR . 'Presenters/WaitlistPresenter.php');

class WaitlistPage extends ActionPage
{
    /**
     * @var WaitlistPresenter
     */
    private $presenter;

    public function __construct()
    {
        parent::__construct('Waitlist');
        $this->presenter = new WaitlistPresenter($this, new ResourceRepository(), new ReservationWaitlistRepository());
    }

    public function ProcessAction()
    {
        $this->presenter->ProcessAction();
    }

    public function ProcessDataRequest($dataRequest)
    {
        // noop
    }

    public function ProcessPageLoad()
    {
        $this->presenter->PageLoad();
        $this->Display('MyAccount/my-waitlist.tpl');
    }

    /**
     * @param ReservationWaitlistRequest[] $requests
     */
    public function BindWaitlistRequests($requests)
    {
        $this->Set('WaitlistRequests', $requests);
    }

    /**
     * @param string[] $resourceNames
     */
    public function BindResourceNames($resourceNames)
    {
        $this->Set('ResourceNames', $resourceNames);
    }

    public function GetDeleteId()
    {
        return $this->GetForm(FormKeys::WAITLIST_REQUEST_ID);
    }
}