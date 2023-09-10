Podrobnosti rezervacije:
<br/>
<br/>

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

{if count($RepeatDates) gt 0}
    <br/>
    Rezervacija je narejena za naslednje dneve:
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
    Eden ali več rezerviranih virov pred uporabo potrebuje potrditev. Ta rezervacija je na čakanju, dokler ni potrjena.
{/if}

{if !empty($ApprovedBy)}
    <br/>
    Rezervacijo odobril: {$ApprovedBy}
{/if}

<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Ogled rezervacije</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Dodaj v Koledar (Outlook)</a> |
<a href="{$ScriptUrl}">Prijava v program Booked Scheduler</a>
	