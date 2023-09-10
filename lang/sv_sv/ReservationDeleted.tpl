Bokningsdetaljer:
<br/>
<br/>

Er bokning börjar: {$StartDate->Format($dateFormat)}<br/>
Er bokning slutar: {$EndDate->Format($dateFormat)}<br/>
Bokning: {$ResourceName}<br/>

{if $ResourceImage}
    <div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

Rubrik: {$Title}<br/>
Beskrivning: {nl2br($Description)}<br/>
{nl2br($DeleteReason)}<br/>


{if count($RepeatDates) gt 0}
    <br/>
    Följande bokning har ångrats:
    <br/>
{/if}

{foreach from=$RepeatDates item=date name=dates}
    {$date->Format($dateFormat)}
    <br/>
{/foreach}

<a href="{$ScriptUrl}">Logga in i Bokningsprogrammet</a>

