<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/ActionPage.php');
require_once(ROOT_DIR . 'Pages/SecurePage.php');
require_once(ROOT_DIR . 'Presenters/Api/ReportsApiPresenter.php');

interface IReportsApiPage extends IActionPage
{
    /**
     * @return int
     */
    public function GetId();
}

class ReportsApiPage extends ActionPage implements IReportsApiPage
{
    /**
     * @var ReportsApiPresenter
     */
    private $presenter;

    public function __construct()
    {
        parent::__construct("", 1);
        $this->presenter = new ReportsApiPresenter(
            $this,
            $this->server->GetUserSession(),
            new ReportingRepository(),
            new UserRepository(),
        );
    }

    public function ProcessAction()
    {
        if ($this->AllowAccess()) {
            $this->presenter->ProcessAction();
        } else {
            $this->SetJsonResponse(['Unauthorized' => true], 'Unauthorized', 401);
        }
    }

    public function ProcessDataRequest($dataRequest)
    {
        // no-op
    }

    public function ProcessPageLoad()
    {
        // no-op
    }

    protected function ProcessApiCall($json)
    {
        if ($this->AllowAccess()) {
            $this->presenter->ProcessApi($json);
        } else {
            $this->SetJsonResponse(['Unauthorized' => true], 'Unauthorized', 401);
        }
    }

    /**
     * @return bool
     */
    protected function AllowAccess(): bool
    {
        $roles = [RoleLevel::APPLICATION_ADMIN, RoleLevel::GROUP_ADMIN, RoleLevel::RESOURCE_ADMIN, RoleLevel::SCHEDULE_ADMIN];
        if (Configuration::Instance()->GetSectionKey(ConfigSection::REPORTS, ConfigKeys::REPORTS_ALLOW_ALL, new BooleanConverter()))
        {
            return true;
        }

        if (Configuration::Instance()->GetSectionKey(ConfigSection::REPORTS, ConfigKeys::REPORTS_RESTRICT_TO_ADMINS, new BooleanConverter())) {
            $roles = [RoleLevel::APPLICATION_ADMIN];
        }

        return $this->presenter->IsInRole($roles);
    }

    public function GetId()
    {
        return $this->GetQuerystring('id');
    }
}
