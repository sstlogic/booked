Fogalása hamarosan kezdődik.<br/>
A foglalás részletei:
	<br/>
	<br/>
	Kezdés: {$StartDate->Format($dateFormat)}<br/>
	Befejezés: {$EndDate->Format($dateFormat)}<br/>
	Elem: {$ResourceName}<br/>
	Megnevezés: {$Title}<br/>
	Leírás: {nl2br($Description)}
<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">A foglalás megtekintése</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Naptárhoz adás</a> |
<a href="{$ScriptUrl}">Bejelentkezés ide: {$AppTitle}</a>
