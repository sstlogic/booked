{*
Copyright 2013-2023 Twinkle Toes Software, LLC
*}
<p>You missed your check in time.</p>
<p><strong>Reservation Details:</strong></p>
<p>
	<strong>Start:</strong> {$StartDate->Format($dateFormat)}<br/>
	<strong>End:</strong> {$EndDate->Format($dateFormat)}<br/>
	<strong>Resource:</strong> {$ResourceName}<br/>
	<strong>Title:</strong> {$Title}<br/>
	<strong>Description:</strong> {nl2br($Description)}
</p>

{if $IsAutoRelease}
	<p>If you do not check in, this reservation will be automatically cancelled at {$AutoReleaseTime->Format($dateFormat)}</p>
{/if}

<p>
	<a href="{$ScriptUrl}/{$ReservationUrl}">View this reservation</a> |
	<a href="{$ScriptUrl}">Log in to {$AppTitle}</a>
</p>
