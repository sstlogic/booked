{*
Copyright 2013-2023 Twinkle Toes Software, LLC
*}
<p>Unohdit ilmoittautumisen.</p>
<p><strong>Varauksen tiedot:</strong></p>
<p>
	<strong>Alkaa:</strong> {$StartDate->Format($dateFormat)}<br/>
	<strong>Päättyy:</strong> {$EndDate->Format($dateFormat)}<br/>
	<strong>Resurssi:</strong> {$ResourceName}<br/>
	<strong>Otsikko:</strong> {$Title}<br/>
	<strong>Kuvaus:</strong> {nl2br($Description)}
</p>

{if $IsAutoRelease}
	<p>Jos et ilmoittaudu, tämä varaus perutaan {$AutoReleaseTime->Format($dateFormat)}</p>
{/if}

<p>
	<a href="{$ScriptUrl}/{$ReservationUrl}">Näytä varaus</a> |
	<a href="{$ScriptUrl}">Kirjaudu sovellukseen {$AppTitle}</a>
</p>
