<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once ROOT_DIR . 'Pages/Ajax/IReservationSaveResultsView.php';

class ReservationApiSaveResultCollector implements IReservationSaveResultsView
{
    /**
     * @param ReservationRetryParameterApiDto[] $retryParameters
     */
    public function __construct($retryParameters = [])
    {
        $this->retryParameters = [];

        if (!empty($retryParameters)) {
            foreach ($retryParameters as $p) {
                $this->retryParameters[] = new ReservationRetryParameter($p->name, $p->value);
            }
        }
    }

    /**
     * @var bool
     */
    private $succeeded = false;
    /**
     * @var string[]
     */
    private $errors = [];
    /**
     * @var string[]
     */
    private $warnings = [];
    /**
     * @var string[]
     */
    private $retryMessages = [];
    /**
     * @var bool
     */
    private $canBeRetried = false;
    /**
     * @var ReservationRetryParameter[]
     */
    private $retryParameters = [];
    /**
     * @var bool
     */
    private $canJoinWaitlist = false;

    public function SetSaveSuccessfulMessage($succeeded)
    {
        $this->succeeded = $succeeded;
    }

    public function SetErrors($errors)
    {
        $this->errors = $errors;
    }

    public function SetWarnings($warnings)
    {
        $this->warnings = $warnings;
    }

    public function SetRetryMessages($messages)
    {
        $this->retryMessages = $messages;
    }

    public function SetCanBeRetried($canBeRetried)
    {
        $this->canBeRetried = $canBeRetried;
    }

    public function SetRetryParameters($retryParameters)
    {
        $this->retryParameters = $retryParameters;
    }


    public function SetCanJoinWaitList($canJoinWaitlist)
    {
        $this->canJoinWaitlist = $canJoinWaitlist;
    }

    /**
     * @return ReservationRetryParameter[]
     */
    public function GetRetryParameters()
    {
        return $this->retryParameters;
    }

    /**
     * @return bool
     */
    public function GetWasSuccessful()
    {
        return $this->succeeded;
    }

    /**
     * @return bool
     */
    public function GetCanBeRetried()
    {
        return $this->canBeRetried;
    }

    /**
     * @return bool
     */
    public function GetCanJoinWaitlist()
    {
        return $this->canJoinWaitlist;
    }

    /**
     * @return string[]
     */
    public function GetErrors()
    {
        return $this->errors;
    }

    /**
     * @return string[]
     */
    public function GetWarnings()
    {
        return $this->warnings;
    }

    /**
     * @return string[]
     */
    public function GetRetryMessages()
    {
        return $this->retryMessages;
    }
}