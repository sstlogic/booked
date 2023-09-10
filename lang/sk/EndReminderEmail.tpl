{*
Copyright 2013-2023 Twinkle Toes Software, LLC
*}

Vaša rezervácia čoskoro skončí.<br/>
Podrobnosti rezervácie:
	<br/>
	<br/>
	Začiatok: {$StartDate->Format($dateFormat)}<br/>
	Koniec: {$EndDate->Format($dateFormat)}<br/>
	Ihrisko: {$ResourceName}<br/>
	Názov: {$Title}<br/>
	Popis: {nl2br($Description)}<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Zobraziť túto rezerváciu</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Pridať do kalendára</a> |
<a href="{$ScriptUrl}">Prihláste sa</a>

