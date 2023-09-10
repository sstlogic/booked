Din reservation starter snart.<br/>
Oplysninger om reservation:
	<br/>
	<br/>
	Begynder: {$StartDate->Format($dateFormat)}<br/>
	Slutter: {$EndDate->Format($dateFormat)}<br/>
	Facilitet: {$ResourceName}<br/>
	Overskrift: {$Title}<br/>
	Beskrivelse: {nl2br($Description)}
<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Se denne reservation</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Tilføj til kalender</a> |
<a href="{$ScriptUrl}">Log på {$AppTitle}</a>
