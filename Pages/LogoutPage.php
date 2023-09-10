<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'Pages/LoginPage.php');
require_once(ROOT_DIR . 'Presenters/LoginPresenter.php');
require_once(ROOT_DIR . 'lib/Application/Authentication/namespace.php');

class LogoutPage extends LoginPage
{
	public function __construct()
	{
		parent::__construct();
	}

	public function PageLoad()
	{
		$this->presenter->Logout();
	}

	public function GetResumeUrl()
	{
		return self::CleanRedirect($this->GetQuerystring(QueryStringKeys::REDIRECT));
	}
}
