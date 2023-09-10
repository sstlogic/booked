Je reservering eindigd zo.<br/>
Reserverings Details:
	<br/>
	<br/>
	Start: {$StartDate->Format($dateFormat)}<br/>
	Einde: {$EndDate->Format($dateFormat)}<br/>
	Bron: {$ResourceName}<br/>
	Titel: {$Title}<br/>
	Beschrijving: {nl2br($Description)}<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Bekijk deze reservering</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Voeg toe aan agenda</a> |
<a href="{$ScriptUrl}">Inloggen Booked Scheduler</a>