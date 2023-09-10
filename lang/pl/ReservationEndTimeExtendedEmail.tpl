{*
Copyright 2022 Twinkle Toes Software, LLC
*}
<p>Twoja rezerwacja <strong>{$ResourceName}</strong> została automatycznie wydłużona, ponieważ nie wymeldowałeś/-aś się.</p>

<p>
	<strong>ORyginalne terminy rezerwacji:</strong>
	{$OriginalStart->Format($dateFormat)} - {$OriginalEnd->Format($dateFormat)}
</p>

<p>
	<strong>Nowe terminy rezerwacji:</strong>
	{$OriginalStart->Format($dateFormat)} - {$NewEnd->Format($dateFormat)}
</p>

<p>
	<a href="{$ScriptUrl}/{$ReservationUrl}">Zobacz tę rezerwację</a> |
	<a href="{$ScriptUrl}">Zaloguj się do {$AppTitle}</a>
</p>