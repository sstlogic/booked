<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/WebService/namespace.php');

class RecurrenceRequestResponse
{
    public $type;
    public $interval;
    public $monthlyType;
    public $weekdays = [];
    public $repeatTerminationDate;
    public $repeatDates = [];

    public function __construct($type, $interval, $monthlyType, $weekdays, $repeatTerminationDate, $repeatDates)
    {
        $this->type = $type;
        $this->interval = $interval;
        $this->monthlyType = $monthlyType;
        $this->weekdays = $weekdays;
        $this->repeatTerminationDate = $repeatTerminationDate;
        $this->repeatDates = $repeatDates;
    }

    public static function Example()
    {
        return new ExampleRecurrenceRequestResponse();
    }

    /**
     * @return RecurrenceRequestResponse
     */
    public static function Null()
    {
        return new RecurrenceRequestResponse(RepeatType::None, null, null, array(), null, []);
    }
}

class ExampleRecurrenceRequestResponse extends RecurrenceRequestResponse
{
    public function __construct()
    {
        $this->interval = 3;
        $this->monthlyType = RepeatMonthlyType::DayOfMonth . '|' . RepeatMonthlyType::DayOfWeek . '|null';
        $this->type = RepeatType::Daily . '|' . RepeatType::Monthly . '|' . RepeatType::None . '|' . RepeatType::Weekly . '|' . RepeatType::Yearly;
        $this->weekdays = array(0, 1, 2, 3, 4, 5, 6);
        $this->repeatTerminationDate = Date::Now()->ToIso();
        $this->repeatDates = [Date::Now()->ToIso()];
    }
}

