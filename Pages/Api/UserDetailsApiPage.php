<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/Page.php');
require_once(ROOT_DIR . 'Pages/ActionPage.php');
require_once(ROOT_DIR . 'Pages/Ajax/IUserDetailsRequestPage.php');
require_once(ROOT_DIR . 'Presenters/Api/UserDetailsApiPresenter.php');

interface IUserDetailsApiPage extends IUserDetailsRequestPage, IActionPage{}

class UserDetailsApiPage extends ActionPage implements IUserDetailsApiPage {

    /**
     * @var UserDetailsApiPresenter
     */
    private $presenter;

    public function __construct()
    {
        parent::__construct('', 1);
        $this->presenter = new UserDetailsApiPresenter($this, new PrivacyFilter(), new UserRepository(), new AttributeService(new AttributeRepository()));
    }

    public function GetUserId()
    {
        return $this->GetQuerystring(QueryStringKeys::USER_ID);
    }

    protected function ProcessApiCall($json)
    {
        if ($this->AllowAccess()) {
            $this->presenter->ProcessApi($json);
        }
        else {
            $this->SetJsonResponse(['Unauthorized' => true], 'Unauthorized', 401);
        }
    }

    /**
     * @return bool
     */
    protected function AllowAccess(): bool
    {
        $user = $this->server->GetUserSession();

        if ($user->IsAdmin || $user->IsScheduleAdmin || $user->IsResourceAdmin || $user->IsGroupAdmin) {
            return true;
        }


        $hideUsers = Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_HIDE_USER_DETAILS, new BooleanConverter());
        $viewReservation = Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_VIEW_RESERVATIONS, new BooleanConverter());

        return !$hideUsers && ($user->IsLoggedIn() || $viewReservation);
    }

    public function ProcessAction()
    {
        // no-op
    }

    public function ProcessDataRequest($dataRequest)
    {
        // no-op
    }

    public function ProcessPageLoad()
    {
        // no-op
    }
}