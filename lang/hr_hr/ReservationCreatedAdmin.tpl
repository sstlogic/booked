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
Opis: {$Description}<br/>

{if count($RepeatDates) gt 0}
    <br/>
    Rezevacija va�i za navedeni datum:
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

{if count($Attributes) > 0}
    <br/>
    {foreach from=$Attributes item=attribute}
        <div>{control type="AttributeControl" attribute=$attribute readonly=true}</div>
    {/foreach}
{/if}

{if $RequiresApproval}
    <br/>
    Jedan ili vi�e terena zahtijevaju odobrenje prije upotrebe. Molimo provjerite da li je rezervacija prihvacena ili odbijena.
{/if}

<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">View this reservation</a> | <a href="{$ScriptUrl}">Ulogiraj se</a>

