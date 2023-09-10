Podrobnosti rezervacije:
<br/>
<br/>

Začetek: {$StartDate->Format($dateFormat)}<br/>
Konec: {$EndDate->Format($dateFormat)}<br/>
Vir: {$ResourceName}<br/>

{if $ResourceImage}
    <div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

Nalsov: {$Title}<br/>
Opis: {nl2br($Description)}<br/>

{if count($RepeatDates) gt 0}
    <br/>
    Rezervacija je narejena za naslednje dneve:
    <br/>
{/if}

{foreach from=$RepeatDates item=date name=dates}
    {$date->Format($dateFormat)}
    <br/>
{/foreach}

{if $RequiresApproval}
    <br/>
    Eden ali več rezerviranih virov pred uporabo potrebuje potrditev. Ta rezervacija je na čakanju, dokler ni potrjena.
{/if}

<br/>
Sprejmete? <a href="{$ScriptUrl}/{$AcceptUrl}">Da</a> <a href="{$ScriptUrl}/{$DeclineUrl}">Ne</a>
<br/>

<a href="{$ScriptUrl}/{$ReservationUrl}">Ogled rezervacije</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Dodaj v Koledar (Outlook)</a> |
<a href="{$ScriptUrl}">Prijava v program Booked Scheduler</a>
