Bokningsdetaljer:
<br/>
<br/>

Er tid börjar: {$StartDate->Format($dateFormat)}<br/>
Välkommen till oss 10min innan bokad tid.<br/>

Slutar: {$EndDate->Format($dateFormat)}<br/>
<br/>
Bokning: {$ResourceName}<br/>

{if $ResourceImage}
    <div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

<br/>
Rubrik: {$Title}<br/>
<br/>
Beskrivning: {nl2br($Description)}<br/>

{if count($RepeatDates) gt 0}
    <br/>
    Ni har reserverat följande tid / er:
    <br/>
{/if}

{foreach from=$RepeatDates item=date name=dates}
    {$date->Format($dateFormat)}
    <br/>
{/foreach}

{if $RequiresApproval}
    <br/>
    Innan er reservation övergår i bokning behöver den godkännas först.
{/if}

<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Visa Bokning</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Lägg till i Outlook</a> |
<a href="{$ScriptUrl}">Logga in i Bokningsprogrammet</a>

