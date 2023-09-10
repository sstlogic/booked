{*
Copyright 2022-2023 Twinkle Toes Software, LLC
*}
<p>Your reservation for <strong>{$ResourceName}</strong> was automatically extended because you did not check out.</p>

<p>
	<strong>Original reservation dates:</strong>
	{$OriginalStart->Format($dateFormat)} - {$OriginalEnd->Format($dateFormat)}
</p>

<p>
	<strong>New reservation dates:</strong>
	{$OriginalStart->Format($dateFormat)} - {$NewEnd->Format($dateFormat)}
</p>

<p>
	<a href="{$ScriptUrl}/{$ReservationUrl}">View this reservation</a> |
	<a href="{$ScriptUrl}">Log in to {$AppTitle}</a>
</p>
