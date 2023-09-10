<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/ActionPage.php');

class ClientErrorApiPage extends ActionPage
{
    public function __construct()
    {
        parent::__construct('', 1);
    }

    public function ProcessAction()
    {

    }

    public function ProcessDataRequest($dataRequest)
    {
    }

    public function ProcessPageLoad()
    {
    }

    protected function ProcessApiCall($json)
    {
        $user = $this->server->GetUserSession();

        if ($user->IsLoggedIn()) {
          Log::Error('Client error.', ['error' => $json]);
        } else {
            $this->SetJsonResponse(['Unauthorized' => true], 'Unauthorized', 401);
        }
    }
}