<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'Presenters/ApiDtos/namespace.php');
require_once(ROOT_DIR . 'Presenters/Api/AutoCompleteType.php' );
require_once(ROOT_DIR . 'Pages/Api/UserAutocompleteApiPage.php');

class UserAutocompleteApiPresenter extends ActionPresenter
{
	/**
	 * @var IUserAutocompleteApiPage
	 */
	private $page;
	/**
	 * @var IUserRepository
	 */
	private $userRepository;

	public function __construct(IUserAutocompleteApiPage $page, IUserRepository $userRepository)
	{
		$this->page = $page;

		parent::__construct($page);

		$this->AddApi('users', 'GetUsers');
		$this->userRepository = $userRepository;
	}

	public function GetUsers(): ApiActionResult
	{
	    if (!$this->Allowed()) {
	        return new ApiActionResult(false, [], new ApiErrorList(["Not allowed"]));
        }
		$term = $this->page->GetTerm();
		$onlyActive = !$this->page->GetIncludeInactive();
        $apiOnlyFilter = new SqlFilterEquals(ColumnNames::API_ONLY, 0);
        if (!empty($term)) {
            $filter = new SqlFilterLike(ColumnNames::FIRST_NAME, $term);
            $filter->_Or(new SqlFilterLike(ColumnNames::LAST_NAME, $term));
            $filter->_Or(new SqlFilterLike(ColumnNames::EMAIL, $term));
            $filter->_Or(new SqlFilterLike(ColumnNames::USERNAME, $term));
            $apiOnlyFilter->_And($filter);
        }

		$currentUser = ServiceLocator::GetServer()->GetUserSession();
		$user = $this->userRepository->LoadById($currentUser->UserId);

		$status = AccountStatus::ACTIVE;
		if (!$onlyActive && ($currentUser->IsAdmin || $currentUser->IsGroupAdmin))
		{
			$status = AccountStatus::ALL;
		}
		$results = $this->userRepository->GetList(1, PageInfo::All, null, null, $apiOnlyFilter, $status)->Results();

		$hideUserDetails = Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_HIDE_USER_DETAILS, new BooleanConverter());

		$users = array();
		/** @var $result UserItemView */
		foreach ($results as $result)
		{
			$isUserAdmin = $result->Id == $currentUser->UserId || $user->IsGroupAdminFor($result->GroupIds) || $currentUser->IsAdmin;
			if (!$hideUserDetails || $isUserAdmin)
			{
				$users[] = UserApiDto::FromUserItemView($result, $this->IsEmailHidden());
			}
		}

		return new ApiActionResult(true, $users);
	}

	/**
	 * @return bool
	 */
	private function IsEmailHidden(): bool
	{
        $hideUser = Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_HIDE_USER_DETAILS, new BooleanConverter());
		return !ServiceLocator::GetServer()->GetUserSession()->IsAdmin && $hideUser;
	}

    private function Allowed()
    {
        $allowAnonSearch = ($this->page->GetType() != AutoCompleteType::Organization) ||
            (Configuration::Instance()->GetSectionKey(ConfigSection::TABLET_VIEW, ConfigKeys::TABLET_VIEW_AUTOCOMPLETE, new BooleanConverter()) ||
            !Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_HIDE_USER_DETAILS, new BooleanConverter()));

        return ServiceLocator::GetServer()->GetUserSession()->IsAdmin || $allowAnonSearch;
    }
}