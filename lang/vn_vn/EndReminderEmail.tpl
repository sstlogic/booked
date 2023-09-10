{*
Copyright 2013-2023 Twinkle Toes Software, LLC
*}
Your reservation is ending soon.<br/>
Reservation Details:
	<br/>
	<br/>
	Start: {$StartDate->Format($dateFormat)}<br/>
	End: {$EndDate->Format($dateFormat)}<br/>
	Resource: {$ResourceName}<br/>
	Title: {$Title}<br/>
	Description: {nl2br($Description)}
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">View this reservation</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Add to Calendar</a> |
<a href="{$ScriptUrl}">Log in to Booked Scheduler</a>