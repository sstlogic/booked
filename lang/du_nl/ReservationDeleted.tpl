Reserverings Details:
<br/>
<br/>

Gebruiker: {$UserName}<br/>
Start: {$StartDate->Format($dateFormat)}<br/>
Eindingd: {$EndDate->Format($dateFormat)}<br/>
{if count($ResourceNames) > 1}
    Bronnen:
    <br/>
    {foreach from=$ResourceNames item=resourceName}
        {$resourceName}
        <br/>
    {/foreach}
{else}
    Bron: {$ResourceName}
    <br/>
{/if}

{if $ResourceImage}
    <div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

Titel: {$Title}<br/>
Beschrijving: {nl2br($Description)}<br/>
{nl2br($DeleteReason)}<br/>


{if count($RepeatDates) gt 0}
    <br/>
    De volgende data zijn verwijderd:
    <br/>
{/if}

{foreach from=$RepeatDates item=date name=dates}
    {$date->Format($dateFormat)}
    <br/>
{/foreach}

{if count($Accessories) > 0}
    <br/>
    Benodigdheden:
    <br/>
    {foreach from=$Accessories item=accessory}
        ({$accessory->QuantityReserved}) {$accessory->Name}
        <br/>
    {/foreach}
{/if}

<br/>
<br/>
<a href="{$ScriptUrl}">Inloggen Booked Scheduler</a>

