<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/SecurePage.php');
require_once(ROOT_DIR . 'Pages/Ajax/IUserDetailsRequestPage.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Attributes/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/namespace.php');

interface IUserDetailsPopupPage extends IUserDetailsRequestPage
{
    /**
     * @param bool $canView
     */
    public function SetCanViewUser($canView);

    /**
     * @param bool $canView
     */
    public function SetCanViewUserName($canView);

    /**
     * @param CustomAttribute[] $attributes
     */
    public function BindAttributes($attributes);

    /**
     * @param User $user
     */
    public function BindUser($user);
}

class UserDetailsPopupPage extends Page implements IUserDetailsPopupPage
{
    /**
     * @var UserDetailsPopupPresenter
     */
    private $presenter;

    public function __construct()
    {
        parent::__construct('', 1);
        $this->presenter = new UserDetailsPopupPresenter($this, new PrivacyFilter(), new UserRepository(), new AttributeService(new AttributeRepository()));
    }

    public function PageLoad()

    {
        $this->presenter->PageLoad(ServiceLocator::GetServer()->GetUserSession());
        $this->Display('Ajax/user_details.tpl');
    }

    public function SetCanViewUser($canView)
    {
        $this->Set('CanViewUser', $canView);
    }

    /**
     * @return string
     */
    public function GetUserId()
    {
        return $this->GetQuerystring(QueryStringKeys::USER_ID);
    }

    /**
     * @param CustomAttribute[] $attributes
     */
    public function BindAttributes($attributes)
    {
        $this->Set('Attributes', $attributes);
    }

    /**
     * @param User $user
     */
    public function BindUser($user)
    {
        $this->Set('User', $user);
    }

    public function SetCanViewUserName($canView)
    {
        $this->Set('CanViewUserName', $canView);
    }
}

class UserDetailsPopupPresenter
{
    /**
     * @var IUserDetailsPopupPage
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

    public function __construct(IUserDetailsPopupPage $page, IPrivacyFilter $privacyFilter, IUserRepository $userRepository, IAttributeService $attributeService)
    {
        $this->page = $page;
        $this->privacyFilter = $privacyFilter;
        $this->userRepository = $userRepository;
        $this->attributeService = $attributeService;
    }

    /**
     * @param $currentUser UserSession
     */
    public function PageLoad($currentUser)
    {
        $user = $this->userRepository->LoadById($this->page->GetUserId());
        $showReservationDetails = !$currentUser->IsLoggedIn() && Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_VIEW_RESERVATIONS, new BooleanConverter());
        $showUserName = Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_ALWAYS_SHOW_USER_NAME, new BooleanConverter());
        $this->page->SetCanViewUserName($showUserName);
        $this->page->BindUser($user);

        if ($showReservationDetails || $this->privacyFilter->CanViewUser($currentUser, null, $user->Id())) {
            $this->page->SetCanViewUser(true);
            $attributes = $this->attributeService->GetAttributes(CustomAttributeCategory::USER, $currentUser, $user->Id());
            $this->page->BindAttributes($attributes->GetAttributes($user->Id()));
        } else {
            $this->page->SetCanViewUser(false);
        }
    }
}