<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Controls/Control.php');

class RecurrenceControl extends Control
{
	public function PageLoad()
	{
        $prefix = $this->Get('prefix');
        $this->Set('prefix', isset($prefix) ? $prefix : '');

        $repeatTerminationDate = $this->Get('RepeatTerminationDate');
        $this->Set('RepeatTerminationDate', isset($repeatTerminationDate) ? $repeatTerminationDate : '');


		$this->Set('RepeatEveryOptions', range(1, 20));
		$this->Set('RepeatOptions', array (
						'none' => array('key' => 'DoesNotRepeat', 'everyKey' => ''),
						'daily' => array('key' => 'Daily', 'everyKey' => 'days'),
						'weekly' => array('key' => 'Weekly', 'everyKey' => 'weeks'),
						'monthly' => array('key' => 'Monthly', 'everyKey' => 'months'),
						'yearly' => array('key' => 'Yearly', 'everyKey' => 'years'),
						'custom' => array('key' => 'Custom', 'everyKey' => 'custom'),
								)
		);
		$this->Set('DayNames', array(
								0 => 'DaySundayAbbr',
								1 => 'DayMondayAbbr',
								2 => 'DayTuesdayAbbr',
								3 => 'DayWednesdayAbbr',
								4 => 'DayThursdayAbbr',
								5 => 'DayFridayAbbr',
								6 => 'DaySaturdayAbbr',
								)
		);

		$this->Display('Controls/RecurrenceDiv.tpl');
	}
}