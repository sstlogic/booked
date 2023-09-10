<p>Nie wymeldowałeś/-aś sie z rezerwacji.</p>
<p><strong>Szczegóły rezerwacji:</strong></p>
<p>
	<strong>Początek:</strong> {$StartDate->Format($dateFormat)}<br/>
	<strong>Koniec:</strong> {$EndDate->Format($dateFormat)}<br/>
	<strong>Zasób:</strong> {$ResourceName}<br/>
	<strong>Tytuł:</strong> {$Title}<br/>
	<strong>Opis:</strong> {$Description|nl2br}
</p>

<p>
	<a href="{$ScriptUrl}/{$ReservationUrl}">Zobacz rezerwację</a> |
	<a href="{$ScriptUrl}">Zaloguj się do {$AppTitle}</a>
</p>