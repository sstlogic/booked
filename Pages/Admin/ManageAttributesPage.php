<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/Admin/AdminPage.php');
require_once(ROOT_DIR . 'Presenters/Admin/ManageAttributesPresenter.php');

interface IManageAttributesPage extends IActionPage
{
}

class ManageAttributesPage extends ActionPage implements IManageAttributesPage, IPageWithId
{
	private ManageAttributesPresenter $presenter;

	public function __construct()
	{
		parent::__construct('CustomAttributes', 2);
		$this->presenter = new ManageAttributesPresenter($this, new AttributeRepository(), new UserRepository(), new ResourceRepository());
        $this->Set('PageId', $this->GetPageId());
    }

	public function ProcessPageLoad()
	{
        $this->Display('Admin/Attributes/manage-attributes-spa.tpl');
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

    public function GetPageId(): int
    {
        return AdminPageIds::Attributes;
    }
}