{*
Copyright 2013-2016 Twinkle Toes Software, LLC
*}
Sinu broneeritud aeg algab varsti.<br/>
Broneeringu detailid:
	<br/>
	<br/>
	Algus: {$StartDate->Format($dateFormat)}<br/>
	Lõpp: {$EndDate->Format($dateFormat)}<br/>
	Väljak: {$ResourceName}<br/>
	Pealkiri: {$Title}<br/>
	Kirjeldus: {nl2br($Description)}
<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Vaata seda broneeringut</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Lisa kalendrisse</a> |
<a href="{$ScriptUrl}">Logi sisse Rannahalli kalendrisse</a>