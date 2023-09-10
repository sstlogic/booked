<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

define('ROOT_DIR', '../../');

require_once(ROOT_DIR . 'Pages/Admin/ResourceAdminManageReservationsPage.php');

class GroupAdminManageReservationsPage extends ManageReservationsPage
{
    public function __construct()
    {
        parent::__construct();

        $userRepository = new UserRepository();
        $this->presenter = new ManageReservationsPresenter($this,
            new GroupAdminManageReservationsService(new ReservationViewRepository(), $userRepository, new ReservationAuthorization(PluginManager::Instance()->LoadAuthorization())),
            new ScheduleRepository(),
            new ResourceRepository(),
            new AttributeService(new AttributeRepository()),
            $userRepository,
            new TermsOfServiceRepository());

        $this->SetCanUpdateResourceStatus(false);
    }
}

$page = new RoleRestrictedPageDecorator(new GroupAdminManageReservationsPage(), array(RoleLevel::GROUP_ADMIN));
$page->PageLoad();