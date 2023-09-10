{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}

Reservierungsdetails:
<br/>
<br/>

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

{if count($RepeatDates) gt 0}
    <br/>
    Die Reservierung gilt für den/die folgenden Tag(e):
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

{if $RequiresApproval}
    <br/>
    Eine oder mehrere Ressourcen benötigen eine Genehmigung.
    Diese Reservierung wird zurückgehalten, bis sie genehmigt ist.
{/if}

<br/>
Möchten Sie teilnehmen? <a href="{$ScriptUrl}/{$AcceptUrl}">Ja</a> <a href="{$ScriptUrl}/{$DeclineUrl}">Nein</a>
<br/>
<br/>

<a href="{$ScriptUrl}/{$ReservationUrl}">Reservierung ansehen</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Zum Kalender hinzufügen</a> |
<a href="{$ScriptUrl}">Anmelden bei Booked Scheduler</a>
	
