<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/Api/UserDetailsApiPage.php');
require_once(ROOT_DIR . 'Presenters/ApiDtos/UserDetailsApiDto.php');
require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Attributes/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/namespace.php');

class UserDetailsApiPresenter extends ActionPresenter
{
	/**
	 * @var IUserDetailsRequestPage
	 */
	private $page;
	/**
	 * @var IPrivacyFilter
	 */
	private $privacyFilter;
	/**
	 * @var IUserRepository
	 */
	private $userRepository;
	/**
	 * @var IAttributeService
	 */
	private $attributeService;

	public function __construct(IUserDetailsApiPage $page, IPrivacyFilter $privacyFilter, IUserRepository $userRepository, IAttributeService $attributeService)
	{
        parent::__construct($page);

		$this->page = $page;
		$this->privacyFilter = $privacyFilter;
		$this->userRepository = $userRepository;
		$this->attributeService = $attributeService;

        $this->AddApi('load', 'GetUserDetails');
	}

	public function GetUserDetails(): ApiActionResult
	{
        $currentUser = ServiceLocator::GetServer()->GetUserSession();
		$user = $this->userRepository->LoadById($this->page->GetUserId());
		$showReservationDetails = !$currentUser->IsLoggedIn() && Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_VIEW_RESERVATIONS, new BooleanConverter());
		$showUserName = Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_ALWAYS_SHOW_USER_NAME, new BooleanConverter());

		if ($showReservationDetails || $this->privacyFilter->CanViewUser($currentUser, null, $user->Id()))
		{
            $attributes = $this->attributeService->GetAttributes(CustomAttributeCategory::USER, $currentUser, $user->Id());
            return new ApiActionResult(true, UserDetailsApiDto::FromUser($user, $attributes));
		}
		else
		{
			return new ApiActionResult(false, null, new ApiErrorList(['Unauthorized']));
		}
	}
}