Du har ikke tjekket ind på din reservation.<br/>
	<br/>
	<br/>
	Starttidspunkt: {$StartDate->Format($dateFormat)}<br/>
	Sluttidspunkt: {$EndDate->Format($dateFormat)}<br/>
	Facilitet: {$ResourceName}<br/>
  Overskrift: {$Title}<br/>
	Beskrivelse: {nl2br($Description)}
    {if $IsAutoRelease}
        <br/>
        Hvis du ikke tjekker ind, bliver reservationen automatisk slettet den {$AutoReleaseTime->Format($dateFormat)}
    {/if}
<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Se denne reservation</a> |
<a href="{$ScriptUrl}">Log på {$AppTitle}</a>
