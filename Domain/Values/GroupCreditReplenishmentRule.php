<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class GroupCreditReplenishmentRuleType
{
    const NONE = 0;
    const INTERVAL = 1;
    const DAY_OF_MONTH = 2;
}

class GroupCreditReplenishmentRule
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var int
     */
    private $groupId;
    /**
     * @var GroupCreditReplenishmentRuleType
     */
    private $type;
    /**
     * @var int
     */
    private $amount;
    /**
     * @var int
     */
    private $dayOfMonth;
    /**
     * @var int
     */
    private $interval;
    /**
     * @var Date
     */
    private $lastReplenishment;

    public function __construct($id, $groupId, $type, $amount, $dayOfMonth, $interval, $lastReplenishment)
    {
        $this->id = $id;
        $this->groupId = $groupId;
        $this->type = $type;
        $this->amount = $amount;
        $this->dayOfMonth = $dayOfMonth;
        $this->interval = $interval;
        $this->lastReplenishment = $lastReplenishment;
    }

    /**
     * @return int
     */
    public function Id()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function GroupId()
    {
        return $this->groupId;
    }

    /**
     * @return GroupCreditReplenishmentRuleType|int
     */
    public function Type()
    {
        return $this->type;
    }

    /**
     * @return float
     */
    public function Amount()
    {
        return $this->amount;
    }

    /**
     * @return int
     */
    public function DayOfMonth()
    {
        return $this->dayOfMonth;
    }

    /**
     * @return int
     */
    public function Interval()
    {
        return $this->interval;
    }

    public function LastReplenishmentDate()
    {
        return $this->lastReplenishment;
    }

    /**
     * @param Date $date
     * @return bool
     */
    public function ShouldBeRunOn(Date $date)
    {
        if ($this->type == GroupCreditReplenishmentRuleType::INTERVAL) {
            if ($this->lastReplenishment->ToString() == '') {
                return true;
            }

            return $this->lastReplenishment->AddDays($this->interval)->DateEquals($date->ToUtc());
        }

        if ($this->type == GroupCreditReplenishmentRuleType::DAY_OF_MONTH) {
            return intval($this->dayOfMonth) == intval($date->DayOfMonth());
        }

        return false;
    }

    public function UpdateLastReplenishment(Date $date)
    {
        $this->lastReplenishment = $date;
    }
}