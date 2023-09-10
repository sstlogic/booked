{*
Copyright 2013-2023 Twinkle Toes Software, LLC
*}
Ismétlődő foglalási sorozata {$ResourceName} hamarosan befejeződik {$StartDate->Format($dateFormat)}.<br/>
Fogalás részletei:
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
<a href="{$ScriptUrl}">Bejelentkezés ide: {$AppTitle}</a>
