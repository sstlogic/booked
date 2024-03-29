<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'Pages/Page.php');

class ErrorPage extends Page
{
	public function __construct()
	{
		parent::__construct('Error');
	}

	public function PageLoad()
	{
		$returnUrl = self::CleanRedirect($this->server->GetQuerystring(QueryStringKeys::REDIRECT));

		if (empty($returnUrl))
		{
			$returnUrl = "index.php";
		}

		$errorMessageKey = ErrorMessages::Instance()->GetResourceKey($this->server->GetQuerystring(QueryStringKeys::MESSAGE_ID));

		//TODO: Log

		$this->Set('ReturnUrl', urldecode($returnUrl));
		$this->Set('ErrorMessage', $errorMessageKey);
		$this->Display('error.tpl');
	}
}