{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}


Detalji o rezervaciji:
<br/>
<br/>

Korisnik: {$UserName}<br/>
Pocetak: {$StartDate->Format($dateFormat)}<br/>
Kraj: {$EndDate->Format($dateFormat)}<br/>
{if count($ResourceNames) > 1}
    Tereni:
    <br/>
    {foreach from=$ResourceNames item=resourceName}
        {$resourceName}
        <br/>
    {/foreach}
{else}
    Tereni: {$ResourceName}
    <br/>
{/if}

{if $ResourceImage}
    <div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

Naziv: {$Title}<br/>
Opis: {nl2br($Description)}<br/>
{nl2br($DeleteReason)}<br/>


{if count($RepeatDates) gt 0}
    <br/>
    Navedeni datumi su obrisani:
    <br/>
{/if}

{foreach from=$RepeatDates item=date name=dates}
    {$date->Format($dateFormat)}
    <br/>
{/foreach}

{if count($Accessories) > 0}
    <br/>
    Dodatno:
    <br/>
    {foreach from=$Accessories item=accessory}
        ({$accessory->QuantityReserved}) {$accessory->Name}
        <br/>
    {/foreach}
{/if}
<br/>
<a href="{$ScriptUrl}">Ulogiraj se</a>

