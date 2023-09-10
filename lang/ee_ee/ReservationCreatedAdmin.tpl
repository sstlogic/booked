{*
Copyright 2011-2015 Twinkle Toes Software, LLC
*}
Broneeringu detailid:
<br/>
<br/>

Kasutaja: {$UserName}<br/>
{if !empty($CreatedBy)}
    Broneerija: {$CreatedBy}
    <br/>
{/if}
Algus: {$StartDate->Format($dateFormat)}<br/>
Lõpp: {$EndDate->Format($dateFormat)}<br/>
{if count($ResourceNames) > 1}
    Väljakud:
    <br/>
    {foreach from=$ResourceNames item=resourceName}
        {$resourceName}
        <br/>
    {/foreach}
{else}
    Väljak: {$ResourceName}
    <br/>
{/if}

{if $ResourceImage}
    <div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

Pealkiri: {$Title}<br/>
Kirjeldus: {nl2br($Description)}

{if count($RepeatDates) gt 0}
    <br/>
    Broneering esineb järgneval kuupäeval:
    <br/>
{/if}

{foreach from=$RepeatDates item=date name=dates}
    {$date->Format($dateFormat)}
    <br/>
{/foreach}

{if count($Accessories) > 0}
    <br/>
    Accessories:
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
    One or more of the resources reserved require approval before usage.  Please ensure that this reservation request is approved or rejected.
{/if}

<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Vaata seda broneeringut</a> | <a href="{$ScriptUrl}">Logi sisse Rannahalli
    kalendrisse</a>