Dettagli prenotazione:
<br/>
<br/>

Utente: {$UserName}<br/>
{if !empty($CreatedBy)}
    Creato da: {$CreatedBy}
    <br/>
{/if}
Inizio: {$StartDate->Format($dateFormat)}<br/>
Fine: {$EndDate->Format($dateFormat)}<br/>
{if count($ResourceNames) > 1}
    Risorse:
    <br/>
    {foreach from=$ResourceNames item=resourceName}
        {$resourceName}
        <br/>
    {/foreach}
{else}
    Risorsa: {$ResourceName}
    <br/>
{/if}

{if $ResourceImage}
    <div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

Note: {$Title}<br/>
Descrizione: {$Description}<br/>

{if count($RepeatDates) gt 0}
    <br/>
    La prenotazione si ripete nelle seguenti date:
    <br/>
{/if}

{foreach from=$RepeatDates item=date name=dates}
    {$date->Format($dateFormat)}
    <br/>
{/foreach}

{if count($Accessories) > 0}
    <br/>
    Accessori:
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
    E' stata inserita una nuova prenotazione. Rimarrà in sospeso fino all'approvazione.
{/if}

<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Vedi questa prenotazione</a> | <a href="{$ScriptUrl}">Accedi a Booked
    Scheduler</a>

