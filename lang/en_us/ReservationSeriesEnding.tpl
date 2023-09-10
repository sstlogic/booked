{*
Copyright 2013-2023 Twinkle Toes Software, LLC
*}
<p>Your recurring reservation series for {$ResourceName} is ending on {$StartDate->Format($dateFormat)}.</p>
<p><strong>Reservation Details:</strong></p>
<p>
	<strong>Start:</strong> {$StartDate->Format($dateFormat)}<br/>
	<strong>End:</strong> {$EndDate->Format($dateFormat)}<br/>
	<strong>Resource:</strong> {$ResourceName}<br/>
	<strong>Title:</strong> {$Title}<br/>
	<strong>Description:</strong> {nl2br($Description)}
</p>

<p>
	<a href="{$ScriptUrl}/{$ReservationUrl}">View this reservation</a> |
	<a href="{$ScriptUrl}">Log in to {$AppTitle}</a>
</p>