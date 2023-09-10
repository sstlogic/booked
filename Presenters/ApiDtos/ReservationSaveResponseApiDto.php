<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/ApiDtos/ReservationApiDto.php');

class ReservationSaveResponseApiDto {
    /**
     * @var string
     */
    public $referenceNumber;
    /**
     * @var boolean
     */
    public $success;
    /**
     * @var boolean
     */
    public $canBeRetried;
    /**
     * @var boolean
     */
    public $canJoinWaitlist;
    /**
     * @var string[]
     */
    public $errors = [];
    /**
     * @var string[]
     */
    public $warnings = [];
    /**
     * @var string[]
     */
    public $retryMessages;
    /**
     * @var ReservationRetryParameterApiDto[]
     */
    public $retryParameters;
    /**
     * @var boolean
     */
    public $requiresApproval;
    /**
     * @var boolean
     */
    public $showDetails;
    /**
     * @var string[]
     */
    public $dates = [];
}
