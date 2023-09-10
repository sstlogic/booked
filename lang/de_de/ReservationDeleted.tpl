{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}


Reservierungsdetails:
<br/>
<br/>

Benutzer: {$UserName}
Beginn: {$StartDate->Format($dateFormat)}<br/>
Ende: {$EndDate->Format($dateFormat)}<br/>
{if count($ResourceNames) > 1}
    Ressourcen:
    <br/>
    {foreach from=$ResourceNames item=resourceName}
        {$resourceName}
        <br/>
    {/foreach}
{else}
    Ressource: {$ResourceName}
    <br/>
{/if}

{if $ResourceImage}
    <div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

Titel: {$Title}<br/>
Beschreibung: {nl2br($Description)}<br/>
{nl2br($DeleteReason)}<br/>


{if count($RepeatDates) gt 0}
    <br/>
    Die folgenden Reservierungen wurden entfernt:
    <br/>
{/if}

{foreach from=$RepeatDates item=date name=dates}
    {$date->Format($dateFormat)}
    <br/>
{/foreach}

{if count($Accessories) > 0}
    <br/>
    Zubehör:
    <br/>
    {foreach from=$Accessories item=accessory}
        ({$accessory->QuantityReserved}) {$accessory->Name}
        <br/>
    {/foreach}
{/if}
<br/>
<a href="{$ScriptUrl}">Anmelden bei Booked Scheduler</a>
	
