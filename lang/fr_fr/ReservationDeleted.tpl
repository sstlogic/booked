{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}

Détails de la réservation :
<br/>
<br/>

Début: {$StartDate->Format($dateFormat)}<br/>
Fin: {$EndDate->Format($dateFormat)}<br/>
{if count($ResourceNames) > 1}
    Ressources:
    <br/>
    {foreach from=$ResourceNames item=resourceName}
        {$resourceName}
        <br/>
    {/foreach}
{else}
    Ressource: {$ResourceName}
    <br/>
{/if}
Titre: {$Title}<br/>
Description: {nl2br($Description)}<br/>
{nl2br($DeleteReason)}<br/>

{if count($RepeatDates) gt 0}
    <br/>
    Les dates suivantes ont été effacées:
    <br/>
{/if}

{foreach from=$RepeatDates item=date name=dates}
    {$date->Format($dateFormat)}
    <br/>
{/foreach}

{if count($Accessories) > 0}
    <br/>
    Accessoires:
    <br/>
    {foreach from=$Accessories item=accessory}
        ({$accessory->QuantityReserved}) {$accessory->Name}
        <br/>
    {/foreach}
{/if}

<a href="{$ScriptUrl}">Connexion à Booked Scheduler</a>


