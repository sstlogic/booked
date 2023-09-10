<?php
/**
Copyright 2022-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'Pages/Admin/AdminPage.php');
require_once(ROOT_DIR . 'Presenters/Admin/Reservations/ManageReservationColorsPresenter.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Attributes/namespace.php');

interface IManageReservationColorsPage extends IActionPage
{
}


class ManageReservationColorsPage extends ActionPage implements IManageReservationColorsPage, IPageWithId
{
	private ManageReservationColorsPresenter $presenter;

	public function __construct()
	{
		parent::__construct('ReservationColors', 3);

        $this->Set('PageId', $this->GetPageId());
        $this->presenter = new ManageReservationColorsPresenter($this, new ReservationRepository(), new AttributeRepository());
	}

	public function ProcessAction()
	{
	}

	public function ProcessDataRequest($dataRequest)
	{
	}

	public function ProcessPageLoad()
	{
		$this->Display('Admin/Reservations/manage_reservation_colors-spa.tpl');
	}

    public function ProcessApiCall($json)
    {
       $this->presenter->ProcessApi($json);
    }

    public function GetPageId(): int
    {
        return AdminPageIds::ReservationColors;
    }
}