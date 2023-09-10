La tua prenotazione sta per iniziare.<br />
Dettagli prenotazione:
	<br />
	<br />
	Inizio: {$StartDate->Format($dateFormat)}<br />
	Fine: {$EndDate->Format($dateFormat)}<br />
	Risorsa: {$ResourceName}<br />
	Note: {$Title}<br />
	Descrizione: {nl2br($Description)}<br />
<br />
<br />
<a href="{$ScriptUrl}/{$ReservationUrl}">Vedi questa prenotazione</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Aggiungi al calendario</a> |
<a href="{$ScriptUrl}">Accedi a Booked Scheduler</a>

