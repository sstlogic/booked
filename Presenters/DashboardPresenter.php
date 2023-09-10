<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'lib/Config/namespace.php');
require_once(ROOT_DIR . 'lib/Common/namespace.php');
require_once(ROOT_DIR . 'lib/Database/namespace.php');
require_once(ROOT_DIR . 'lib/Database/Commands/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Schedule/namespace.php');

require_once(ROOT_DIR . 'Controls/Dashboard/AnnouncementsControl.php');
require_once(ROOT_DIR . 'Controls/Dashboard/UpcomingReservations.php');
require_once(ROOT_DIR . 'Controls/Dashboard/ResourceAvailabilityControl.php');

class DashboardPresenter extends ActionPresenter
{
    private $_page;

    public function __construct(IDashboardPage $page)
    {
        $this->_page = $page;
        parent::__construct($page);

        $this->AddAction('addFavorite', 'AddFavoriteResource');
        $this->AddAction('removeFavorite', 'RemoveFavoriteResource');
    }

    public function Initialize()
    {
        $announcement = new AnnouncementsControl(new SmartyPage());
        $upcomingReservations = new UpcomingReservations(new SmartyPage());
        $availability = new ResourceAvailabilityControl(new SmartyPage());

        $this->_page->AddItem($announcement);
        $this->_page->AddItem($upcomingReservations);
        $this->_page->AddItem($availability);

        if (ServiceLocator::GetServer()->GetUserSession()->IsAdmin) {
            $allUpcomingReservations = new AllUpcomingReservations(new SmartyPage());
            $this->_page->AddItem($allUpcomingReservations);
        }
    }

    public function AddFavoriteResource()
    {
        $user = ServiceLocator::GetServer()->GetUserSession();
        $userId = $user->UserId;
        $resourceId = $this->_page->GetResourceId();

        Log::Debug("Adding favorite resource.", ['userId' => $userId, 'resourceId' => $resourceId]);

        $userRepository = new UserRepository();
        $userRepository->AddFavoriteResource($userId, $resourceId);
        $availability = new ResourceAvailabilityControl(new SmartyPage());
        $availability->BindAvailability();
    }

    public function RemoveFavoriteResource()
    {
        $userId = ServiceLocator::GetServer()->GetUserSession()->UserId;
        $resourceId = $this->_page->GetResourceId();

        Log::Debug("Removing favorite resource.", ['userId' => $userId, 'resourceId' => $resourceId]);
        $userRepository = new UserRepository();
        $userRepository->DeleteFavoriteResource($userId, $resourceId);
        $availability = new ResourceAvailabilityControl(new SmartyPage());
        $availability->BindAvailability();
    }
}