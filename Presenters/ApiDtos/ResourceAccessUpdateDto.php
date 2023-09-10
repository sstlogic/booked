<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ResourceAccessUpdateDto
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var TimeIntervalApiDto|null
     */
    public $noticeAdd;
    /**
     * @var TimeIntervalApiDto|null
     */
    public $noticeUpdate;
    /**
     * @var TimeIntervalApiDto|null
     */
    public $noticeDelete;
    /**
     * @var TimeIntervalApiDto|null
     */
    public $noticeEnd;
    /**
     * @var bool
     */
    public $requiresApproval;
    /**
     * @var bool
     */
    public $requiresCheckin;
    /**
     * @var bool
     */
    public $enableAutoExtend;
    /**
     * @var int|null
     */
    public $autoReleaseMinutes;
    /**
     * @var bool
     */
    public $allowConcurrent;
    /**
     * @var int|null
     */
    public $maxConcurrent;
    /**
     * @var bool
     */
    public $checkinLimitedToAdmins;
    /**
     * @var int|ResourceAutoReleaseAction|null
     */
    public $autoReleaseAction;
}