<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/WebService/namespace.php');

class ReservationAccessoryResponse extends RestResponse
{
	public $id;
	public $name;
	public $quantityAvailable;
	public $quantityReserved;

	public function __construct(IRestServer $server, $id, $name, $quantityReserved, $quantityAvailable)
	{
		$this->id = $id;
		$this->name = apidecode($name);
		$this->quantityReserved = $quantityReserved;
		$this->quantityAvailable = $quantityAvailable;

		$this->AddService($server, WebServices::GetAccessory, array(WebServiceParams::AccessoryId => $id));
	}

	public static function Example()
	{
		return new ExampleReservationAccessoryResponse();
	}
}

class ExampleReservationAccessoryResponse extends ReservationAccessoryResponse
{
	public function __construct()
	{
		$this->id = 1;
		$this->name = 'Example';
		$this->quantityAvailable = 12;
		$this->quantityReserved = 3;
	}
}

