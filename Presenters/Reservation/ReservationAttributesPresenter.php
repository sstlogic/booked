<?php
/**
 * Copyright 2014-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/Ajax/ReservationAttributesPage.php');

require_once(ROOT_DIR . 'lib/Application/Attributes/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Authorization/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/namespace.php');

class ReservationAttributesPresenter
{
	/**
	 * @var IReservationAttributesPage
	 */
	private $page;

	/**
	 * @var IAttributeService
	 */
	private $attributeService;

	/**
	 * @var IPrivacyFilter
	 */
	private $privacyFilter;

	/**
	 * @var IReservationViewRepository
	 */
	private $reservationViewRepository;

	public function __construct(IReservationAttributesPage $page,
								IAttributeService $attributeService,
								IPrivacyFilter $privacyFilter,
								IReservationViewRepository $reservationViewRepository)
	{
		$this->page = $page;
		$this->attributeService = $attributeService;
		$this->privacyFilter = $privacyFilter;
		$this->reservationViewRepository = $reservationViewRepository;
	}

	public function PageLoad(UserSession $userSession)
	{
		$hideReservations = !Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_VIEW_RESERVATIONS, new BooleanConverter());
		$hideDetails = Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_HIDE_RESERVATION_DETAILS, new BooleanConverter());
		if (($hideReservations || $hideDetails) && !$userSession->IsLoggedIn())
		{
			return;
		}
		$requestedUserId = $this->page->GetRequestedUserId();
		$requestedReferenceNumber = $this->page->GetRequestedReferenceNumber();
		$resourceIds = $this->page->GetRequestedResourceIds();

		$reservationView = new ReservationView();
		$canViewDetails = true;

		if (!empty($requestedReferenceNumber))
		{
			$reservationView = $this->reservationViewRepository->GetReservationForEditing($requestedReferenceNumber);
			$canViewDetails = $this->privacyFilter->CanViewDetails($userSession, $reservationView, $requestedUserId);
		}

		$attributes = array();

		if ($canViewDetails)
		{
			$attributes = $this->attributeService->GetReservationAttributes($userSession, $reservationView, $requestedUserId, $resourceIds);
		}

		$this->page->SetAttributes($attributes);
	}
}
