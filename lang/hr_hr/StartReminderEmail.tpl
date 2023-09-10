{*
Copyright 2013-2023 Twinkle Toes Software, LLC
*}

Va�a rezervacija uskoro pocinje.<br/>
Detalji o rezervaciji:
	<br/>
	<br/>
	Pocetak: {$StartDate->Format($dateFormat)}<br/>
	Kraj: {$EndDate->Format($dateFormat)}<br/>
	Teren: {$ResourceName}<br/>
	Naslov: {$Title}<br/>
	Opis: {nl2br($Description)}<br/>
<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Pregledaj rezervaciju</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Dodaj u kalendar</a> |
<a href="{$ScriptUrl}">Ulogiraj se</a>

