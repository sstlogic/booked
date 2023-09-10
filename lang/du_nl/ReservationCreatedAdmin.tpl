Reserverings Details:
<br/>
<br/>

Gebruiker: {$UserName}<br/>
Start: {$StartDate->Format($dateFormat)}<br/>
Einde: {$EndDate->Format($dateFormat)}<br/>
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
Beschrijving: {$Description}<br/>

{if count($RepeatDates) gt 0}
    <br/>
    De reservering komt voor op de volgende data:
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

{if count($Attributes) > 0}
    <br/>
    {foreach from=$Attributes item=attribute}
        <div>{control type="AttributeControl" attribute=$attribute readonly=true}</div>
    {/foreach}
{/if}

{if $RequiresApproval}
    <br/>
    Eén of meerdere bronnen die gereserveerd zijn hebben goedkeuring nodig voordat ze gebruikt kunnen worden. Accepteer of wijs de reservering af.
{/if}

<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Bekijk deze reservering</a> | <a href="{$ScriptUrl}">Inloggen Booked
    Scheduler</a>

