{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
Detalles de la Reserva:
<br/>
<br/>

Usuario: {$UserName}<br/>
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
{nl2br($DeleteReason)}<br/>


{if count($RepeatDates) gt 0}
    <br/>
    Se han eliminado las siguientes fechas:
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

<br/>
<br/>
<a href="{$ScriptUrl}">Iniciar sesión en Booked Scheduler</a>