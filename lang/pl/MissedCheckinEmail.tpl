<p>Nie zameldowałeś/-aś się w wyznaczonym terminie.</p>
<p><strong>Szczegóły rezerwacji:</strong></p>
<p>
	<strong>Początek:</strong> {$StartDate->Format($dateFormat)}<br/>
	<strong>Koniec:</strong> {$EndDate->Format($dateFormat)}<br/>
	<strong>Zasób:</strong> {$ResourceName}<br/>
	<strong>Tytuł:</strong> {$Title}<br/>
	<strong>Opis:</strong> {$Description|nl2br}
</p>

{if $IsAutoRelease}
	<p>Jeśli się nie zameldujesz, ta rezerwacja zostanie automatycznie anulowana w terminie: {$AutoReleaseTime->Format($dateFormat)}</p>
{/if}

<p>
	<a href="{$ScriptUrl}/{$ReservationUrl}">Zobacz rezerwację</a> |
	<a href="{$ScriptUrl}">Zaloguj się do {$AppTitle}</a>
</p>