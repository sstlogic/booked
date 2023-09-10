{*
Copyright 2011-2016 Twinkle Toes Software, LLC
*}
Broneeringu detailid:
<br/>
<br/>

Kasutaja: {$UserName}<br/>
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
{nl2br($DeleteReason)}<br/>


{if count($RepeatDates) gt 0}
    <br/>
    Järgnevad ajad on tühistatud:
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

<br/>
<br/>
<a href="{$ScriptUrl}">Logi sisse Rannahalli kalendrisse</a>