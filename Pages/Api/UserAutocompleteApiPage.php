<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/ActionPage.php');
require_once(ROOT_DIR . 'Presenters/Api/UserAutocompleteApiPresenter.php');

interface IUserAutocompleteApiPage extends IActionPage
{
    /**
     * @return string|null
     */
    public function GetTerm();

    /**
     * @return string
     */
    public function GetType();

    public function GetIncludeInactive(): bool;
}

class UserAutocompleteApiPage extends ActionPage implements IUserAutocompleteApiPage
{
    /**
     * @var UserAutocompleteApiPresenter
     */
    private $presenter;

    public function __construct()
    {
        parent::__construct("", 1);
        $this->presenter = new UserAutocompleteApiPresenter($this, new UserRepository());
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
        $user = $this->server->GetUserSession();

        if ($user->IsAdmin || $user->IsScheduleAdmin || $user->IsResourceAdmin || $user->IsGroupAdmin) {
            return true;
        }

        $hideUsers = Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_HIDE_USER_DETAILS, new BooleanConverter());
        $viewSchedules = Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_VIEW_SCHEDULES, new BooleanConverter());

        return !$hideUsers && ($user->IsLoggedIn() || $viewSchedules);
    }

    public function GetTerm()
    {
        return $this->GetQuerystring(QueryStringKeys::AUTOCOMPLETE_TERM);
    }

    public function GetType()
    {
        return $this->GetQuerystring(QueryStringKeys::AUTOCOMPLETE_TYPE);
    }

    public function GetIncludeInactive(): bool
    {
        $include = $this->GetQuerystring(QueryStringKeys::INCLUDE_INACTIVE);
        return BooleanConverter::ConvertValue($include);
    }
}

