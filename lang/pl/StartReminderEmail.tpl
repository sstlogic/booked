<p>Twoja rezerwacja zacznie sie niedługo.</p>
<p><strong>Szczegóły rezerwacji:</strong></p>
<p>
	<strong>Początek:</strong> {$StartDate->Format($dateFormat)}<br/>
	<strong>Koniec:</strong> {$EndDate->Format($dateFormat)}<br/>
	<strong>Zasób:</strong> {$ResourceName}<br/>
	<strong>Tytuł:</strong> {$Title}<br/>
	<strong>DOpis:</strong> {$Description|nl2br}
</p>

<p>
	<a href="{$ScriptUrl}/{$ReservationUrl}">Zobacz tę rezerwację</a> |
	<a href="{$ScriptUrl}/{$ICalUrl}">Dodaj do Outlook'a</a> |
	<a href="{$ScriptUrl}">Zaloguj się do {$AppTitle}</a>
</p>