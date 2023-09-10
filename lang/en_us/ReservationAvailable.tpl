{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
<p>{$FirstName},</p>
<p>{$ResourceName} is currently available between {$StartDate->Format($dateFormat)} and {$EndDate->Format($dateFormat)}</p>

<p>
	<a href="{$ScriptUrl}/{$ReservationUrl}">Book now</a> |
	<a href="{$ScriptUrl}">Log in to {$AppTitle}</a>
</p>
