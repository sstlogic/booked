{*
Copyright 2013-2017 Twinkle Toes Software, LLC
*}

Vaša rezervacija uskoro ističe.<br/>
Detalji rezervacije:
	<br/>
	<br/>
	Početak: {$StartDate->Format($dateFormat)}<br/>
	Kraj: {$EndDate->Format($dateFormat)}<br/>
	Teren: {$ResourceName}<br/>
	Naziv: {$Title}<br/>
	Opis: {nl2br($Description)}<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Pregled rezervacije</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Dodaj u kalendar</a> |
<a href="{$ScriptUrl}">Uloguj se</a>

