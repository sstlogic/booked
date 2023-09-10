{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
Detalles de la Reserva:
<br/>
<br/>

Inicio: {$StartDate->Format($dateFormat)}<br/>
Fin: {$EndDate->Format($dateFormat)}<br/>
{if count($ResourceNames) > 1}
    Recursos:
    <br/>
    {foreach from=$ResourceNames item=resourceName}
        {$resourceName}
        <br/>
    {/foreach}
{else}
    Recurso: {$ResourceName}
    <br/>
{/if}

{if $ResourceImage}
    <div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

Título: {$Title}<br/>
Descripción: {nl2br($Description)}<br/>

{if count($RepeatDates) gt 0}
    <br/>
    La reserva ocurren en las siguientes fechas:
    <br/>
{/if}

{foreach from=$RepeatDates item=date name=dates}
    {$date->Format($dateFormat)}
    <br/>
{/foreach}

{if count($Accessories) > 0}
    <br/>
    Accesorios:
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
    Uno o más recursos reservados requiere aprobación antes de su uso. Esta reserva quedará pendiente hasta que se apruebe.
{/if}

{if !empty($ApprovedBy)}
    <br/>
    Aprobada por: {$ApprovedBy}
{/if}

<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Ver esta reserva</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Agregar a un calendario</a> |
<a href="{$ScriptUrl}">Iniciar sesión en Booked Scheduler</a>