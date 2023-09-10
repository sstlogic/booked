{*
Copyright 2013-2023 Twinkle Toes Software, LLC
*}
You missed your check in.<br/>
Reservation Details:
	<br/>
	<br/>
	Start: {$StartDate->Format($dateFormat)}<br/>
	End: {$EndDate->Format($dateFormat)}<br/>
	Resource: {$ResourceName}<br/>
	Title: {$Title}<br/>
	Description: {nl2br($Description)}
    {if $IsAutoRelease}
        <br/>
        If you do not check in, this reservation will be automatically cancelled at {$AutoReleaseTime->Format($dateFormat)}
    {/if}
<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">View this reservation</a> |
<a href="{$ScriptUrl}">Log in to Booked Scheduler</a>