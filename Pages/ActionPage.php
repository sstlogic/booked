<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/Page.php');

interface IActionPage extends IPage
{
    public function TakingAction();

    public function GetAction();

    public function RequestingData();

    public function GetDataRequest();

    public function SetJsonResponse($objectToSerialize, $error = null, $httpResponseCode = 200);

    /**
     * @return object|null
     */
    public function GetJsonPost();
}

abstract class ActionPage extends Page implements IActionPage
{
    public function __construct($titleKey, $pageDepth = 0)
    {
        parent::__construct($titleKey, $pageDepth);
    }

    public function PageLoad()
    {
        try {
            if ($this->TakingAction()) {
                $this->ProcessAction();
            }
            elseif ($this->RequestingData()) {
                $this->ProcessDataRequest($this->GetDataRequest());
            }
            elseif ($this->IsApiCall()) {
                $this->ProcessApiCall($this->GetJsonPost());
            }
            else {
                $this->ProcessPageLoad();
            }

        } catch (Exception $ex) {
            Log::Error('Error loading page', ['exception' => $ex]);
            throw $ex;
        }
    }

    /**
     * @return bool
     */
    public function TakingAction()
    {
        $action = $this->GetAction();
        return !empty($action);
    }

    /**
     * @return bool
     */
    public function RequestingData()
    {
        $dataRequest = $this->GetDataRequest();
        return !empty($dataRequest);
    }

    /**
     * @return null|string
     */
    public function GetAction()
    {
        return $this->GetQuerystring(QueryStringKeys::ACTION);
    }

    /**
     * @return string|null
     */
    public function GetApi()
    {
        return $this->GetQuerystring(QueryStringKeys::API);
    }

    /**
     * @return null|string
     */
    public function GetDataRequest()
    {
        return $this->GetQuerystring(QueryStringKeys::DATA_REQUEST);
    }

    /**
     * @return array|string|null
     */
    private function IsApiCall()
    {
        return $this->GetQuerystring(QueryStringKeys::API);
    }

    /**
     * @return bool
     */
    public function IsValid()
    {
        if (parent::IsValid()) {
            Log::Debug('Action passed all validations');
            return true;
        }

        $errors = new ActionErrors();
        $inlineErrors = array();

        foreach ($this->smarty->failedValidators as $id => $validator) {
            Log::Debug('Failed validator', ['validatorId' => $id]);
            $errors->Add($id, $validator->Messages());

            if ($validator->ReturnsErrorResponse()) {
                http_response_code(400);
                array_merge($validator->Messages(), $inlineErrors);
            }
        }

        if (!empty($inlineErrors)) {
            $this->SetJson(implode(',', $inlineErrors));
        }
        else {
            $this->SetJson($errors);
        }
        return false;
    }

    /**
     * @return void
     */
    public abstract function ProcessAction();

    /**
     * @param $dataRequest string
     * @return void
     */
    public abstract function ProcessDataRequest($dataRequest);

    /**
     * @return void
     */
    public abstract function ProcessPageLoad();

    /**
     * @param $json object|null
     */
    protected function ProcessApiCall($json)
    {
        // hook for pages
    }

    public function SetJsonResponse($objectToSerialize, $error = null, $httpResponseCode = 200)
    {
        parent::SetJson($objectToSerialize, $error, $httpResponseCode);
    }

    public function GetJsonPost()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contents = file_get_contents("php://input");
            if (empty($contents)) {
                return null;
            }
            return json_decode($contents);
        }

        return null;
    }
}

class ApiActionResult
{
    /**
     * @var bool
     */
    public $success = false;

    /**
     * @var null|array|object
     */
    public $data = null;

    /**
     * @var null|ApiErrorList
     */
    public $error = null;

    /**
     * @param bool $success
     * @param object|array|null $data
     * @param ApiErrorList|null $error
     */
    public function __construct(bool $success, $data = null, $error = null)
    {
        $this->success = $success;
        $this->data = $data;
        $this->error = $error;
    }
}

class ApiErrorList
{
    /**
     * @param string[] $errors
     */
    public function __construct($errors = [])
    {
        $this->errors = $errors;
    }

    /**
     * @var string[]
     */
    public $errors = [];
}

class ActionErrors
{
    public $ErrorIds = array();
    public $Messages = array();

    public function Add($id, $messages = array())
    {
        $this->ErrorIds[] = $id;
        $this->Messages[$id] = $messages;
    }
}