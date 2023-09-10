<?php
/**
 * Copyright 2013-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/WebService/JsonRequest.php');
require_once(ROOT_DIR . 'WebServices/Requests/CustomAttributes/AttributeValueRequest.php');

class ResourceRequest extends JsonRequest
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $location;
    /**
     * @var string
     */
    public $contact;
    /**
     * @var string
     */
    public $notes;
    /**
     * @var string
     */
    public $minLength;
    /**
     * @var string
     */
    public $maxLength;
    /**
     * @var bool
     */
    public $requiresApproval;
    /**
     * @var bool
     */
    public $allowMultiday;
    /**
     * @var int|null
     */
    public $maxParticipants;
    /**
     * @var int|null
     */
    public $minParticipants;
    /**
     * @var string
     */
    public $minNotice;
    /**
     * @var string
     */
    public $minNoticeUpdate;
    /**
     * @var string
     */
    public $minNoticeDelete;
    /**
     * @var string
     */
    public $maxNotice;
    /**
     * @var string
     */
    public $description;
    /**
     * @var int
     */
    public $scheduleId;
    /**
     * @var bool
     */
    public $autoAssignPermissions;
    /**
     * @var array|AttributeValueRequest[]
     */
    public $customAttributes = array();
    /**
     * @var int
     */
    public $sortOrder;
    /**
     * @var int
     */
    public $statusId;
    /**
     * @var int|null
     */
    public $statusReasonId;
    /**
     * @var int|null
     */
    public $autoReleaseMinutes;
    /**
     * @var bool|null
     */
    public $extendIfMissedCheckout;
    /**
     * @var bool|null
     */
    public $checkinLimitedToAdmins;
    /**
     * @var int|ResourceAutoReleaseAction|null
     */
    public $autoReleaseAction;
    /**
     * @var bool|null
     */
    public $requiresCheckIn;
    /**
     * @var string|null
     */
    public $color;
    /**
     * @var float|null
     */
    public $credits;
    /**
     * @var float|null
     */
    public $peakCredits;
	/**
	 * @var int|null
	 */
    public $creditApplicability;
    /**
	 * @var bool|null
	 */
    public $creditsChargedAllSlots;
	/**
	 * @var int|null
	 */
	public $maxConcurrentReservations;
    /**
     * @var int|null
     */
    public $adminGroupId;
    /**
     * @var int|null
     */
    public $typeId;
    /**
     * @var string|null
     */
    public $slotLabel;
    /**
     * @var string|null
     */
    public $bufferTime;
    /**
     * @return ExampleResourceRequest
     */
    public static function Example()
    {
        return new ExampleResourceRequest();
    }

    /**
     * @return array|AttributeValueRequest[]
     */
    public function GetCustomAttributes()
    {
        if (!empty($this->customAttributes)) {
            return $this->customAttributes;
        }
        return array();
    }
}

class ExampleResourceRequest extends ResourceRequest
{
    public function __construct()
    {
        $this->name = 'resource name';
        $this->location = 'location';
        $this->contact = 'contact information';
        $this->notes = 'notes';
        $this->minLength = '1d0h0m';
        $this->maxLength = '3600';
        $this->requiresApproval = true;
        $this->allowMultiday = true;
        $this->maxParticipants = 100;
        $this->minParticipants = 10;
        $this->minNotice = '0d12h30m';
        $this->minNoticeUpdate = '0d12h30m';
        $this->minNoticeDelete = '0d12h30m';
        $this->maxNotice = '0d12h30m';
        $this->description = 'description';
        $this->scheduleId = 10;
        $this->autoAssignPermissions = true;
        $this->customAttributes = array(AttributeValueRequest::Example());
        $this->sortOrder = 1;
        $this->statusId = ResourceStatus::AVAILABLE;
        $this->statusReasonId = 2;
        $this->autoReleaseMinutes = 15;
        $this->requiresCheckIn = true;
        $this->color = '#ffffff';
        $this->credits = 3;
        $this->peakCredits = 6;
        $this->creditApplicability = CreditApplicability::SLOT;
        $this->maxConcurrentReservations = 1;
        $this->creditsChargedAllSlots = 1;
        $this->adminGroupId = 1;
        $this->typeId = 1;
        $this->slotLabel = null;
        $this->bufferTime = '0d12h30m';
    }
}