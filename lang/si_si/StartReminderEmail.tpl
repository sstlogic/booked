Vaša rezervacija se bo kmalu začela.<br/>
Podrobnosti rezervacije:
	<br/>
	<br/>
	Začetek: {$StartDate->Format($dateFormat)}<br/>
	Konec: {$EndDate->Format($dateFormat)}<br/>
	Vir: {$ResourceName}<br/>
	Naslov: {$Title}<br/>
	Opis: {nl2br($Description)}<br/>
<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Ogled rezervacije</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Dodaj v Koledar (Outlook)</a> |
<a href="{$ScriptUrl}">Prijava v program Booked Scheduler</a>
