<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/


class CalendarTypes
{
	const Month = 'dayGridMonth';
	const Week = 'timeGridWeek';
	const Day = 'timeGridDay';
}

interface ICalendarFactory
{
	/**
	 * @abstract
	 * @param $type
	 * @param $year
	 * @param $month
	 * @param $day
	 * @param $timezone
	 * @return ICalendarSegment
	 */
	public function Create($type, $year, $month, $day, $timezone);
}

class CalendarFactory implements ICalendarFactory
{
	public function Create($type, $year, $month, $day, $timezone)
	{
		if ($type == CalendarTypes::Day)
		{
			return new CalendarDay(Date::Create($year, $month, $day, 0, 0, 0, $timezone));
		}

		if ($type == CalendarTypes::Week)
		{
			return CalendarWeek::FromDate($year, $month, $day, $timezone);
		}

		return new CalendarMonth($month, $year, $timezone);
	}
}
