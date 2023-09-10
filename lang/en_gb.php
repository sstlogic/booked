<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once('Language.php');
require_once('en_us.php');

class en_gb extends en_us
{
    protected function _LoadDates()
    {
        $dates = parent::_LoadDates();

        // change defaults here
        $dates['general_date'] = 'd/m/Y';
        $dates['short_date'] = 'j/n/y';
        $dates['general_datetime'] = 'd/m/Y H:i:s';
        $dates['schedule_daily'] = 'l, j/m/Y';
        $dates['reservation_email'] = 'd/m/Y @ H:i (e)';
        $dates['res_popup'] = 'D, d/n H:i';
        $dates['dashboard'] = 'D, d/n H:i';
        $dates['period_time'] = "H:i";
        $dates['timepicker'] = 'H:i';
        $dates['general_date_js'] = "dd/mm/yy";
		$dates['short_datetime'] = 'j/n/y H:i';
		$dates['res_popup_time'] = 'H:i';
		$dates['short_reservation_date'] = 'j/n/y H:i';
		$dates['mobile_reservation_date'] = 'j/n H:i';
        $dates['general_time_js'] = 'H:mm';
        $dates['timepicker_js'] = 'H:i';
        $dates['momentjs_datetime'] = 'D/M/YY H:mm';
		$dates['calendar_time'] = 'H:mm';
		$dates['calendar_dates'] = 'd M';
        $dates['report_date'] = '%d/%m';
        $dates['react_date'] = 'dd/MM/yyyy'; // date-fns format
        $dates['react_time'] = 'H:mm'; // date-fns format
        $dates['react_datetime'] = 'd/M/yy H:mm'; // date-fns format
        $dates['monitor_date'] = 'j/n/y';
        $dates['monitor_time'] = 'H:i';
        $dates['monitor_event_date'] = 'j/n';
        $dates['monitor_event_time'] = 'H:i';
        $dates['sms_datetime'] = 'j/n H:i';

        $this->Dates = $dates;
        return $this->Dates;
    }
}
