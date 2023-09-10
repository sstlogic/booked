{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
<p>{$FirstName},</p>
<p>{$ResourceName} on tällä hetkellä vapaana {$StartDate->Format($dateFormat)} ja {$EndDate->Format($dateFormat)} välillä</p>

<p>
	<a href="{$ScriptUrl}/{$ReservationUrl}">Varaa nyt</a> |
	<a href="{$ScriptUrl}">Kirjaudu sovellukseen {$AppTitle}</a>
</p>
