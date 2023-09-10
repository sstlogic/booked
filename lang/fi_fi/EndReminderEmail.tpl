{*
Copyright 2013-2023 Twinkle Toes Software, LLC
*}
<p>Varauksesi päättyy pian.</p>
<p><strong>Varauksen tiedot:</strong></p>

<p>
	<strong>Alkaa:</strong> {$StartDate->Format($dateFormat)}<br/>
	<strong>Päättyy:</strong> {$EndDate->Format($dateFormat)}<br/>
	<strong>Resurssi:</strong> {$ResourceName}<br/>
	<strong>Otsikko:</strong> {$Title}<br/>
	<strong>Kuvaus:</strong> {nl2br($Description)}
</p>

<p>
	<a href="{$ScriptUrl}/{$ReservationUrl}">Näytä varaus</a> |
	<a href="{$ScriptUrl}/{$ICalUrl}">Lisää kalenteriin</a> |
	<a href="{$ScriptUrl}">Kirjaudu sovellukseen {$AppTitle}</a>
</p>
