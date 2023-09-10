<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/ActionPage.php');

abstract class ActionPresenter
{
	/**
	 * @var IActionPage
	 */
	private $actionPage;

	/**
	 * @var array
	 */
	private $actions;

	/**
	 * @var array
	 */
	private $validations;

	/**
	 * @var array
	 */
	private $apis;

	protected function __construct(IActionPage $page)
	{
		$this->actionPage = $page;
		$this->actions = array();
		$this->apis = array();
		$this->validations = array();
	}

	/**
	 * @param string $actionName
	 * @param string $actionMethod
	 * @return void
	 */
	protected function AddAction($actionName, $actionMethod)
	{
		$this->actions[$actionName] = $actionMethod;
	}

	protected function AddApi($api, $actionMethod)
	{
		$this->apis[$api] = $actionMethod;
	}

	protected function AddValidation($actionName, $validationMethod)
	{
		$this->validations[$actionName] = $validationMethod;
	}

	protected function ActionIsKnown($action)
	{
		return isset($this->actions[$action]);
	}

	protected function ApiIsKnown($api)
	{
		return isset($this->apis[$api]);
	}

	protected function LoadValidators($action)
	{
		// Hook for children to load validators
	}

	public function ProcessAction()
	{
		/** @var $action string */
		$action = $this->actionPage->GetAction();

		if ($this->ActionIsKnown($action))
		{
            $server = ServiceLocator::GetServer();
            $token = $server->GetHeader('HTTP_X_CSRF_TOKEN');
            if (!empty($token)) {
                $this->EnforceApiCSRFCheck();
            }
            else {
                $this->actionPage->EnforceCSRFCheck();
            }

			$method = $this->actions[$action];
			try
			{
				$this->LoadValidators($action);

				if ($this->actionPage->IsValid())
				{
					Log::Debug("Processing page action", ['action' => $action]);
					$this->$method();
				}
			} catch (Exception $ex)
			{
				Log::Error("ProcessAction Error", ['action' => $action, 'exception' => $ex]);
			}
		}
		else
		{
			Log::Error("Unknown action", ['action' => $action]);
		}
	}

	public function ProcessApi(?object $json)
	{
		/** @var $api string */
		$api = $this->actionPage->GetApi();

		if ($this->ApiIsKnown($api))
		{
			$this->EnforceApiCSRFCheck();

			$method = $this->apis[$api];
			try
			{
				Log::Debug("Processing page api.", ['api' => $api]);
				/** @var ApiActionResult $apiResult */
				$apiResult = $this->$method($json);

				if ($apiResult->success && !empty($apiResult->data))
				{
					$this->actionPage->SetJsonResponse(['data' => $apiResult->data]);
					return;
				}

				if ($apiResult->success)
				{
					$this->actionPage->SetJsonResponse(null);
					return;
				}

				$this->actionPage->SetJsonResponse(null, $apiResult->error->errors, 400);
			} catch (Exception $ex)
			{
				Log::Error("ProcessApi Error", ['api' => $api, 'exception' => $ex]);
				$this->actionPage->SetJsonResponse(null, "Unknown error", 400);
			}
		}
		else
		{
			Log::Error("Unknown api", ['api' => $api]);
			$this->actionPage->SetJsonResponse(null, "Unknown api", 404);
		}
	}

	private function EnforceApiCSRFCheck()
	{
		$server = ServiceLocator::GetServer();
		$token = $server->GetHeader('HTTP_X_CSRF_TOKEN');

		$session = $server->GetUserSession();
		if (!$session->IsLoggedIn())
		{
			return;
		}
		if ((empty($token) || $token != $session->CSRFToken))
		{
			Log::Error('Possible CSRF in API attack.', ['submittedToken' => $token, 'expectedToken' => $session->CSRFToken]);
			$this->actionPage->SetJsonResponse(null, "Unauthorized", 401);
			die('');
		}
	}
}