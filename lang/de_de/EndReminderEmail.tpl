{*
Copyright 2013-2023 Twinkle Toes Software, LLC
*}

Ihre Reservierung endet bald.<br/>
Reservierungsdetails:
	<br/>
	<br/>
	Start: {$StartDate->Format($dateFormat)}<br/>
	Ende: {$EndDate->Format($dateFormat)}<br/>
	Ressource: {$ResourceName}<br/>
	Titel: {$Title}<br/>
	Beschreibung: {nl2br($Description)}<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Reservierung ansehen</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Zum Kalender hinzufügen</a> |
<a href="{$ScriptUrl}">Anmelden bei Booked Schedule</a>

