{$OwnerName} jätti ilmoittautumatta resurssiin {$ResourceName}.<br/>
Varauksen tiedot:
<br/>
<br/>
Alkaa: {$StartDate->Format($dateFormat)}<br/>
Päättyy: {$EndDate->Format($dateFormat)}<br/>
Resurssi: {$ResourceName}<br/>
Otsikko: {$Title}<br/>
Kuvaus: {nl2br($Description)}
{if $IsAutoRelease}
    <br/>
    Tämä varaus perutaan automaattisesti {$AutoReleaseTime->Format($dateFormat)}
{/if}
<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Näytä varaus</a> |
<a href="{$ScriptUrl}">Kirjaudu sovellukseen {$AppTitle}</a>