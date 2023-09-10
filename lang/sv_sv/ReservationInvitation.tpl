Bokningsdetaljer:
<br/>
<br/>

Bokningen börjar: {$StartDate->Format($dateFormat)}<br/>
Bokningen slutar: {$EndDate->Format($dateFormat)}<br/>
Bokning: {$ResourceName}<br/>

{if $ResourceImage}
    <div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

Rubrik: {$Title}<br/>
Beskrivning: {nl2br($Description)}<br/>

{if count($RepeatDates) gt 0}
    <br/>
    Bokning har gjorts följande datum:
    <br/>
{/if}

{foreach from=$RepeatDates item=date name=dates}
    {$date->Format($dateFormat)}
    <br/>
{/foreach}

{if $RequiresApproval}
    <br/>
    Denna bokning behöver godkännas innan den börjar gälla.  Denna bokning är reserverad tills den är godkänd.
{/if}

<br/>
Deltar? <a href="{$ScriptUrl}/{$AcceptUrl}">Ja</a> <a href="{$ScriptUrl}/{$DeclineUrl}">Nej</a>
<br/>

<a href="{$ScriptUrl}/{$ReservationUrl}">Visa denna bokning</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Lägg till i Outlook</a> |
<a href="{$ScriptUrl}">Logga in i Bokningsprogrammet</a>

