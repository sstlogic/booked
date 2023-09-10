{$OwnerName} missed their check in on {$ResourceName}.<br/>
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
    This reservation will be automatically cancelled at {$AutoReleaseTime->Format($dateFormat)}
{/if}
<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">View this reservation</a> |
<a href="{$ScriptUrl}">Log in to {$AppTitle}</a>