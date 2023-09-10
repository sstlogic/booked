<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Controls/Control.php');

class TimePickerControl extends Control
{
    public function PageLoad()
    {
        $this->SetDefault('Id', 'timepicker-range');
        $this->SetDefault('StartInputId', 'startTime');
        $this->SetDefault('EndInputId', 'endTime');
        $this->SetDefault('StartInputFormName', FormKeys::BEGIN_TIME);
        $this->SetDefault('EndInputFormName', FormKeys::END_TIME);
        $this->SetDefault('Start', Date::Now());
        $this->SetDefault('End', Date::Now()->AddMinutes(30));
        $format = Resources::GetInstance()->GetDateFormat('timepicker');
        $this->SetDefault('TimeFormat', $format);

        $times = [];
        for($i = 0; $i < 24; $i++) {
            $times[] = new Time($i, 00);
            $times[] = new Time($i, 30);
        }
        $this->Set('times', $times);

        $this->Display('Controls/TimePicker.tpl');
    }

    private function SetDefault($key, $value)
    {
        $item = $this->Get($key);
        if ($item == null)
        {
            $this->Set($key, $value);
        }
    }
}