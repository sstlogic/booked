Podrobnosti rezervacije:
<br/>
<br/>

Uporabnik: {$UserName}<br/>
Začetek: {$StartDate->Format($dateFormat)}<br/>
Konec: {$EndDate->Format($dateFormat)}<br/>
{if count($ResourceNames) > 1}
    Viri:
    <br/>
    {foreach from=$ResourceNames item=resourceName}
        {$resourceName}
        <br/>
    {/foreach}
{else}
    Vir: {$ResourceName}
    <br/>
{/if}

{if $ResourceImage}
    <div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

Naslov: {$Title}<br/>
Opis: {nl2br($Description)}<br/>
{nl2br($DeleteReason)}<br/>


{if count($RepeatDates) gt 0}
    <br/>
    Naslednji datumi so bili odstranjeni:
    <br/>
{/if}

{foreach from=$RepeatDates item=date name=dates}
    {$date->Format($dateFormat)}
    <br/>
{/foreach}

{if count($Accessories) > 0}
    <br/>
    Dodatki:
    <br/>
    {foreach from=$Accessories item=accessory}
        ({$accessory->QuantityReserved}) {$accessory->Name}
        <br/>
    {/foreach}
{/if}

<br/>
<br/>
<a href="{$ScriptUrl}">Prijava v program Booked Scheduler</a>
	